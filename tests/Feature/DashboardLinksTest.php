<?php

namespace Tests\Feature;

use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLinksTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedMerchant(array $merchantAttributes = []): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $merchant = Merchant::factory()->create(array_merge([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
        ], $merchantAttributes));

        return [$user, $merchant];
    }

    // ── Settings ──────────────────────────────────────────────────────────────

    public function test_settings_page_returns_200_for_verified_merchant(): void
    {
        [$user] = $this->makeVerifiedMerchant();

        $this->actingAs($user)->get(route('settings'))->assertOk();
    }

    public function test_settings_page_returns_200_when_merchant_settings_is_null(): void
    {
        // Factory defaults settings to null — this was the crash scenario in BUG-002
        [$user] = $this->makeVerifiedMerchant(['settings' => null]);

        $this->actingAs($user)->get(route('settings'))->assertOk();
    }

    public function test_settings_page_returns_200_when_merchant_settings_is_empty_array(): void
    {
        [$user] = $this->makeVerifiedMerchant(['settings' => []]);

        $this->actingAs($user)->get(route('settings'))->assertOk();
    }

    public function test_settings_route_resolves_to_correct_url(): void
    {
        $this->assertStringContainsString('/settings', route('settings'));
    }

    public function test_settings_page_requires_authentication(): void
    {
        $this->get(route('settings'))->assertRedirect(route('login'));
    }

    // ── Campaign View ─────────────────────────────────────────────────────────

    public function test_campaign_show_returns_200_for_verified_merchant(): void
    {
        [$user, $merchant] = $this->makeVerifiedMerchant();
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id]);

        $this->actingAs($user)->get(route('campaigns.show', $campaign))->assertOk();
    }

    public function test_campaign_show_returns_200_when_campaign_settings_is_null(): void
    {
        [$user, $merchant] = $this->makeVerifiedMerchant();
        $campaign = LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'settings'    => null,
        ]);

        $this->actingAs($user)->get(route('campaigns.show', $campaign))->assertOk();
    }

    public function test_campaign_show_route_requires_authentication(): void
    {
        [$user, $merchant] = $this->makeVerifiedMerchant();
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id]);

        $this->get(route('campaigns.show', $campaign))->assertRedirect(route('login'));
    }

    public function test_campaign_view_link_from_dashboard_resolves_correctly(): void
    {
        [$user, $merchant] = $this->makeVerifiedMerchant();
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id]);

        $expectedUrl = route('campaigns.show', $campaign);

        $this->assertStringContainsString('/campaigns/' . $campaign->id, $expectedUrl);
    }

    // ── Merchant model settings accessor ──────────────────────────────────────

    public function test_merchant_settings_returns_empty_array_when_null_in_db(): void
    {
        $merchant = Merchant::factory()->create(['settings' => null]);

        $this->assertIsArray($merchant->settings);
        $this->assertEmpty($merchant->settings);
    }

    public function test_merchant_settings_preserves_existing_values(): void
    {
        $merchant = Merchant::factory()->create([
            'settings' => ['locale' => 'th', 'counter_mode' => true],
        ]);

        $this->assertSame('th', $merchant->settings['locale']);
        $this->assertTrue($merchant->settings['counter_mode']);
    }

    public function test_merchant_fresh_from_db_settings_returns_array_when_null(): void
    {
        $merchant = Merchant::factory()->create(['settings' => null]);

        // Reload from DB to ensure the DB-level NULL comes through the accessor
        $fresh = Merchant::find($merchant->id);

        $this->assertIsArray($fresh->settings);
    }
}
