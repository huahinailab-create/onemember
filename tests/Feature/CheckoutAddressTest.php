<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CUSTOMER-001B — address selection at storefront checkout: saved-address
 * snapshots, add-new-with-save, guest checkout unchanged, and the merchant
 * privacy boundary (only the chosen address ever reaches the order).
 */
class CheckoutAddressTest extends TestCase
{
    use RefreshDatabase;

    private Merchant $merchant;
    private User $owner;
    private Product $latte;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = $this->owner = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $owner->id,
            'onboarding_completed_at' => now(),
            'settings'                => [
                'installed_apps' => ['commerce'],
                'locale'         => 'en',
                'commerce'       => [
                    'pickup_enabled'   => true,
                    'delivery_enabled' => true,
                    'delivery_fee'     => 25,
                ],
            ],
        ]);
        $this->latte = Product::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Latte', 'price' => 60]);
    }

    private function placeOrder(array $overrides = [])
    {
        return $this->post(route('storefront.order.store', $this->merchant->slug), array_merge([
            'customer_name'    => 'Chelsea',
            'customer_phone'   => '0812223333',
            'fulfillment_type' => 'delivery',
            'qty'              => [$this->latte->id => 1],
        ], $overrides));
    }

    private function newAddressPayload(array $overrides = []): array
    {
        return array_merge([
            'recipient_name' => 'Chelsea W.',
            'phone'          => '0812345678',
            'country'        => 'TH',
            'admin_area_1'   => 'Bangkok',
            'admin_area_2'   => 'Watthana',
            'admin_area_3'   => 'Khlong Toei Nuea',
            'postal_code'    => '10110',
            'line1'          => '99/1 Sukhumvit Rd',
        ], $overrides);
    }

    // ── Guest checkout (must keep working exactly as before) ─────────────

    public function test_guest_checkout_with_plain_address_still_works(): void
    {
        $this->placeOrder(['address' => '99 Rama IV'])->assertRedirect();

        $this->assertSame('99 Rama IV', Order::first()->address);
    }

    public function test_guest_cannot_use_a_saved_address_reference(): void
    {
        $address = CustomerAddress::factory()->default()->create();

        // address_choice from a guest is ignored — the plain field is required
        $this->placeOrder(['address_choice' => $address->uuid])
            ->assertSessionHasErrors('address');

        $this->assertSame(0, Order::count());
    }

    // ── Saved-address selection ───────────────────────────────────────────

    public function test_customer_can_order_with_a_saved_address_snapshot(): void
    {
        $customer = Customer::factory()->account()->create();
        $address  = CustomerAddress::factory()->default()->for($customer)->create([
            'recipient_name'        => 'Chelsea W.',
            'line1'                 => '99/1 Sukhumvit Rd',
            'delivery_instructions' => 'Leave with the guard',
        ]);

        $this->actingAs($customer, 'customer')
            ->placeOrder(['address_choice' => $address->uuid])
            ->assertRedirect();

        $order = Order::first();
        $this->assertStringContainsString('Chelsea W.', $order->address);
        $this->assertStringContainsString('99', $order->address);          // street line
        $this->assertStringContainsString('Leave with the guard', $order->address);
    }

    public function test_customer_cannot_use_another_customers_address(): void
    {
        $victim  = Customer::factory()->account()->create();
        $address = CustomerAddress::factory()->default()->for($victim)->create();
        $me      = Customer::factory()->account()->create();

        $this->actingAs($me, 'customer')
            ->placeOrder(['address_choice' => $address->uuid])
            ->assertSessionHasErrors('address_choice');

        $this->assertSame(0, Order::count());
    }

    public function test_customer_cannot_use_an_archived_address(): void
    {
        $customer = Customer::factory()->account()->create();
        $address  = CustomerAddress::factory()->archived()->for($customer)->create();

        $this->actingAs($customer, 'customer')
            ->placeOrder(['address_choice' => $address->uuid])
            ->assertSessionHasErrors('address_choice');
    }

    public function test_customer_must_choose_an_address_for_delivery(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->placeOrder()
            ->assertSessionHasErrors('address_choice');
    }

    public function test_pickup_order_needs_no_address_for_customers(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')
            ->placeOrder(['fulfillment_type' => 'pickup'])
            ->assertRedirect();

        $this->assertNull(Order::first()->address);
    }

    // ── Add new address at checkout ───────────────────────────────────────

    public function test_new_address_at_checkout_is_used_without_saving_by_default(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->placeOrder([
            'address_choice' => 'new',
            'new_address'    => $this->newAddressPayload(),
        ])->assertRedirect();

        $this->assertStringContainsString('Sukhumvit', Order::first()->address);
        $this->assertSame(0, $customer->addresses()->count()); // not saved
    }

    public function test_new_address_with_save_option_lands_in_the_address_book(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->placeOrder([
            'address_choice' => 'new',
            'save_address'   => '1',
            'new_address'    => $this->newAddressPayload(),
        ])->assertRedirect();

        $saved = $customer->addresses()->sole();
        $this->assertSame('99/1 Sukhumvit Rd', $saved->line1);
        $this->assertSame('+66812345678', $saved->phone);   // normalized
        $this->assertTrue($saved->is_default);              // first address
    }

    public function test_new_address_at_checkout_is_validated_per_country(): void
    {
        $customer = Customer::factory()->account()->create();

        $this->actingAs($customer, 'customer')->placeOrder([
            'address_choice' => 'new',
            'new_address'    => $this->newAddressPayload(['postal_code' => 'nope']),
        ])->assertSessionHasErrors('new_address.postal_code');

        $this->assertSame(0, Order::count());
    }

    // ── Merchant privacy ──────────────────────────────────────────────────

    public function test_merchant_receives_only_the_snapshot_never_the_book(): void
    {
        $customer = Customer::factory()->account()->create();
        $chosen   = CustomerAddress::factory()->default()->for($customer)->create(['label' => 'Home']);
        CustomerAddress::factory()->for($customer)->create(['label' => 'Secret Work Address', 'line1' => '1 Hidden Lane']);

        $this->actingAs($customer, 'customer')
            ->placeOrder(['address_choice' => $chosen->uuid])
            ->assertRedirect();

        $order = Order::first();
        // Snapshot only — plain text, no reference, no other book entries
        $this->assertStringNotContainsString('Hidden Lane', $order->address);
        $this->assertStringNotContainsString($chosen->uuid, $order->address);
        $this->assertStringNotContainsString('Secret Work Address', (string) json_encode($order->getAttributes()));

        // Editing the book later never rewrites the merchant's copy
        $before = $order->address;
        $chosen->update(['line1' => 'Totally New Street 1']);
        $this->assertSame($before, $order->fresh()->address);
    }

    public function test_merchant_web_session_gets_no_address_selector_data(): void
    {
        // A merchant viewing their own storefront is not a customer session —
        // the page renders the guest path (no address book leakage).
        $customer = Customer::factory()->account()->create();
        CustomerAddress::factory()->default()->for($customer)->create(['line1' => '1 Private Lane']);

        $this->actingAs($this->owner, 'web')
            ->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertDontSee('Private Lane');
    }

    // ── Storefront rendering ──────────────────────────────────────────────

    public function test_signed_in_customer_sees_their_saved_addresses_at_checkout(): void
    {
        $customer = Customer::factory()->account()->create();
        CustomerAddress::factory()->default()->for($customer)->create(['label' => 'Home Sweet Home']);

        $this->actingAs($customer, 'customer')
            ->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee('Home Sweet Home');
    }

    public function test_guest_sees_plain_address_field(): void
    {
        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee('name="address"', false)
            ->assertDontSee('address_choice');
    }
}
