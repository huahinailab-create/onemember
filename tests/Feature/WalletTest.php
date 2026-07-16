<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\RewardStatus;
use App\Enums\TransactionType;
use App\Models\Customer;
use App\Models\CustomerMemberLink;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CUSTOMER-001C — the OneMember Wallet: home, memberships, rewards,
 * activity, orders, preferences, localization, and the hard boundaries
 * (own data only, merchant sessions locked out, points never combined).
 */
class WalletTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::factory()->account()->create(['first_name' => 'Chelsea']);
    }

    /** A merchant + active points programme + linked member for the customer. */
    private function joinMerchant(array $memberAttrs = [], string $programmeType = 'points', ?Customer $customer = null): Member
    {
        $merchant = Merchant::factory()->create(['onboarding_completed_at' => now()]);
        LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'type'        => $programmeType === 'stamps' ? LoyaltyProgramType::Stamps : LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
        ]);
        $member = Member::factory()->create(array_merge(['merchant_id' => $merchant->id], $memberAttrs));

        CustomerMemberLink::create([
            'customer_id' => ($customer ?? $this->customer)->id,
            'member_id'   => $member->id,
            'merchant_id' => $merchant->id,
            'linked_via'  => 'test',
            'linked_at'   => now(),
        ]);

        return $member;
    }

    private function reward(Member $member, int $points, ?int $quantity = null): Reward
    {
        return Reward::factory()->create([
            'merchant_id'        => $member->merchant_id,
            'points_required'    => $points,
            'quantity_available' => $quantity,
            'status'             => RewardStatus::Active,
        ]);
    }

    // ── Home ──────────────────────────────────────────────────────────────

    public function test_wallet_home_greets_by_first_name_with_summary(): void
    {
        $member = $this->joinMerchant(['total_points' => 500]);
        $this->reward($member, 100); // available

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet', absolute: false))
            ->assertOk()
            ->assertSee('Chelsea')
            ->assertSee(__('customer_wallet.stat_merchants'))
            ->assertSee(__('customer_wallet.coming_soon'));
    }

    public function test_login_lands_on_the_wallet(): void
    {
        $this->post(route('customer.login.password'), [
            'identifier' => $this->customer->email,
            'password'   => 'Secret!Password99',
        ])->assertRedirect(route('customer.wallet', absolute: false));
    }

    // ── Memberships ───────────────────────────────────────────────────────

    public function test_memberships_show_each_merchant_with_its_own_points(): void
    {
        $this->joinMerchant(['total_points' => 120]);
        $this->joinMerchant(['total_points' => 4500]);

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.memberships', absolute: false))
            ->assertOk()
            ->assertSee('120')
            ->assertSee('4,500');

        // Never combined: the total across merchants appears nowhere
        $response->assertDontSee('4,620');
    }

    public function test_stamps_programme_is_labelled_in_stamps(): void
    {
        $this->joinMerchant(['total_points' => 7], 'stamps');

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.memberships', absolute: false))
            ->assertOk()
            ->assertSee(__('customer_wallet.unit_stamps'));
    }

    public function test_membership_detail_is_read_only_with_member_information(): void
    {
        $member = $this->joinMerchant(['total_points' => 300]);
        Transaction::factory()->create([
            'merchant_id' => $member->merchant_id,
            'member_id'   => $member->id,
            'type'        => TransactionType::Earn,
            'points'      => 50,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.membership', $member->public_uuid, absolute: false))
            ->assertOk()
            ->assertSee($member->member_code, false)
            ->assertSee(__('customer_wallet.membership_info'))
            ->assertSee(__('customer_wallet.recent_transactions'));
    }

    public function test_foreign_membership_detail_is_a_404(): void
    {
        $other       = Customer::factory()->account()->create();
        $otherMember = $this->joinMerchant(customer: $other);

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.membership', $otherMember->public_uuid, absolute: false))
            ->assertNotFound();
    }

    // ── Rewards ───────────────────────────────────────────────────────────

    public function test_rewards_are_grouped_with_honest_statuses_and_disabled_redeem(): void
    {
        $member = $this->joinMerchant(['total_points' => 500]);
        $this->reward($member, 100);   // available
        $this->reward($member, 9999);  // coming soon (not affordable)
        $this->reward($member, 100, 0); // coming soon (out of stock)

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.rewards', absolute: false))
            ->assertOk()
            ->assertSee(__('customer_wallet.reward_available'))
            ->assertSee(__('customer_wallet.reward_coming_soon'));

        // Redeem exists but is honestly disabled — no fake functionality
        $this->assertStringContainsString('disabled', $response->getContent());
    }

    public function test_rewards_from_other_customers_merchants_never_leak(): void
    {
        $other       = Customer::factory()->account()->create();
        $otherMember = $this->joinMerchant(customer: $other);
        $this->reward($otherMember, 100)->update(['name' => 'Secret Reward']);

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.rewards', absolute: false))
            ->assertOk()
            ->assertDontSee('Secret Reward');
    }

    // ── Activity ──────────────────────────────────────────────────────────

    public function test_activity_lists_joins_transactions_and_orders_newest_first(): void
    {
        $member = $this->joinMerchant(['total_points' => 100]);
        Transaction::factory()->create([
            'merchant_id' => $member->merchant_id,
            'member_id'   => $member->id,
            'type'        => TransactionType::Earn,
            'points'      => 25,
            'created_at'  => now()->subDay(),
        ]);
        Order::create([
            'merchant_id'      => $member->merchant_id,
            'customer_id'      => $this->customer->id,
            'customer_name'    => 'Chelsea',
            'customer_phone'   => '0812345678',
            'fulfillment_type' => 'pickup',
            'subtotal'         => 60,
            'fulfillment_fee'  => 0,
            'total'            => 60,
        ]);

        $content = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.activity', absolute: false))
            ->assertOk()
            ->getContent();

        $orderPos = strpos($content, __('customer_wallet.order_number', ['number' => Order::first()->id]));
        $earnPos  = strpos($content, '+25');
        $this->assertNotFalse($orderPos);
        $this->assertNotFalse($earnPos);
        $this->assertLessThan($earnPos, $orderPos); // newest (order, today) first
    }

    // ── Orders ────────────────────────────────────────────────────────────

    public function test_order_history_shows_own_orders_with_items_and_address(): void
    {
        $member = $this->joinMerchant();
        $order  = Order::create([
            'merchant_id'      => $member->merchant_id,
            'customer_id'      => $this->customer->id,
            'customer_name'    => 'Chelsea',
            'customer_phone'   => '0812345678',
            'fulfillment_type' => 'delivery',
            'address'          => "Chelsea W.\n99 Sukhumvit Rd",
            'subtotal'         => 100,
            'fulfillment_fee'  => 25,
            'total'            => 125,
        ]);
        $order->items()->create(['product_id' => null, 'name' => 'Latte', 'price' => 100, 'qty' => 1]);

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.orders', absolute: false))
            ->assertOk()
            ->assertSee('Latte')
            ->assertSee('125.00')
            ->assertSee('Sukhumvit');
    }

    public function test_other_customers_orders_never_appear(): void
    {
        $other  = Customer::factory()->account()->create();
        $member = $this->joinMerchant(customer: $other);
        Order::create([
            'merchant_id'      => $member->merchant_id,
            'customer_id'      => $other->id,
            'customer_name'    => 'Somebody Else',
            'customer_phone'   => '0899999999',
            'fulfillment_type' => 'pickup',
            'subtotal'         => 10, 'fulfillment_fee' => 0, 'total' => 10,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet.orders', absolute: false))
            ->assertOk()
            ->assertDontSee('Somebody Else');
    }

    public function test_signed_in_checkout_records_wallet_ownership(): void
    {
        $owner    = User::factory()->create(['email_verified_at' => now()]);
        $merchant = Merchant::factory()->create([
            'user_id'                 => $owner->id,
            'onboarding_completed_at' => now(),
            'settings'                => [
                'installed_apps' => ['commerce'],
                'commerce'       => ['pickup_enabled' => true],
            ],
        ]);
        $product = \App\Models\Product::factory()->create(['merchant_id' => $merchant->id, 'price' => 60]);

        $this->actingAs($this->customer, 'customer')->post(route('storefront.order.store', $merchant->slug), [
            'customer_name'    => 'Chelsea',
            'customer_phone'   => '0812345678',
            'fulfillment_type' => 'pickup',
            'qty'              => [$product->id => 1],
        ])->assertRedirect();

        $this->assertSame($this->customer->id, Order::first()->customer_id);
    }

    // ── Preferences ───────────────────────────────────────────────────────

    public function test_customer_can_save_communication_preferences(): void
    {
        $this->actingAs($this->customer, 'customer')->put(route('customer.preferences.update'), [
            'communication_channel' => 'sms',
            'marketing_opt_in'      => '1',
        ])->assertRedirect(route('customer.settings', absolute: false));

        $fresh = $this->customer->fresh();
        $this->assertSame('sms', $fresh->preference('communication_channel'));
        $this->assertTrue($fresh->preference('marketing_opt_in'));
    }

    public function test_invalid_communication_channel_is_rejected(): void
    {
        $this->actingAs($this->customer, 'customer')->put(route('customer.preferences.update'), [
            'communication_channel' => 'carrier-pigeon',
        ])->assertSessionHasErrors('communication_channel');
    }

    // ── Authorization ─────────────────────────────────────────────────────

    public function test_guests_cannot_open_the_wallet(): void
    {
        foreach (['customer.wallet', 'customer.wallet.memberships', 'customer.wallet.rewards',
            'customer.wallet.activity', 'customer.wallet.orders'] as $routeName) {
            $this->get(route($routeName, absolute: false))
                ->assertRedirect(route('customer.login', absolute: false));
        }
    }

    public function test_merchant_web_session_cannot_open_the_wallet(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user, 'web')
            ->get(route('customer.wallet', absolute: false))
            ->assertRedirect(route('customer.login', absolute: false));
    }

    // ── Localization ──────────────────────────────────────────────────────

    public function test_wallet_renders_in_thai(): void
    {
        $this->joinMerchant(['total_points' => 10]);

        $this->actingAs($this->customer, 'customer')
            ->withSession(['locale' => 'th'])
            ->get(route('customer.wallet', absolute: false))
            ->assertOk()
            ->assertSee('ทางลัด')       // quick links
            ->assertSee('เร็ว ๆ นี้');  // coming soon
    }

    public function test_wallet_navigation_is_present_and_labelled(): void
    {
        $this->actingAs($this->customer, 'customer')
            ->get(route('customer.wallet', absolute: false))
            ->assertOk()
            ->assertSee(__('customer_wallet.nav_label'))
            ->assertSee(__('customer_wallet.nav_memberships'))
            ->assertSee(__('customer_wallet.nav_rewards'));
    }
}
