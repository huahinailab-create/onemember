<?php

namespace Tests\Feature;

use App\Mail\CustomerOtpMail;
use App\Models\Customer;
use App\Services\CustomerIdentity\Contracts\SmsProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * CUSTOMER-001A — profile editing and account-security settings, including
 * the mandatory re-verification loop for email/phone changes.
 */
class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

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

    // ── Profile ───────────────────────────────────────────────────────────

    public function test_customer_can_view_profile_with_onemember_id(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->get(route('customer.profile', absolute: false))
            ->assertOk()
            ->assertSee($customer->onemember_id, false);
    }

    public function test_customer_can_update_profile_and_language(): void
    {
        $customer = Customer::factory()->account()->create(['locale' => 'th']);

        $this->actingAs($customer, 'customer')->put(route('customer.profile.update'), [
            'first_name'   => 'Chelsea',
            'last_name'    => 'W.',
            'nickname'     => 'Chel',
            'display_name' => 'Chelsea W.',
            'birthday'     => '1994-02-14',
            'locale'       => 'en',
        ])->assertRedirect(route('customer.profile', absolute: false));

        $customer->refresh();
        $this->assertSame('Chelsea', $customer->first_name);
        $this->assertSame('Chel', $customer->nickname);
        $this->assertSame('Chelsea W.', $customer->name); // canonical card name synced
        $this->assertSame('en', $customer->locale);
        $this->assertSame('en', session('locale'));
        $this->assertSame('1994-02-14', $customer->birthday->format('Y-m-d'));
    }

    public function test_profile_rejects_future_birthday(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->put(route('customer.profile.update'), [
            'first_name' => 'Chelsea',
            'locale'     => 'en',
            'birthday'   => now()->addYear()->format('Y-m-d'),
        ])->assertSessionHasErrors('birthday');
    }

    // ── Password change ───────────────────────────────────────────────────

    public function test_password_change_requires_current_password(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->put(route('customer.password.change'), [
            'current_password'      => 'not-the-password',
            'password'              => 'NewSecret!Pass123',
            'password_confirmation' => 'NewSecret!Pass123',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_password_change_succeeds_with_current_password(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->put(route('customer.password.change'), [
            'current_password'      => 'Secret!Password99',
            'password'              => 'NewSecret!Pass123',
            'password_confirmation' => 'NewSecret!Pass123',
        ])->assertRedirect(route('customer.settings', absolute: false));

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewSecret!Pass123', $customer->fresh()->password));
    }

    public function test_otp_only_account_can_add_a_password_without_current(): void
    {
        $customer = Customer::factory()->otpOnly()->create();

        $this->actingAs($customer, 'customer')->put(route('customer.password.change'), [
            'password'              => 'NewSecret!Pass123',
            'password_confirmation' => 'NewSecret!Pass123',
        ])->assertRedirect(route('customer.settings', absolute: false));

        $this->assertTrue($customer->fresh()->hasPassword());
    }

    // ── Email change with re-verification ─────────────────────────────────

    public function test_email_change_applies_only_after_otp_verification(): void
    {
        Mail::fake();
        $customer = Customer::factory()->account()->create();
        $oldEmail = $customer->email;

        // Request the change — nothing applies yet
        $this->actingAs($customer, 'customer')->post(route('customer.email.change'), [
            'new_email' => 'NEW@Example.com',
        ])->assertRedirect(route('customer.change.confirm', absolute: false));

        $this->assertSame($oldEmail, $customer->fresh()->email);

        // Capture the emailed code (sent to the NEW address)
        $code = null;
        Mail::assertSent(CustomerOtpMail::class, function (CustomerOtpMail $mail) use (&$code) {
            $code = $mail->code;

            return $mail->hasTo('new@example.com');
        });

        // Verify → change applies, already verified by construction
        $this->actingAs($customer, 'customer')->post(route('customer.change.apply'), ['code' => $code])
            ->assertRedirect(route('customer.settings', absolute: false));

        $customer->refresh();
        $this->assertSame('new@example.com', $customer->email);
        $this->assertNotNull($customer->email_verified_at);
    }

    public function test_email_change_rejects_an_email_already_in_use(): void
    {
        $other    = Customer::factory()->create(['email' => 'taken@example.com']);
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->post(route('customer.email.change'), [
            'new_email' => 'taken@example.com',
        ])->assertSessionHasErrors('new_email');
    }

    // ── Phone change with re-verification ─────────────────────────────────

    public function test_phone_change_applies_only_after_otp_verification(): void
    {
        $customer = Customer::factory()->account()->create();
        $oldPhone = $customer->phone;

        $this->actingAs($customer, 'customer')->post(route('customer.phone.change'), [
            'new_phone' => '082 999 8888',
        ])->assertRedirect(route('customer.change.confirm', absolute: false));

        $this->assertSame($oldPhone, $customer->fresh()->phone);
        $this->assertSame('+66829998888', end($this->sms->sent)['to']); // normalized, sent to the NEW number

        $this->actingAs($customer, 'customer')->post(route('customer.change.apply'), [
            'code' => $this->lastSmsCode(),
        ])->assertRedirect(route('customer.settings', absolute: false));

        $customer->refresh();
        $this->assertSame('+66829998888', $customer->phone);
        $this->assertNotNull($customer->phone_verified_at);
    }

    public function test_phone_change_rejects_invalid_number(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->post(route('customer.phone.change'), [
            'new_phone' => '12345',
        ])->assertSessionHasErrors('new_phone');
    }

    public function test_wrong_code_does_not_apply_a_pending_change(): void
    {
        $customer = Customer::factory()->account()->create();
        $oldPhone = $customer->phone;

        $this->actingAs($customer, 'customer')->post(route('customer.phone.change'), [
            'new_phone' => '082 999 8888',
        ]);

        $real = $this->lastSmsCode();
        $wrong = $real === '000000' ? '111111' : '000000';

        $this->actingAs($customer, 'customer')->post(route('customer.change.apply'), ['code' => $wrong])
            ->assertSessionHasErrors('code');

        $this->assertSame($oldPhone, $customer->fresh()->phone);
    }

    public function test_settings_pages_require_customer_authentication(): void
    {
        $this->get(route('customer.settings', absolute: false))
            ->assertRedirect(route('customer.login', absolute: false));
    }

    public function test_merchant_user_session_cannot_access_customer_settings(): void
    {
        $user = \App\Models\User::factory()->create(['email_verified_at' => now()]);

        // A merchant `web` session is NOT a customer session
        $this->actingAs($user, 'web')
            ->get(route('customer.settings', absolute: false))
            ->assertRedirect(route('customer.login', absolute: false));
    }
}
