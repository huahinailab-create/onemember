<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reward;
use App\Models\TermsAcceptance;
use App\Models\User;
use App\Services\IdentityService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * BETA-001 — the COMPLETE private-beta merchant journey as one sequential
 * HTTP test, in the exact order a real founder demo runs it: register →
 * verify → onboard (terms) → campaign → reward → member → purchase → redeem
 * → launch kit → counter → identity card → scan-to-join → Commerce install
 * → product → storefront → order → accept → mark paid → admin surfaces.
 *
 * If any step regresses, this fails with the step name.
 */
class FullBetaJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_entire_private_beta_journey(): void
    {
        Event::fake([Registered::class]);

        // ── 1. Register merchant ─────────────────────────────────────────
        $this->post(route('register'), [
            'name' => 'Journey Owner', 'email' => 'journey@example.com',
            'password' => 'Password!2345', 'password_confirmation' => 'Password!2345',
        ])->assertRedirect();
        $user = User::where('email', 'journey@example.com')->firstOrFail();
        Event::assertDispatched(Registered::class);

        // ── 2. Verify email (signed verification URL, as the email link does) ──
        $verifyUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id, 'hash' => sha1($user->email),
        ]);
        $this->actingAs($user)->get($verifyUrl)->assertRedirect();
        $this->assertNotNull($user->fresh()->email_verified_at, 'STEP 2: email verification failed');
        $user = $user->fresh();

        // ── 3. Onboarding: business info → settings incl. TERMS → loyalty → quick start ──
        $r = $this->actingAs($user)->post(route('onboarding.business-info.store'), [
            'name' => 'Journey Café', 'business_type' => 'Restaurant & Café',
        ]);
        $this->assertSame(302, $r->status(), 'STEP 3a business-info status=' . $r->status());

        $r = $this->actingAs($user->fresh())->post(route('onboarding.business-settings.store'), [
            'currency' => 'THB', 'timezone' => 'Asia/Bangkok', 'date_format' => 'DD/MM/YYYY',
            'locale' => 'en', 'country' => 'TH', 'terms' => 1,
        ]);
        $this->assertSame(302, $r->status(), 'STEP 3b settings status=' . $r->status() . ' errors=' . json_encode(session('errors')?->all() ?? []));
        $merchant = Merchant::where('name', 'Journey Café')->firstOrFail();
        $this->assertTrue(TermsAcceptance::where('merchant_id', $merchant->id)->exists(), 'STEP 3: terms not recorded');

        // fresh() — the in-memory User cached a null merchant from before onboarding
        $r = $this->actingAs($user->fresh())->post(route('onboarding.loyalty.store'), ['loyalty_type' => 'points']);
        $this->assertSame(302, $r->status(), 'STEP 3c loyalty status=' . $r->status());
        $r = $this->actingAs($user->fresh())->post(route('onboarding.quick-start.store'), ['choice' => 'yes']);
        $this->assertSame(302, $r->status(), 'STEP 3d quick-start status=' . $r->status());
        $merchant = $merchant->fresh();
        $this->assertNotNull($merchant->onboarding_completed_at, 'STEP 3: onboarding not completed');
        $this->assertTrue($merchant->loyaltyPrograms()->exists(), 'STEP 3: starter campaign missing');

        // ── 4. Dashboard loads with launch checklist ─────────────────────
        $this->actingAs($user = $user->fresh())->withSession(['locale' => 'en'])->get(route('dashboard', absolute: false))
            ->assertOk()->assertSee(__('launch_check.title', [], 'en'));

        // ── 5. Create an additional campaign + a reward via real HTTP ────
        $this->actingAs($user)->post(route('campaigns.store'), [
            'name' => 'Journey Points', 'type' => 'points', 'status' => 'active',
        ])->assertRedirect();
        $campaign = $merchant->loyaltyPrograms()->where('name', 'Journey Points')->firstOrFail();

        $this->actingAs($user)->post(route('campaigns.rewards.store', $campaign), [
            'name' => 'Free Journey Coffee', 'type' => 'free_item', 'unlimited' => 1,
            'status' => 'active', 'points_required' => 3,
        ])->assertRedirect();
        $reward = Reward::where('name', 'Free Journey Coffee')->firstOrFail();

        // ── 6. Add member → record purchase → redeem reward ─────────────
        $this->actingAs($user)->post(route('members.store'), [
            'name' => 'Chelsea Journey', 'phone' => '0817778888', 'birthday' => '1994-02-14',
        ])->assertRedirect();
        $member = Member::where('merchant_id', $merchant->id)->where('phone', '0817778888')->firstOrFail();

        // Registration auto-created the global identity (PH2-001A)
        $customer = Customer::where('phone', '0817778888')->first();
        $this->assertNotNull($customer, 'STEP 6: identity not created on registration');

        $this->actingAs($user)->post(route('members.purchases.store', $member), ['purchase_amount' => 500])
            ->assertRedirect();
        $this->assertGreaterThan(0, $member->fresh()->total_points, 'STEP 6: purchase earned no points');

        $this->actingAs($user)->post(route('members.redemptions.store', $member), ['reward_id' => $reward->id])
            ->assertRedirect();

        // ── 7. Launch kit + printable QR assets ──────────────────────────
        $this->actingAs($user)->get(route('launch-kit', absolute: false))->assertOk()->assertSee('<svg', false);
        $this->actingAs($user)->get(route('launch-kit.poster', absolute: false))->assertOk()->assertSee('<svg', false);
        $this->actingAs($user)->get(route('launch-kit.counter-card', absolute: false))->assertOk();
        $this->actingAs($user)->get(route('launch-kit.staff-guide', absolute: false))->assertOk();

        // ── 8. Counter mode ──────────────────────────────────────────────
        $settings = $merchant->fresh()->settings;
        $settings['counter_mode'] = true;
        $merchant->update(['settings' => $settings]);
        $this->actingAs($user->fresh())->get('/counter?q=0817778888')->assertOk()->assertSee('Chelsea Journey');

        // ── 9. Customer OneMember card + scan-to-join at a second merchant ──
        $this->get(route('identity.card', $customer->public_uuid, absolute: false))
            ->assertOk()->assertSee($customer->onemember_id);

        $userB = User::factory()->create(['email_verified_at' => now()]);
        Merchant::factory()->create(['user_id' => $userB->id, 'onboarding_completed_at' => now()]);
        $identity = app(IdentityService::class);
        $this->actingAs($userB)->post(route('members.identity.resolve'), [
            'qr_payload' => $identity->qrPayload($customer),
        ])->assertOk();
        $this->actingAs($userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid, 'fields' => ['name', 'phone'],
        ])->assertRedirect();

        // ── 10. Install Commerce app → add product ───────────────────────
        $this->actingAs($user->fresh())->post(route('apps.install'), ['app' => 'commerce'])->assertRedirect();
        $this->assertTrue($merchant->fresh()->hasApp('commerce'), 'STEP 10: commerce install failed');

        $this->actingAs($user->fresh())->post(route('commerce.products.store'), [
            'name' => 'Journey Latte', 'price' => 65, 'status' => 'active', 'category_name' => 'Drinks',
        ])->assertRedirect();
        $product = Product::where('name', 'Journey Latte')->firstOrFail();

        // Enable pickup so the storefront takes orders
        $this->actingAs($user->fresh())->put(route('commerce.settings.update'), [
            'pickup_enabled' => 1, 'payment_instructions' => 'Scan my PromptPay at pickup',
        ])->assertRedirect();

        // ── 11. Public storefront → place order ─────────────────────────
        $this->get(route('storefront.show', $merchant->slug, absolute: false))
            ->assertOk()->assertSee('Journey Latte');

        $this->post(route('storefront.order.store', $merchant->slug), [
            'customer_name' => 'Walk-in Guest', 'customer_phone' => '0801112222',
            'fulfillment_type' => 'pickup', 'qty' => [$product->id => 2],
        ])->assertRedirect();
        $order = Order::where('merchant_id', $merchant->id)->firstOrFail();
        $this->assertEquals(130, (float) $order->total, 'STEP 11: order total wrong');

        // Confirmation shows the merchant's own payment instructions
        $this->get(route('storefront.order.show', [$merchant->slug, $order->public_uuid], absolute: false))
            ->assertOk()->assertSee('Scan my PromptPay at pickup');

        // ── 12. Merchant accepts order → manual payment confirmation ────
        $this->actingAs($user->fresh())->put(route('commerce.orders.status', $order), ['status' => 'accepted'])
            ->assertRedirect();
        $this->actingAs($user->fresh())->put(route('commerce.orders.paid', $order))->assertRedirect();
        $order = $order->fresh();
        $this->assertSame('accepted', $order->status);
        $this->assertSame('paid', $order->payment_status, 'STEP 12: manual payment confirmation failed');

        // ── 13. Admin surfaces ───────────────────────────────────────────
        $admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);
        $this->actingAs($admin)->get(route('admin.dashboard', absolute: false))->assertOk()->assertSee('Merchant Health');
        $this->actingAs($admin)->get(route('admin.control-room', absolute: false))->assertOk();
        $this->actingAs($admin)->get(route('admin.go-live', absolute: false))->assertOk();
    }
}
