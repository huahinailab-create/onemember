<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MerchantStatus;
use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'name'                    => 'Paris Coffee',
            'onboarding_completed_at' => now(),
            'settings'                => [
                'installed_apps' => ['commerce'],
                'locale'         => 'en',
                'commerce'       => ['pickup_enabled' => true, 'delivery_enabled' => true, 'delivery_radius_km' => 5],
            ],
        ]);
    }

    public function test_storefront_renders_profile_products_by_category_and_fulfillment(): void
    {
        $product = Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'name'        => 'Iced Latte Supreme',
            'price'       => 65,
        ]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee('Paris Coffee')
            ->assertSee('Iced Latte Supreme')
            ->assertSee(__('commerce.pickup_label', [], 'en'))
            ->assertSee('5')   // delivery radius shown
            ->assertSee(__('commerce.store_seller_note', ['merchant' => 'Paris Coffee'], 'en'));
    }

    public function test_storefront_hides_hidden_and_flags_out_of_stock(): void
    {
        Product::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Hidden Item', 'status' => 'hidden']);
        Product::factory()->create(['merchant_id' => $this->merchant->id, 'name' => 'Sold Out Item', 'stock_qty' => 0]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertDontSee('Hidden Item')
            ->assertSee('Sold Out Item')
            ->assertSee(__('commerce.stock_out', [], 'en'));
    }

    public function test_storefront_shows_loyalty_and_rewards_with_join_link(): void
    {
        LoyaltyProgram::factory()->create([
            'merchant_id' => $this->merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
        ]);
        Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => LoyaltyProgram::first()->id,
            'name'               => 'Free Croissant',
            'status'             => 'active',
            'points_required'    => 120,
        ]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.store_loyalty_title', [], 'en'))
            ->assertSee('Free Croissant')
            ->assertSee(route('join.show', $this->merchant->slug, absolute: false));
    }

    public function test_storefront_404_without_commerce_app(): void
    {
        $this->merchant->update(['settings' => ['installed_apps' => []]]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertNotFound();
    }

    public function test_storefront_404_for_suspended_merchant_or_unknown_slug(): void
    {
        $this->get('/store/no-such-shop')->assertNotFound();

        $this->merchant->update(['status' => MerchantStatus::Suspended]);
        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))->assertNotFound();
    }

    public function test_storefront_follows_merchant_locale_not_browser(): void
    {
        $settings = $this->merchant->settings;
        $settings['locale'] = 'th';
        $this->merchant->update(['settings' => $settings]);

        $this->withHeaders(['Accept-Language' => 'en-US'])
            ->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.store_products_title', [], 'th'));
    }
}
