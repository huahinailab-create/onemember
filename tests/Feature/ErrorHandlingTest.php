<?php

namespace Tests\Feature;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use App\Models\User;
use App\Services\MerchantBrandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * OVERNIGHT-001 P5 — safe error-state guards. Pins graceful degradation so
 * empty data, missing assets, and bad slugs never produce a 500 or a broken
 * page in the private beta.
 */
class ErrorHandlingTest extends TestCase
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

    // ── No merchant / no app ─────────────────────────────────────────────

    public function test_user_without_merchant_cannot_reach_commerce(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // No merchant → app.installed middleware denies (not a 500)
        $this->actingAs($user)->get(route('commerce.products.index', absolute: false))->assertForbidden();
    }

    public function test_merchant_without_commerce_app_cannot_reach_commerce(): void
    {
        [$user] = $this->merchant([]);   // no installed_apps

        $this->actingAs($user)->get(route('commerce.products.index', absolute: false))->assertForbidden();
    }

    public function test_launch_kit_denies_user_without_merchant(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->get(route('launch-kit', absolute: false))->assertForbidden();
    }

    // ── Empty data states ────────────────────────────────────────────────

    public function test_empty_members_campaigns_rewards_render(): void
    {
        [$user] = $this->merchant();

        foreach (['members', 'campaigns.index', 'rewards'] as $name) {
            $this->actingAs($user)->get(route($name, absolute: false))->assertOk();
        }
    }

    public function test_storefront_with_no_products_renders_empty_state(): void
    {
        [, $merchant] = $this->merchant(['installed_apps' => ['commerce'], 'locale' => 'en']);

        $this->get(route('storefront.show', $merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.store_no_products', [], 'en'));
    }

    public function test_commerce_products_empty_state_renders(): void
    {
        [$user] = $this->merchant(['installed_apps' => ['commerce']]);

        $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('commerce.products.index', absolute: false))
            ->assertOk()
            ->assertSee(__('commerce.no_products', [], 'en'));
    }

    // ── Bad / suspended storefront ───────────────────────────────────────

    public function test_invalid_storefront_slug_is_404(): void
    {
        $this->get('/store/definitely-not-a-real-shop')->assertNotFound();
    }

    public function test_suspended_merchant_storefront_is_404(): void
    {
        [, $merchant] = $this->merchant(['installed_apps' => ['commerce']], ['status' => MerchantStatus::Suspended]);

        $this->get(route('storefront.show', $merchant->slug, absolute: false))->assertNotFound();
    }

    public function test_merchant_without_commerce_has_no_public_storefront(): void
    {
        [, $merchant] = $this->merchant([]);

        $this->get(route('storefront.show', $merchant->slug, absolute: false))->assertNotFound();
    }

    public function test_invalid_join_slug_is_404(): void
    {
        $this->get('/join/no-such-merchant')->assertNotFound();
    }

    public function test_invalid_identity_card_uuid_is_404(): void
    {
        $this->get('/omid/' . fake()->uuid())->assertNotFound();
    }

    // ── Missing logo asset ───────────────────────────────────────────────

    public function test_missing_logo_file_falls_back_gracefully(): void
    {
        [$user, $merchant] = $this->merchant();
        // Points at a file that does not exist on disk
        $merchant->update(['logo_path' => 'logos/deleted-file.png']);

        // Service returns null (no 500), dashboard renders text brand mark
        $this->assertNull((new MerchantBrandingService($merchant))->logo());
        $this->actingAs($user)->get(route('dashboard', absolute: false))->assertOk();
    }

    public function test_branding_handles_null_merchant(): void
    {
        $branding = new MerchantBrandingService(null);

        $this->assertNull($branding->logo());
        $this->assertSame(config('app.name'), $branding->displayName());
        $this->assertNotEmpty($branding->primaryColor());
    }
}
