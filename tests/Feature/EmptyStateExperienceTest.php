<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * MERCHANT-READY-001 / MR-002 — every empty state answers "what do I do
 * next?": friendly copy, a primary CTA, and a contextual Help Center link.
 */
class EmptyStateExperienceTest extends TestCase
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
            'settings'                => ['locale' => 'en', 'installed_apps' => ['commerce']],
        ]);
    }

    private function get_(string $route, ...$params)
    {
        return $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route($route, ...$params, absolute: false));
    }

    public function test_members_empty_state_has_cta_and_help_link(): void
    {
        $this->get_('members')
            ->assertOk()
            ->assertSee(__('members.empty_title', [], 'en'))
            ->assertSee(__('members.empty_body', [], 'en'))
            ->assertSee(route('members.create', absolute: false))
            ->assertSee('help/context/members.index', false);
    }

    public function test_campaigns_empty_state_has_cta_and_help_link(): void
    {
        $this->get_('campaigns.index')
            ->assertOk()
            ->assertSee(__('campaigns.empty_title', [], 'en'))
            ->assertSee(__('campaigns.empty_state_body', [], 'en'))
            ->assertSee(route('campaigns.create', absolute: false))
            ->assertSee('help/context/campaigns.index', false);
    }

    public function test_rewards_tab_empty_state_has_cta_and_help_link(): void
    {
        $campaign = LoyaltyProgram::factory()->create([
            'merchant_id' => $this->merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
        ]);

        $this->get_('campaigns.show', $campaign)
            ->assertOk()
            ->assertSee(__('campaigns.rewards_empty_title', [], 'en'))
            ->assertSee(__('campaigns.rewards_empty_state_body', [], 'en'))
            ->assertSee(route('campaigns.rewards.create', $campaign, absolute: false))
            ->assertSee('help/context/rewards', false);
    }

    public function test_products_empty_state_has_cta_and_help_link(): void
    {
        $this->get_('commerce.products.index')
            ->assertOk()
            ->assertSee(__('commerce.no_products', [], 'en'))
            ->assertSee(route('commerce.products.create', absolute: false))
            ->assertSee('help/context/commerce.products', false);
    }

    public function test_orders_empty_state_has_storefront_cta_and_help_link(): void
    {
        $this->get_('commerce.orders.index')
            ->assertOk()
            ->assertSee(__('commerce.orders_empty', [], 'en'))
            ->assertSee(__('commerce.view_store_button', [], 'en'))
            ->assertSee(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertSee('help/context/commerce.orders', false);
    }

    public function test_rewards_page_directs_to_campaigns_instead_of_dead_end(): void
    {
        $this->get_('rewards')
            ->assertOk()
            ->assertSee(__('messages.rewards_landing_title', [], 'en'))
            ->assertSee(__('messages.rewards_landing_cta', [], 'en'))
            ->assertSee(route('campaigns.index', absolute: false))
            ->assertSee('help/context/rewards', false)
            ->assertDontSee('under development');
    }

    public function test_reports_placeholder_is_localized_and_friendly(): void
    {
        $this->get_('reports')
            ->assertOk()
            ->assertSee(__('messages.coming_soon_title', ['page' => __('navigation.reports', [], 'en')], 'en'))
            ->assertSee(__('messages.back_to_dashboard', [], 'en'))
            ->assertDontSee('under development');
    }

    public function test_placeholders_render_in_thai(): void
    {
        // The merchant's internal language (BETA-008B) drives the app locale.
        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['locale' => 'th'])]);

        $this->actingAs($this->user->fresh())
            ->get(route('reports', absolute: false))
            ->assertOk()
            ->assertSee(__('messages.coming_soon_body', [], 'th'));
    }
}
