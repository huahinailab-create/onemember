<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\RewardType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Proves that Merchant A cannot access or mutate Merchant B's data.
 * Every test creates two independent merchants and verifies 403/404
 * on cross-tenant requests.
 */
class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function merchantWithUser(): array
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);
        return [$user, $merchant];
    }

    private function campaignFor(Merchant $merchant): LoyaltyProgram
    {
        return LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
            'settings'    => [
                'spend_amount'    => 100,
                'points_awarded'  => 1,
                'expiration_type' => 'never',
            ],
        ]);
    }

    private function rewardFor(Merchant $merchant, LoyaltyProgram $campaign): Reward
    {
        return Reward::factory()->create([
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $campaign->id,
            'type'               => RewardType::Custom,
            'points_required'    => 500,
        ]);
    }

    private function memberFor(Merchant $merchant): Member
    {
        return Member::factory()->create(['merchant_id' => $merchant->id]);
    }

    // ---------------------------------------------------------------
    // Members
    // ---------------------------------------------------------------

    public function test_merchant_a_cannot_view_merchant_b_member(): void
    {
        [$userA]      = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $member        = $this->memberFor($merchantB);

        $this->actingAs($userA)
             ->get("/members/{$member->id}")
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_edit_merchant_b_member(): void
    {
        [$userA]      = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $member        = $this->memberFor($merchantB);

        $this->actingAs($userA)
             ->put("/members/{$member->id}", [
                 'name'     => $member->name,
                 'phone'    => $member->phone,
                 'birthday' => $member->birthday->format('Y-m-d'),
             ])
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_archive_merchant_b_member(): void
    {
        [$userA]      = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $member        = $this->memberFor($merchantB);

        $this->actingAs($userA)
             ->delete("/members/{$member->id}")
             ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Campaigns
    // ---------------------------------------------------------------

    public function test_merchant_a_cannot_view_merchant_b_campaign(): void
    {
        [$userA]         = $this->merchantWithUser();
        [, $merchantB]   = $this->merchantWithUser();
        $campaign         = $this->campaignFor($merchantB);

        $this->actingAs($userA)
             ->get("/campaigns/{$campaign->id}")
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_edit_merchant_b_campaign(): void
    {
        [$userA]       = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $campaign       = $this->campaignFor($merchantB);

        $this->actingAs($userA)
             ->put("/campaigns/{$campaign->id}", [
                 'name'   => 'Hacked',
                 'type'   => 'points',
                 'status' => 'active',
             ])
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_archive_merchant_b_campaign(): void
    {
        [$userA]       = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $campaign       = $this->campaignFor($merchantB);

        $this->actingAs($userA)
             ->delete("/campaigns/{$campaign->id}")
             ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Rewards
    // ---------------------------------------------------------------

    public function test_merchant_a_cannot_view_merchant_b_reward(): void
    {
        [$userA]       = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $campaign       = $this->campaignFor($merchantB);
        $reward         = $this->rewardFor($merchantB, $campaign);

        $this->actingAs($userA)
             ->get("/campaigns/{$campaign->id}/rewards/{$reward->id}")
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_edit_merchant_b_reward(): void
    {
        [$userA]       = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $campaign       = $this->campaignFor($merchantB);
        $reward         = $this->rewardFor($merchantB, $campaign);

        $this->actingAs($userA)
             ->put("/campaigns/{$campaign->id}/rewards/{$reward->id}", [
                 'name'               => 'Hacked',
                 'type'               => 'custom',
                 'unlimited'          => true,
                 'quantity_available' => null,
                 'status'             => 'active',
                 'points_required'    => 100,
             ])
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_archive_merchant_b_reward(): void
    {
        [$userA]       = $this->merchantWithUser();
        [, $merchantB] = $this->merchantWithUser();
        $campaign       = $this->campaignFor($merchantB);
        $reward         = $this->rewardFor($merchantB, $campaign);

        $this->actingAs($userA)
             ->delete("/campaigns/{$campaign->id}/rewards/{$reward->id}")
             ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Purchases (earn points for another merchant's member)
    // ---------------------------------------------------------------

    public function test_merchant_a_cannot_record_purchase_for_merchant_b_member(): void
    {
        [$userA, $merchantA] = $this->merchantWithUser();
        [, $merchantB]       = $this->merchantWithUser();
        $campaignA            = $this->campaignFor($merchantA);
        $memberB              = $this->memberFor($merchantB);

        $this->actingAs($userA)
             ->post("/members/{$memberB->id}/purchases", [
                 'purchase_amount' => 500,
             ])
             ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Redemptions
    // ---------------------------------------------------------------

    public function test_merchant_a_cannot_redeem_reward_for_merchant_b_member(): void
    {
        [$userA, $merchantA] = $this->merchantWithUser();
        [, $merchantB]       = $this->merchantWithUser();
        $campaignA            = $this->campaignFor($merchantA);
        $rewardA              = $this->rewardFor($merchantA, $campaignA);
        $memberB              = $this->memberFor($merchantB);

        $this->actingAs($userA)
             ->post("/members/{$memberB->id}/redemptions", [
                 'reward_id' => $rewardA->id,
             ])
             ->assertForbidden();
    }

    public function test_merchant_a_cannot_use_merchant_b_reward_for_own_member(): void
    {
        [$userA, $merchantA] = $this->merchantWithUser();
        [, $merchantB]       = $this->merchantWithUser();
        $campaignB            = $this->campaignFor($merchantB);
        $rewardB              = $this->rewardFor($merchantB, $campaignB);
        $memberA              = $this->memberFor($merchantA);

        // Validation passes (reward exists), but the controller rejects it via "reward not found"
        // because the reward belongs to Merchant B, not Merchant A.
        $this->actingAs($userA)
             ->post("/members/{$memberA->id}/redemptions", [
                 'reward_id' => $rewardB->id,
             ])
             ->assertRedirect();

        // Confirm no redemption was created
        $this->assertDatabaseMissing('redemptions', ['reward_id' => $rewardB->id]);
    }

    // ---------------------------------------------------------------
    // Dashboard — index page must not expose cross-tenant data
    // ---------------------------------------------------------------

    public function test_dashboard_only_shows_own_merchant_data(): void
    {
        [$userA, $merchantA] = $this->merchantWithUser();
        [, $merchantB]       = $this->merchantWithUser();

        // Merchant B has members; Merchant A has none
        $this->memberFor($merchantB);
        $this->memberFor($merchantB);

        $merchantA->update(['onboarding_completed_at' => now()]);

        $response = $this->actingAs($userA)->get('/dashboard');

        $response->assertOk();
        // Merchant A has 0 members — dashboard must reflect that
        $response->assertViewHas('totalActiveMembers', 0);
    }
}
