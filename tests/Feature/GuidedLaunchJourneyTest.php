<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * MERCHANT-READY-001 / MR-003 — guided launch journey: every completed
 * step explains why it matters and recommends the next one; encouraging
 * progress copy; a celebration with quick actions at 100%.
 */
class GuidedLaunchJourneyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        // Profile, logo and store URL complete → the journey continues at
        // "create your first campaign" (deterministic first-incomplete rule).
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'business_type'           => 'Retail',
            'logo_path'               => 'logos/x.png',
            'settings'                => ['locale' => 'en'],
        ]);
    }

    private function follow($response)
    {
        return $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get($response->headers->get('Location'));
    }

    // ── Part 3: success experience — why + next ─────────────────────────

    public function test_creating_a_campaign_shows_why_and_recommends_next_step(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->post(route('campaigns.store', absolute: false), [
                'name' => 'Coffee Points', 'type' => 'points', 'status' => 'active',
            ]);

        $this->follow($response)
            ->assertOk()
            ->assertSee(__('messages.campaign_created', [], 'en'))
            ->assertSee(__('launch_check.why_campaign', [], 'en'))
            ->assertSee(__('launch_check.next_title', [], 'en'))
            // campaign done → reward is the deterministic next step
            ->assertSee(__('launch_check.action_reward', [], 'en'));
    }

    public function test_adding_a_member_shows_why_and_recommends_next_step(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->post(route('members.store', absolute: false), [
                'name' => 'Somchai Test', 'phone' => '0812345678', 'birthday' => '1990-05-15',
            ]);

        $this->follow($response)
            ->assertOk()
            ->assertSee(__('messages.member_created', [], 'en'))
            ->assertSee(__('launch_check.why_member', [], 'en'))
            ->assertSee(__('launch_check.next_title', [], 'en'));
    }

    public function test_completing_the_final_step_celebrates_instead_of_next_action(): void
    {
        // Everything done except the first member
        $this->merchant->update([
            'logo_path' => 'logos/x.png',
            'settings'  => array_merge($this->merchant->settings, [
                'launch_flags' => ['qr_poster_viewed' => true, 'storefront_visited' => true],
            ]),
        ]);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $campaign->id]);

        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->post(route('members.store', absolute: false), [
                'name' => 'Last Step', 'phone' => '0899999999', 'birthday' => '1991-01-01',
            ]);

        $this->follow($response)
            ->assertOk()
            ->assertSee(__('launch_check.why_member', [], 'en'))
            ->assertSee(__('launch_check.launch_ready', [], 'en'))
            ->assertSee(__('launch_check.celebrate_dashboard_cta', [], 'en'));
    }

    // ── Part 4: progress experience ─────────────────────────────────────

    public function test_dashboard_shows_encouraging_steps_left_copy(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('steps to go');
    }

    // ── Part 5: first launch celebration ────────────────────────────────

    public function test_dashboard_celebrates_at_100_percent_with_quick_actions(): void
    {
        $this->merchant->update([
            'logo_path' => 'logos/x.png',
            'settings'  => array_merge($this->merchant->settings, [
                'installed_apps' => ['commerce'],
                'launch_flags'   => ['qr_poster_viewed' => true, 'storefront_visited' => true],
            ]),
        ]);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $campaign->id]);
        Member::factory()->create(['merchant_id' => $this->merchant->id]);
        Product::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(__('launch_check.celebrate_heading', [], 'en'))
            ->assertSee(__('launch_check.celebrate_body', [], 'en'))
            ->assertSee(__('launch_check.qa_storefront', [], 'en'))
            ->assertSee(__('launch_check.qa_poster', [], 'en'))
            ->assertSee(__('launch_check.qa_member', [], 'en'))
            ->assertSee(__('launch_check.qa_guide', [], 'en'))
            ->assertSee(route('launch-kit.poster', absolute: false))
            ->assertSee(route('help.index', absolute: false));
    }

    // ── Onboarding handoff ──────────────────────────────────────────────

    public function test_onboarding_finish_hands_off_to_the_launch_plan(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('onboarding.finish', absolute: false))
            ->assertOk()
            ->assertSee(__('onboarding.see_launch_plan', [], 'en'))
            ->assertSee(route('dashboard', absolute: false));
    }

    // ── Localization of the journey flashes ─────────────────────────────

    public function test_thai_merchant_sees_thai_success_and_guidance(): void
    {
        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['locale' => 'th'])]);

        $response = $this->actingAs($this->user->fresh())
            ->post(route('campaigns.store', absolute: false), [
                'name' => 'แต้มกาแฟ', 'type' => 'points', 'status' => 'active',
            ]);

        $this->actingAs($this->user->fresh())
            ->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee(__('messages.campaign_created', [], 'th'))
            ->assertSee(__('launch_check.why_campaign', [], 'th'));
    }
}
