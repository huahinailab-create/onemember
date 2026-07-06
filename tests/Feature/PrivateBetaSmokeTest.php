<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MemberStatus;
use App\Models\Customer;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Reward;
use App\Models\User;
use App\Services\IdentityService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * OVERNIGHT-001 P2 — end-to-end private-beta smoke tests. One happy path per
 * critical surface, asserting it loads and the core action works. Breadth over
 * depth: this suite is the "is the beta walkable end to end?" guard.
 */
class PrivateBetaSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function merchant(array $settings = [], array $attrs = []): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $merchant = Merchant::factory()->create(array_merge([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'settings'                => $settings,
        ], $attrs));

        return [$user, $merchant];
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);
    }

    // ── Registration & verification assumptions ──────────────────────────

    public function test_merchant_registration_creates_verified_pending_user(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('register'), [
            'name' => 'Beta Owner', 'email' => 'beta@example.com',
            'password' => 'Password!2345', 'password_confirmation' => 'Password!2345',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'beta@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);          // must verify (MustVerifyEmail)
        Event::assertDispatched(Registered::class);           // verification email event fired
    }

    public function test_unverified_user_is_blocked_from_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get(route('dashboard', absolute: false))
            ->assertRedirect(route('verification.notice', absolute: false));
    }

    // ── Onboarding ───────────────────────────────────────────────────────

    public function test_onboarding_finish_page_loads(): void
    {
        [$user] = $this->merchant();

        $this->actingAs($user)->get(route('onboarding.finish', absolute: false))->assertOk();
    }

    // ── Admin surfaces ───────────────────────────────────────────────────

    public function test_admin_dashboard_control_room_and_go_live_load(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.dashboard', absolute: false))->assertOk();
        $this->actingAs($admin)->get(route('admin.control-room', absolute: false))->assertOk();
        $this->actingAs($admin)->get(route('admin.go-live', absolute: false))->assertOk();
        $this->actingAs($admin)->get(route('admin.merchants.index', absolute: false))->assertOk();
    }

    // ── Merchant core surfaces ───────────────────────────────────────────

    public function test_members_campaigns_rewards_pages_load(): void
    {
        [$user] = $this->merchant();

        foreach (['members', 'campaigns.index', 'rewards', 'launch-kit', 'apps.index'] as $name) {
            $this->actingAs($user)->get(route($name, absolute: false))->assertOk();
        }
    }

    public function test_counter_mode_loads_when_enabled(): void
    {
        [$user] = $this->merchant(['counter_mode' => true]);

        $this->actingAs($user)->get(route('counter', absolute: false))->assertOk();
    }

    public function test_full_loyalty_cycle_purchase_then_redeem(): void
    {
        [$user, $merchant] = $this->merchant();
        $campaign = LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id, 'type' => LoyaltyProgramType::Points,
            'status' => CampaignStatus::Active, 'settings' => ['spend_amount' => 100, 'points_awarded' => 1],
        ]);
        $reward = Reward::factory()->create([
            'merchant_id' => $merchant->id, 'loyalty_program_id' => $campaign->id,
            'status' => 'active', 'points_required' => 3,
        ]);
        $member = Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active, 'total_points' => 0]);

        $this->actingAs($user)->post(route('members.purchases.store', $member), ['purchase_amount' => 500])->assertRedirect();
        $this->assertSame(5, $member->fresh()->total_points);

        $this->actingAs($user)->post(route('members.redemptions.store', $member), ['reward_id' => $reward->id])->assertRedirect();
        $this->assertSame(2, $member->fresh()->total_points);
    }

    // ── Identity: card + scan-to-join ────────────────────────────────────

    public function test_identity_card_and_scan_to_join(): void
    {
        [$userA, $merchantA] = $this->merchant();
        Member::factory()->create(['merchant_id' => $merchantA->id, 'phone' => '0812223344', 'status' => MemberStatus::Active]);
        $memberA = Member::where('merchant_id', $merchantA->id)->first();
        $identity = app(IdentityService::class);
        $customer = $identity->ensureIdentityForMember($memberA);
        $this->assertNotNull($customer);

        // Card renders (public)
        $this->get(route('identity.card', $customer->public_uuid, absolute: false))->assertOk()->assertSee($customer->onemember_id);

        // Merchant B scans → consent → join
        [$userB, $merchantB] = $this->merchant();
        $this->actingAs($userB)->post(route('members.identity.resolve'), ['qr_payload' => $identity->qrPayload($customer)])->assertOk();
        $this->actingAs($userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid, 'fields' => ['name', 'phone'],
        ])->assertRedirect();

        $this->assertSame(1, Member::where('merchant_id', $merchantB->id)->count());
    }

    // ── Commerce: products, storefront, order ────────────────────────────

    public function test_commerce_products_page_loads_with_app(): void
    {
        [$user] = $this->merchant(['installed_apps' => ['commerce']]);

        $this->actingAs($user)->get(route('commerce.products.index', absolute: false))->assertOk();
        $this->actingAs($user)->get(route('commerce.orders.index', absolute: false))->assertOk();
        $this->actingAs($user)->get(route('commerce.settings', absolute: false))->assertOk();
    }

    public function test_storefront_and_order_flow(): void
    {
        [, $merchant] = $this->merchant([
            'installed_apps' => ['commerce'], 'locale' => 'en',
            'commerce' => ['pickup_enabled' => true],
        ]);
        $product = Product::factory()->create(['merchant_id' => $merchant->id, 'name' => 'Smoke Latte', 'price' => 55]);

        // Public storefront loads
        $this->get(route('storefront.show', $merchant->slug, absolute: false))->assertOk()->assertSee('Smoke Latte');

        // Place an order → confirmation page
        $this->post(route('storefront.order.store', $merchant->slug), [
            'customer_name' => 'Guest', 'customer_phone' => '0800000000',
            'fulfillment_type' => 'pickup', 'qty' => [$product->id => 2],
        ])->assertRedirect();

        $order = \App\Models\Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(110, (float) $order->total);
        $this->get(route('storefront.order.show', [$merchant->slug, $order->public_uuid], absolute: false))->assertOk();
    }

    // ── Public join landing ──────────────────────────────────────────────

    public function test_public_join_landing_loads(): void
    {
        [, $merchant] = $this->merchant();

        $this->get(route('join.show', $merchant->slug, absolute: false))->assertOk()->assertSee($merchant->name);
    }
}
