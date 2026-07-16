<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerMemberLink;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * INTEGRATION-001 — cross-system checks for the unified beta release
 * candidate: the WEBSITE-002 public site, the merchant application, and the
 * CUSTOMER-001A/B/C platform + RELEASE-001 fixes operating together in one
 * application. Single-system behaviour is covered by each sprint's own
 * suite; these tests validate only the seams.
 */
class IntegrationBetaTest extends TestCase
{
    use RefreshDatabase;

    private function corporate(string $path = '/')
    {
        return $this->get('http://'.config('domains.corporate').$path);
    }

    private function commerceMerchant(): Merchant
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);

        return Merchant::factory()->create([
            'user_id'                 => $owner->id,
            'onboarding_completed_at' => now(),
            'settings'                => [
                'installed_apps' => ['commerce'],
                'commerce'       => ['pickup_enabled' => true, 'delivery_enabled' => true, 'delivery_fee' => 20],
            ],
        ]);
    }

    // ── Public website + release assets in the merged application ─────────

    public function test_public_website_renders_for_guests_alongside_customer_platform(): void
    {
        foreach (['/', '/features', '/industries', '/pricing', '/about', '/faq', '/contact', '/resources'] as $path) {
            $this->corporate($path)->assertOk();
        }
    }

    public function test_release_audit_assets_survive_the_website_merge(): void
    {
        // Both branches shipped a PNG og-image; the merged layout must use it
        $this->corporate('/')->assertSee('images/og-default.png', false);

        $this->assertFileExists(public_path('images/og-default.png'));
        $this->assertFileExists(public_path('icons/icon-192.png'));   // RELEASE-001
        $this->assertFileExists(public_path('icons/icon-512.png'));
        $this->assertFileExists(public_path('favicon.png'));

        $this->corporate('/sitemap.xml')->assertOk();                 // WEBSITE-002A polish
        $this->assertStringContainsString('Disallow: /account',       // RELEASE-001
            file_get_contents(public_path('robots.txt')));
    }

    public function test_marketing_pages_use_the_slim_corporate_bundle_and_wallet_uses_app(): void
    {
        $home = $this->corporate('/')->assertOk()->getContent();
        $this->assertStringContainsString('assets/corporate-', $home);        // slim bundle
        $this->assertStringNotContainsString('assets/product-image-', $home); // no Cropper leak

        $customer = Customer::factory()->account()->create();
        $wallet = $this->actingAs($customer, 'customer')
            ->get(route('customer.wallet', absolute: true))
            ->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/assets\/app-[\w-]+\.js/', $wallet); // full app bundle
        $this->assertStringNotContainsString('assets/corporate-', $wallet);
    }

    public function test_branded_localized_404_serves_in_the_merged_app(): void
    {
        $this->get('http://'.config('domains.app').'/definitely-not-a-page')
            ->assertNotFound()
            ->assertSee(__('errors.404_title'));
    }

    // ── Guard isolation across the merged systems ─────────────────────────

    public function test_guards_stay_isolated_in_both_directions(): void
    {
        $merchantUser = User::factory()->create(['email_verified_at' => now()]);
        $customer     = Customer::factory()->account()->create();

        // Merchant session cannot enter the customer platform
        $this->actingAs($merchantUser, 'web')
            ->get(route('customer.wallet', absolute: true))
            ->assertRedirect(route('customer.login', absolute: true));

        // Fresh application state (actingAs pins the default guard in-process,
        // which a real browser request never sees)
        $this->refreshApplication();

        // Customer session cannot enter the merchant application. Log in via
        // real HTTP (not actingAs) so the default guard behaves as production.
        $this->post(route('customer.login.password'), [
            'identifier' => $customer->email,
            'password'   => 'Secret!Password99',
        ]);
        $this->assertFalse(auth('web')->check()); // customer login never touches the web guard
        $this->get('http://'.config('domains.app').'/dashboard')
            ->assertRedirect('http://'.config('domains.app').'/login');
    }

    public function test_customer_login_page_and_merchant_login_page_coexist(): void
    {
        $this->get('http://'.config('domains.app').'/login')->assertOk();
        $this->get(route('customer.login', absolute: true))->assertOk();
    }

    // ── End-to-end seam: website → storefront → checkout → wallet ─────────

    public function test_signed_in_checkout_with_saved_address_lands_in_wallet_order_history(): void
    {
        $merchant = $this->commerceMerchant();
        $product  = Product::factory()->create(['merchant_id' => $merchant->id, 'name' => 'Latte', 'price' => 55]);
        $customer = Customer::factory()->account()->create();
        $address  = CustomerAddress::factory()->default()->for($customer)->create(['line1' => '9 Integration Rd']);

        // Storefront (public page) shows the saved address to its owner
        $this->actingAs($customer, 'customer')
            ->get(route('storefront.show', $merchant->slug, absolute: true))
            ->assertOk()
            ->assertSee('Integration Rd');

        // Checkout with the saved address
        $this->actingAs($customer, 'customer')->post(route('storefront.order.store', $merchant->slug), [
            'customer_name'    => $customer->name,
            'customer_phone'   => '0812345678',
            'fulfillment_type' => 'delivery',
            'address_choice'   => $address->uuid,
            'qty'              => [$product->id => 2],
        ])->assertRedirect();

        $order = Order::sole();
        $this->assertSame($customer->id, $order->customer_id);
        $this->assertStringContainsString('9 Integration Rd', $order->address);

        // The order appears in the wallet's order history
        $this->actingAs($customer, 'customer')
            ->get(route('customer.wallet.orders', absolute: true))
            ->assertOk()
            ->assertSee('Latte')
            ->assertSee('Integration Rd');
    }

    public function test_guest_checkout_still_works_in_the_merged_application(): void
    {
        $merchant = $this->commerceMerchant();
        $product  = Product::factory()->create(['merchant_id' => $merchant->id, 'price' => 40]);

        $this->post(route('storefront.order.store', $merchant->slug), [
            'customer_name'    => 'Walk-in Guest',
            'customer_phone'   => '0899998888',
            'fulfillment_type' => 'pickup',
            'qty'              => [$product->id => 1],
        ])->assertRedirect();

        $this->assertNull(Order::sole()->customer_id);
    }

    public function test_customer_account_coexists_with_merchant_membership_links(): void
    {
        $merchant = $this->commerceMerchant();
        LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);
        $customer = Customer::factory()->account()->create();
        $member   = Member::factory()->create(['merchant_id' => $merchant->id, 'total_points' => 77]);

        CustomerMemberLink::create([
            'customer_id' => $customer->id, 'member_id' => $member->id,
            'merchant_id' => $merchant->id, 'linked_via' => 'test', 'linked_at' => now(),
        ]);

        // Wallet shows the membership and links back to the public storefront
        $this->actingAs($customer, 'customer')
            ->get(route('customer.wallet.membership', $member->public_uuid, absolute: true))
            ->assertOk()
            ->assertSee('77')
            ->assertSee(route('storefront.show', $merchant->slug), false);

        // The merchant's own member screens are untouched by customer auth
        $this->actingAs($merchant->owner, 'web')
            ->get('http://'.config('domains.app').'/members')
            ->assertOk();
    }

    // ── Localization across merged surfaces ───────────────────────────────

    public function test_thai_renders_on_website_and_wallet(): void
    {
        $this->withSession(['locale' => 'th'])->corporate('/')->assertOk()->assertSee('lang="th"', false);

        $customer = Customer::factory()->account()->create(['locale' => 'th']);
        $this->actingAs($customer, 'customer')
            ->withSession(['locale' => 'th'])
            ->get(route('customer.wallet', absolute: true))
            ->assertOk()
            ->assertSee('ทางลัด');
    }
}
