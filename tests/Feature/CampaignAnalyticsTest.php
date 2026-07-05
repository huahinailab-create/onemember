<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MemberStatus;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;
    private LoyaltyProgram $campaign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
        ]);
        $this->campaign = LoyaltyProgram::factory()->create([
            'merchant_id' => $this->merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
            'settings'    => ['spend_amount' => 100, 'points_awarded' => 1],
        ]);
    }

    public function test_analytics_page_requires_authentication(): void
    {
        $this->get(route('campaigns.analytics', $this->campaign, absolute: false))
            ->assertRedirect();
    }

    public function test_analytics_page_loads_for_owner(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('campaigns.analytics', $this->campaign, absolute: false))
            ->assertOk()
            ->assertSee('Campaign Analytics')
            ->assertSee($this->campaign->name);
    }

    public function test_analytics_page_forbidden_for_other_merchant(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        Merchant::factory()->create(['user_id' => $otherUser->id, 'onboarding_completed_at' => now()]);

        $this->actingAs($otherUser)
            ->get(route('campaigns.analytics', $this->campaign, absolute: false))
            ->assertForbidden();
    }

    public function test_analytics_shows_points_issued_and_redeemed(): void
    {
        $member = Member::factory()->create([
            'merchant_id'  => $this->merchant->id,
            'status'       => MemberStatus::Active,
            'total_points' => 0,
        ]);
        $reward = Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $this->campaign->id,
            'status'             => 'active',
            'points_required'    => 3,
        ]);

        // Earn 5 points via a real purchase, then redeem 3.
        $this->actingAs($this->user)->post(route('members.purchases.store', $member), [
            'purchase_amount' => 500,
        ]);
        $this->actingAs($this->user)->post(route('members.redemptions.store', $member), [
            'reward_id' => $reward->id,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('campaigns.analytics', $this->campaign, absolute: false));

        $response->assertOk()
            ->assertSee('Points Issued')
            ->assertSee('Points Redeemed')
            ->assertSee($member->name)
            ->assertSee($reward->name);
    }

    public function test_analytics_shows_empty_states(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('campaigns.analytics', $this->campaign, absolute: false))
            ->assertOk()
            ->assertSee('No activity recorded for this campaign yet.')
            ->assertSee('No rewards created for this campaign yet.');
    }

    public function test_campaign_page_links_to_analytics(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('campaigns.show', $this->campaign, absolute: false))
            ->assertOk()
            ->assertSee(route('campaigns.analytics', $this->campaign, absolute: false));
    }
}
