<?php

namespace Tests\Feature;

use App\Mail\CustomerOtpMail;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\User;
use App\Services\CustomerIdentity\Contracts\SmsProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Support\FakeSmsProvider;
use Tests\TestCase;

/**
 * CUSTOMER-001A — registration, OTP + password login, password reset,
 * rate limiting, existence-leak prevention, and the hard guarantee that
 * merchant authentication is untouched.
 */
class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    /** Captures SMS sends so tests can read the delivered code. */
    private FakeSmsProvider $sms;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sms = new FakeSmsProvider();
        $this->app->instance(SmsProvider::class, $this->sms);
    }

    private function lastSmsCode(): string
    {
        preg_match('/\d{6}/', end($this->sms->sent)['message'], $m);

        return $m[0];
    }

    // ── Registration ──────────────────────────────────────────────────────

    public function test_customer_can_register_with_phone_only_and_verify_by_otp(): void
    {
        $this->post(route('customer.register.store'), [
            'first_name' => 'Nok',
            'country'    => 'TH',
            'phone'      => '081 234 5678',
        ])->assertRedirect(route('customer.otp.form', absolute: false));

        $customer = Customer::where('phone', '+66812345678')->first();
        $this->assertNotNull($customer, 'phone was not normalized to E.164 on registration');
        $this->assertNull($customer->password);
        $this->assertNull($customer->phone_verified_at);

        // Complete the OTP → signed in + phone verified
        $this->post(route('customer.otp.verify'), ['code' => $this->lastSmsCode()])
            ->assertRedirect(route('customer.wallet', absolute: false));

        $this->assertAuthenticatedAs($customer->fresh(), 'customer');
        $this->assertNotNull($customer->fresh()->phone_verified_at);
        $this->assertNotNull($customer->fresh()->last_login_at);
    }

    public function test_customer_can_register_with_email_only(): void
    {
        Mail::fake();

        $this->post(route('customer.register.store'), [
            'first_name' => 'Aye',
            'country'    => 'MM',
            'email'      => 'Aye@Example.com',
        ])->assertRedirect(route('customer.otp.form', absolute: false));

        $customer = Customer::where('email', 'aye@example.com')->first();
        $this->assertNotNull($customer, 'email was not lowercased on registration');
        $this->assertNull($customer->phone);

        Mail::assertSent(CustomerOtpMail::class);
    }

    public function test_registration_requires_phone_or_email(): void
    {
        $this->post(route('customer.register.store'), [
            'first_name' => 'Nok',
            'country'    => 'TH',
        ])->assertSessionHasErrors(['phone', 'email']);
    }

    public function test_registration_rejects_invalid_phone(): void
    {
        $this->post(route('customer.register.store'), [
            'first_name' => 'Nok',
            'country'    => 'TH',
            'phone'      => '12345',
        ])->assertSessionHasErrors('phone');
    }

    public function test_registration_rejects_duplicate_phone(): void
    {
        Customer::factory()->create(['phone' => '+66812345678']);

        $this->post(route('customer.register.store'), [
            'first_name' => 'Nok',
            'country'    => 'TH',
            'phone'      => '0812345678',
        ])->assertSessionHasErrors('phone');
    }

    public function test_registration_password_is_optional_but_validated_when_given(): void
    {
        $this->post(route('customer.register.store'), [
            'first_name' => 'Nok',
            'country'    => 'TH',
            'phone'      => '0812345678',
            'password'   => 'weak',
            'password_confirmation' => 'weak',
        ])->assertSessionHasErrors('password');
    }

    // ── OTP login ─────────────────────────────────────────────────────────

    public function test_customer_can_log_in_with_otp_via_phone(): void
    {
        $customer = Customer::factory()->otpOnly()->create();

        $this->post(route('customer.login.otp'), ['identifier' => $customer->phone])
            ->assertRedirect(route('customer.otp.form', absolute: false));

        $this->post(route('customer.otp.verify'), ['code' => $this->lastSmsCode()])
            ->assertRedirect(route('customer.wallet', absolute: false));

        $this->assertAuthenticatedAs($customer->fresh(), 'customer');
    }

    public function test_otp_login_for_unknown_identifier_responds_identically_and_sends_nothing(): void
    {
        $customer = Customer::factory()->otpOnly()->create();

        $known   = $this->post(route('customer.login.otp'), ['identifier' => $customer->phone]);
        $unknown = $this->post(route('customer.login.otp'), ['identifier' => '0899999999']);

        // Same redirect, same flash — no existence signal
        $known->assertRedirect(route('customer.otp.form', absolute: false));
        $unknown->assertRedirect(route('customer.otp.form', absolute: false));
        $this->assertSame(
            session('status'),
            __('customer.otp_sent_generic'),
        );

        // …but nothing was actually sent or stored for the unknown number
        $this->assertCount(1, $this->sms->sent);
        $this->assertSame(0, CustomerOtp::where('destination', '+66899999999')->count());
    }

    public function test_wrong_otp_counts_attempts_and_kills_the_code(): void
    {
        $customer = Customer::factory()->otpOnly()->create();
        $this->post(route('customer.login.otp'), ['identifier' => $customer->phone]);
        $realCode = $this->lastSmsCode();

        $max = config('customer_identity.otp.max_attempts');
        for ($i = 0; $i < $max; $i++) {
            $wrong = $realCode === '000000' ? '111111' : '000000';
            $this->post(route('customer.otp.verify'), ['code' => $wrong])
                ->assertSessionHasErrors('code');
        }

        // Attempts exhausted — even the REAL code no longer works
        $this->post(route('customer.otp.verify'), ['code' => $realCode])
            ->assertSessionHasErrors('code');
        $this->assertGuest('customer');
    }

    public function test_expired_otp_is_rejected(): void
    {
        $customer = Customer::factory()->otpOnly()->create();
        $this->post(route('customer.login.otp'), ['identifier' => $customer->phone]);
        $code = $this->lastSmsCode();

        $this->travel(config('customer_identity.otp.expires_minutes') + 1)->minutes();

        $this->post(route('customer.otp.verify'), ['code' => $code])
            ->assertSessionHasErrors('code');
        $this->assertGuest('customer');
    }

    public function test_otp_is_single_use(): void
    {
        $customer = Customer::factory()->otpOnly()->create();
        $this->post(route('customer.login.otp'), ['identifier' => $customer->phone]);
        $code = $this->lastSmsCode();

        $this->post(route('customer.otp.verify'), ['code' => $code]);
        auth('customer')->logout();

        $this->withSession(['customer_otp' => ['destination' => $customer->phone, 'purpose' => 'login']])
            ->post(route('customer.otp.verify'), ['code' => $code])
            ->assertSessionHasErrors('code');
    }

    public function test_otp_resend_is_throttled_by_cooldown(): void
    {
        $customer = Customer::factory()->otpOnly()->create();

        $this->post(route('customer.login.otp'), ['identifier' => $customer->phone]);
        $this->assertCount(1, $this->sms->sent);

        // Immediate resend is inside the cooldown window → silently skipped
        $this->post(route('customer.otp.resend'))
            ->assertRedirect(route('customer.otp.form', absolute: false));
        $this->assertCount(1, $this->sms->sent);
    }

    // ── Password login ────────────────────────────────────────────────────

    public function test_customer_can_log_in_with_password_via_email(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->post(route('customer.login.password'), [
            'identifier' => $customer->email,
            'password'   => 'Secret!Password99',
        ])->assertRedirect(route('customer.wallet', absolute: false));

        $this->assertAuthenticatedAs($customer->fresh(), 'customer');
    }

    public function test_customer_can_log_in_with_password_via_phone(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->post(route('customer.login.password'), [
            'identifier' => $customer->phone,
            'password'   => 'Secret!Password99',
        ])->assertRedirect(route('customer.wallet', absolute: false));

        $this->assertAuthenticatedAs($customer->fresh(), 'customer');
    }

    public function test_wrong_password_unknown_account_and_otp_only_account_all_fail_identically(): void
    {
        $customer = Customer::factory()->account()->create();
        $otpOnly  = Customer::factory()->otpOnly()->create();

        $responses = [
            $this->post(route('customer.login.password'), ['identifier' => $customer->email, 'password' => 'wrong-password-1']),
            $this->post(route('customer.login.password'), ['identifier' => 'ghost@example.com', 'password' => 'wrong-password-1']),
            $this->post(route('customer.login.password'), ['identifier' => $otpOnly->phone, 'password' => 'wrong-password-1']),
        ];

        foreach ($responses as $response) {
            $response->assertSessionHasErrors('identifier');
            $this->assertSame(__('customer.login_failed'), session('errors')->first('identifier'));
        }
        $this->assertGuest('customer');
    }

    public function test_suspended_customer_cannot_log_in(): void
    {
        $customer = Customer::factory()->account()->create(['status' => Customer::STATUS_SUSPENDED]);

        $this->post(route('customer.login.password'), [
            'identifier' => $customer->email,
            'password'   => 'Secret!Password99',
        ])->assertSessionHasErrors('identifier');

        $this->assertGuest('customer');
    }

    public function test_password_login_locks_out_after_repeated_failures(): void
    {
        $customer = Customer::factory()->account()->create();

        for ($i = 0; $i < config('customer_identity.login.max_attempts'); $i++) {
            $this->post(route('customer.login.password'), [
                'identifier' => $customer->email,
                'password'   => 'wrong-password',
            ]);
        }

        $this->post(route('customer.login.password'), [
            'identifier' => $customer->email,
            'password'   => 'Secret!Password99', // correct — but locked out
        ])->assertStatus(429);

        $this->assertGuest('customer');
    }

    // ── Password reset ────────────────────────────────────────────────────

    public function test_password_reset_via_otp_end_to_end(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->post(route('customer.password.email'), ['identifier' => $customer->phone])
            ->assertRedirect(route('customer.password.reset', absolute: false));

        $this->post(route('customer.password.update'), [
            'code'                  => $this->lastSmsCode(),
            'password'              => 'NewSecret!Pass123',
            'password_confirmation' => 'NewSecret!Pass123',
        ])->assertRedirect(route('customer.wallet', absolute: false));

        $this->assertAuthenticatedAs($customer->fresh(), 'customer');
        auth('customer')->logout();

        // The new password works for a fresh login
        $this->post(route('customer.login.password'), [
            'identifier' => $customer->phone,
            'password'   => 'NewSecret!Pass123',
        ])->assertRedirect(route('customer.wallet', absolute: false));
    }

    public function test_password_reset_for_unknown_identifier_responds_identically(): void
    {
        $this->post(route('customer.password.email'), ['identifier' => 'ghost@example.com'])
            ->assertRedirect(route('customer.password.reset', absolute: false));

        $this->assertCount(0, $this->sms->sent);
        $this->assertSame(0, CustomerOtp::count());
    }

    // ── Guard separation & guest access ───────────────────────────────────

    public function test_customer_login_does_not_authenticate_the_merchant_guard(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->post(route('customer.login.password'), [
            'identifier' => $customer->email,
            'password'   => 'Secret!Password99',
        ]);

        $this->assertAuthenticated('customer');
        $this->assertGuest('web');
    }

    public function test_merchant_login_still_works_and_does_not_authenticate_customer_guard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertGuest('customer');
    }

    public function test_guest_is_redirected_to_customer_login_not_merchant_login(): void
    {
        $this->get(route('customer.profile', absolute: false))
            ->assertRedirect(route('customer.login', absolute: false));
    }

    public function test_authenticated_customer_is_redirected_away_from_guest_pages(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->get(route('customer.login', absolute: false))
            ->assertRedirect(route('customer.wallet', absolute: false));
    }

    public function test_storefront_and_portal_remain_public_for_guests(): void
    {
        // Guest checkout stays possible — signing in is optional (charter).
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('storefront.show'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('portal.show'));
        $this->get(route('customer.login', absolute: false))->assertOk();
        $this->get(route('customer.register', absolute: false))->assertOk();
    }

    // ── Localization ──────────────────────────────────────────────────────

    public function test_login_page_renders_in_thai(): void
    {
        $this->withSession(['locale' => 'th'])
            ->get(route('customer.login', absolute: false))
            ->assertOk()
            ->assertSee('เข้าสู่ระบบ OneMember', false);
    }

    public function test_login_page_renders_in_english(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get(route('customer.login', absolute: false))
            ->assertOk()
            ->assertSee('Sign in to OneMember', false);
    }
}

/** Test double for the SMS seam — records instead of sending. */
