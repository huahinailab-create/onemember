<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceAppTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'settings'                => ['installed_apps' => ['commerce']],
        ]);
    }

    // ── App gating (Commerce stays an App) ───────────────────────────────

    public function test_commerce_routes_403_without_app_installed(): void
    {
        $this->merchant->update(['settings' => ['installed_apps' => []]]);

        $this->actingAs($this->user)->get(route('commerce.products.index', absolute: false))
            ->assertForbidden();
    }

    public function test_products_page_loads_with_app_installed(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('commerce.products.index', absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.products_title', [], 'en'));
    }

    public function test_sidebar_shows_commerce_only_when_installed(): void
    {
        $this->actingAs($this->user)->get('/dashboard')
            ->assertSee(route('commerce.products.index', absolute: false));

        $this->merchant->update(['settings' => ['installed_apps' => []]]);

        // fresh() avoids the in-memory relation cache on the test's User
        // instance — real requests always rehydrate.
        $this->actingAs($this->user->fresh())->get('/dashboard')
            ->assertDontSee(route('commerce.products.index', absolute: false));
    }

    // ── Product CRUD + categories + inventory ────────────────────────────

    public function test_product_can_be_created_with_new_category(): void
    {
        $this->actingAs($this->user)->post(route('commerce.products.store'), [
            'name'          => 'Iced Latte',
            'price'         => 65,
            'stock_qty'     => null,
            'status'        => 'active',
            'category_name' => 'Drinks',
        ])->assertRedirect();

        $product = Product::first();
        $this->assertSame('Iced Latte', $product->name);
        $this->assertSame('Drinks', $product->category->name);
        $this->assertSame($this->merchant->id, $product->merchant_id);
    }

    public function test_category_is_reused_not_duplicated(): void
    {
        foreach (['A', 'B'] as $name) {
            $this->actingAs($this->user)->post(route('commerce.products.store'), [
                'name' => "Product $name", 'price' => 10, 'status' => 'active',
                'category_name' => 'Drinks',
            ]);
        }

        $this->assertSame(1, ProductCategory::count());
    }

    public function test_product_update_and_inventory_tracking(): void
    {
        $product = Product::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->actingAs($this->user)->put(route('commerce.products.update', $product), [
            'name' => 'Renamed', 'price' => 99.50, 'stock_qty' => 5, 'status' => 'hidden',
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame('Renamed', $product->name);
        $this->assertSame(5, $product->stock_qty);
        $this->assertFalse($product->isAvailable()); // hidden

        $product->update(['status' => 'active', 'stock_qty' => 0]);
        $this->assertFalse($product->isAvailable()); // out of stock

        $product->update(['stock_qty' => null]);
        $this->assertTrue($product->isAvailable());  // untracked stock
    }

    public function test_product_archive_soft_deletes(): void
    {
        $product = Product::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->actingAs($this->user)->delete(route('commerce.products.archive', $product))
            ->assertRedirect();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_products_are_tenant_isolated(): void
    {
        $otherUser     = User::factory()->create(['email_verified_at' => now()]);
        $otherMerchant = Merchant::factory()->create([
            'user_id'  => $otherUser->id,
            'settings' => ['installed_apps' => ['commerce']],
        ]);
        $foreign = Product::factory()->create(['merchant_id' => $otherMerchant->id, 'name' => 'Foreign Product Zeta']);

        $this->actingAs($this->user)
            ->get(route('commerce.products.index', absolute: false))
            ->assertDontSee('Foreign Product Zeta');

        $this->actingAs($this->user)
            ->put(route('commerce.products.update', $foreign), ['name' => 'Hacked', 'price' => 1, 'status' => 'active'])
            ->assertForbidden();
    }

    // ── Fulfillment settings (merchant-controlled, ADR-011) ─────────────

    public function test_fulfillment_settings_save_including_delivery_radius(): void
    {
        $this->actingAs($this->user)->put(route('commerce.settings.update'), [
            'pickup_enabled'       => 1,
            'delivery_enabled'     => 1,
            'delivery_radius_km'   => 5,
            'delivery_fee'         => 20,
            'shipping_enabled'     => 0,
            'payment_instructions' => 'Scan my QR at pickup',
        ])->assertRedirect();

        $commerce = $this->merchant->fresh()->settings['commerce'];
        $this->assertTrue($commerce['pickup_enabled']);
        $this->assertTrue($commerce['delivery_enabled']);
        $this->assertEquals(5, $commerce['delivery_radius_km']);
        $this->assertEquals(20, $commerce['delivery_fee']);
        $this->assertFalse($commerce['shipping_enabled']);
        $this->assertSame('Scan my QR at pickup', $commerce['payment_instructions']);
    }

    // ── Localization ─────────────────────────────────────────────────────

    public function test_products_page_defaults_to_thai(): void
    {
        $this->actingAs($this->user)
            ->get(route('commerce.products.index', absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.products_title', [], 'th'));
    }
}
