<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;
    private Product $latte;
    private Product $cake;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'settings'                => [
                'installed_apps' => ['commerce'],
                'locale'         => 'en',
                'commerce'       => [
                    'pickup_enabled'   => true,
                    'delivery_enabled' => true,
                    'delivery_fee'     => 25,
                    'payment_instructions' => 'Transfer to my bank',
                ],
            ],
        ]);
        $this->latte = Product::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Latte', 'price' => 60]);
        $this->cake  = Product::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Cake', 'price' => 90, 'stock_qty' => 3]);
    }

    private function placeOrder(array $overrides = [])
    {
        return $this->post(route('storefront.order.store', $this->merchant->slug), array_merge([
            'customer_name'    => 'Chelsea',
            'customer_phone'   => '0812223333',
            'fulfillment_type' => 'pickup',
            'qty'              => [$this->latte->id => 2, $this->cake->id => 1],
        ], $overrides));
    }

    // ── Order creation (public) ──────────────────────────────────────────

    public function test_customer_can_place_order_with_server_side_totals(): void
    {
        $this->placeOrder()->assertRedirect();

        $order = Order::first();
        $this->assertSame('placed', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertEquals(210.00, (float) $order->subtotal);   // 2×60 + 90
        $this->assertEquals(210.00, (float) $order->total);      // pickup: no fee
        $this->assertSame(2, $order->items()->count());
        $this->assertSame(2, $this->cake->fresh()->stock_qty);   // stock decremented
    }

    public function test_delivery_order_adds_fee_and_requires_address(): void
    {
        $this->placeOrder(['fulfillment_type' => 'delivery'])
            ->assertSessionHasErrors(['address']);

        $this->placeOrder(['fulfillment_type' => 'delivery', 'address' => '99 Rama IV'])
            ->assertRedirect();

        $this->assertEquals(235.00, (float) Order::first()->total); // 210 + 25 fee
    }

    public function test_empty_order_is_rejected(): void
    {
        $this->placeOrder(['qty' => [$this->latte->id => 0]])
            ->assertSessionHasErrors(['qty']);
    }

    public function test_order_rejects_insufficient_stock_and_foreign_products(): void
    {
        $this->placeOrder(['qty' => [$this->cake->id => 5]])
            ->assertSessionHasErrors(['qty']);

        $otherUser = User::factory()->create();
        $otherMerchant = Merchant::factory()->create(['user_id' => $otherUser->id, 'settings' => ['installed_apps' => ['commerce']]]);
        $foreign = Product::factory()->create(['merchant_id' => $otherMerchant->id]);

        $this->placeOrder(['qty' => [$foreign->id => 1]])
            ->assertSessionHasErrors(['qty']);
    }

    public function test_confirmation_page_shows_merchant_payment_details(): void
    {
        $this->placeOrder();
        $order = Order::first();

        $this->get(route('storefront.order.show', [$this->merchant->slug, $order->public_uuid], absolute: false))
            ->assertOk()
            ->assertSee('Latte')
            ->assertSee('Transfer to my bank')                                   // merchant's own instructions
            ->assertSee(__('commerce.order_pay_note', ['merchant' => $this->merchant->name], 'en'));
    }

    // ── Merchant management ──────────────────────────────────────────────

    public function test_merchant_accepts_and_completes_order_with_audit(): void
    {
        $this->placeOrder();
        $order = Order::first();

        $this->actingAs($this->user)->put(route('commerce.orders.status', $order), ['status' => 'accepted'])
            ->assertRedirect();
        $this->assertSame('accepted', $order->fresh()->status);

        $this->actingAs($this->user)->put(route('commerce.orders.status', $order), ['status' => 'completed']);
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame(2, AuditLog::where('event', 'order.status_changed')->count());
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $this->placeOrder();
        $order = Order::first();

        // placed → ready is not allowed (must accept first)
        $this->actingAs($this->user)->put(route('commerce.orders.status', $order), ['status' => 'ready'])
            ->assertSessionHasErrors(['status']);
        $this->assertSame('placed', $order->fresh()->status);
    }

    public function test_cancelling_restores_tracked_stock(): void
    {
        $this->placeOrder();
        $order = Order::first();
        $this->assertSame(2, $this->cake->fresh()->stock_qty);

        $this->actingAs($this->user)->put(route('commerce.orders.status', $order), ['status' => 'cancelled']);

        $this->assertSame(3, $this->cake->fresh()->stock_qty);
    }

    public function test_manual_payment_confirmation(): void
    {
        $this->placeOrder();
        $order = Order::first();

        $this->actingAs($this->user)->put(route('commerce.orders.paid', $order))->assertRedirect();

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertTrue(AuditLog::where('event', 'order.marked_paid')->exists());

        // Confirmation page now shows the paid badge
        $this->get(route('storefront.order.show', [$this->merchant->slug, $order->public_uuid], absolute: false))
            ->assertSee(__('commerce.order_paid_badge', [], 'en'));
    }

    public function test_orders_are_tenant_isolated(): void
    {
        $this->placeOrder();
        $order = Order::first();

        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        Merchant::factory()->create(['user_id' => $otherUser->id, 'onboarding_completed_at' => now(), 'settings' => ['installed_apps' => ['commerce']]]);

        $this->actingAs($otherUser)->put(route('commerce.orders.status', $order), ['status' => 'accepted'])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->get(route('commerce.orders.index', absolute: false))
            ->assertOk()
            ->assertDontSee('Chelsea');
    }

    public function test_orders_index_requires_commerce_app(): void
    {
        $this->merchant->update(['settings' => ['installed_apps' => []]]);

        $this->actingAs($this->user->fresh())
            ->get(route('commerce.orders.index', absolute: false))
            ->assertForbidden();
    }

    public function test_storefront_order_form_localized_thai(): void
    {
        $settings = $this->merchant->settings;
        $settings['locale'] = 'th';
        $this->merchant->update(['settings' => $settings]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.order_title', [], 'th'));
    }
}
