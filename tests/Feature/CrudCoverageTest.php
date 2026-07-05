<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MemberStatus;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Redemption;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudCoverageTest extends TestCase
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
        ]);
    }

    private function makeCampaign(array $attributes = []): LoyaltyProgram
    {
        return LoyaltyProgram::factory()->create(array_merge([
            'merchant_id' => $this->merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
            'settings'    => ['spend_amount' => 100, 'points_awarded' => 1],
        ], $attributes));
    }

    // ── Campaign CRUD ────────────────────────────────────────────────────

    public function test_campaign_can_be_created(): void
    {
        $this->actingAs($this->user)->post(route('campaigns.store'), [
            'name'   => 'Coffee Points',
            'type'   => 'points',
            'status' => 'draft',
        ])->assertRedirect();

        $this->assertDatabaseHas('loyalty_programs', [
            'merchant_id' => $this->merchant->id,
            'name'        => 'Coffee Points',
        ]);
    }

    public function test_campaign_create_requires_name_and_type(): void
    {
        $this->actingAs($this->user)
            ->from(route('campaigns.create'))
            ->post(route('campaigns.store'), [])
            ->assertSessionHasErrors(['name', 'type', 'status']);
    }

    public function test_campaign_can_be_updated(): void
    {
        $campaign = $this->makeCampaign(['name' => 'Old Name']);

        $this->actingAs($this->user)->put(route('campaigns.update', $campaign), [
            'name'   => 'New Name',
            'type'   => 'points',
            'status' => 'active',
        ])->assertRedirect();

        $this->assertSame('New Name', $campaign->fresh()->name);
    }

    public function test_campaign_can_be_archived(): void
    {
        $campaign = $this->makeCampaign();

        $this->actingAs($this->user)
            ->delete(route('campaigns.archive', $campaign))
            ->assertRedirect();

        $this->assertSoftDeleted('loyalty_programs', ['id' => $campaign->id]);
    }

    public function test_campaign_of_another_merchant_is_not_accessible(): void
    {
        $otherUser     = User::factory()->create(['email_verified_at' => now()]);
        $otherMerchant = Merchant::factory()->create(['user_id' => $otherUser->id]);
        $campaign      = LoyaltyProgram::factory()->create([
            'merchant_id' => $otherMerchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
        ]);

        $this->actingAs($this->user)
            ->get(route('campaigns.show', $campaign))
            ->assertForbidden();
    }

    // ── Member CRUD ──────────────────────────────────────────────────────

    public function test_member_can_be_created(): void
    {
        $this->actingAs($this->user)->post(route('members.store'), [
            'name'        => 'Somchai Test',
            'phone'       => '0812345678',
            'birthday'    => '1990-05-15',
            'postal_code' => '10110',
        ])->assertRedirect();

        $this->assertDatabaseHas('members', [
            'merchant_id' => $this->merchant->id,
            'name'        => 'Somchai Test',
            'postal_code' => '10110',
        ]);
    }

    public function test_member_create_requires_name_phone_birthday(): void
    {
        $this->actingAs($this->user)
            ->from(route('members.create'))
            ->post(route('members.store'), [])
            ->assertSessionHasErrors(['name', 'phone', 'birthday']);
    }

    public function test_member_phone_must_be_unique_per_merchant(): void
    {
        Member::factory()->create([
            'merchant_id' => $this->merchant->id,
            'phone'       => '0812345678',
        ]);

        $this->actingAs($this->user)->post(route('members.store'), [
            'name'     => 'Duplicate Phone',
            'phone'    => '0812345678',
            'birthday' => '1990-05-15',
        ])->assertSessionHasErrors(['phone']);
    }

    public function test_member_can_be_updated(): void
    {
        $member = Member::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->actingAs($this->user)->put(route('members.update', $member), [
            'name'     => 'Updated Name',
            'phone'    => $member->phone,
            'birthday' => '1990-05-15',
        ])->assertRedirect();

        $this->assertSame('Updated Name', $member->fresh()->name);
    }

    public function test_member_can_be_archived(): void
    {
        $member = Member::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->actingAs($this->user)
            ->delete(route('members.archive', $member))
            ->assertRedirect();

        $this->assertSoftDeleted('members', ['id' => $member->id]);
    }

    // ── Reward CRUD ──────────────────────────────────────────────────────

    public function test_reward_can_be_created(): void
    {
        $campaign = $this->makeCampaign();

        $this->actingAs($this->user)->post(route('campaigns.rewards.store', $campaign), [
            'name'            => 'Free Coffee',
            'type'            => 'free_item',
            'unlimited'       => 1,
            'status'          => 'active',
            'points_required' => 100,
        ])->assertRedirect();

        $this->assertDatabaseHas('rewards', [
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $campaign->id,
            'name'               => 'Free Coffee',
        ]);
    }

    public function test_reward_requires_points_for_points_campaign(): void
    {
        $campaign = $this->makeCampaign();

        $this->actingAs($this->user)->post(route('campaigns.rewards.store', $campaign), [
            'name'      => 'No Points Reward',
            'type'      => 'free_item',
            'unlimited' => 1,
            'status'    => 'active',
        ])->assertSessionHasErrors(['points_required']);
    }

    public function test_reward_can_be_updated(): void
    {
        $campaign = $this->makeCampaign();
        $reward   = Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $campaign->id,
            'points_required'    => 100,
        ]);

        $this->actingAs($this->user)->put(route('campaigns.rewards.update', [$campaign, $reward]), [
            'name'            => 'Renamed Reward',
            'type'            => 'free_item',
            'unlimited'       => 1,
            'status'          => 'active',
            'points_required' => 150,
        ])->assertRedirect();

        $this->assertSame('Renamed Reward', $reward->fresh()->name);
    }

    public function test_reward_can_be_archived(): void
    {
        $campaign = $this->makeCampaign();
        $reward   = Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $campaign->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('campaigns.rewards.archive', [$campaign, $reward]))
            ->assertRedirect();

        $this->assertSoftDeleted('rewards', ['id' => $reward->id]);
    }

    // ── Purchase recording ───────────────────────────────────────────────

    public function test_purchase_awards_points_and_updates_balance(): void
    {
        $this->makeCampaign();
        $member = Member::factory()->create([
            'merchant_id'  => $this->merchant->id,
            'status'       => MemberStatus::Active,
            'total_points' => 0,
        ]);

        $this->actingAs($this->user)->post(route('members.purchases.store', $member), [
            'purchase_amount' => 500,
        ])->assertRedirect();

        $member->refresh();
        $this->assertSame(5, $member->total_points);
        $this->assertDatabaseHas('transactions', [
            'member_id' => $member->id,
            'points'    => 5,
        ]);
    }

    public function test_purchase_rejected_without_active_campaign(): void
    {
        $member = Member::factory()->create([
            'merchant_id' => $this->merchant->id,
            'status'      => MemberStatus::Active,
        ]);

        $this->actingAs($this->user)->post(route('members.purchases.store', $member), [
            'purchase_amount' => 500,
        ])->assertSessionHasErrors(['purchase']);
    }

    public function test_purchase_rejected_for_archived_member(): void
    {
        $this->makeCampaign();
        $member = Member::factory()->create([
            'merchant_id' => $this->merchant->id,
            'status'      => MemberStatus::Active,
        ]);
        $member->delete();

        $this->actingAs($this->user)->post(route('members.purchases.store', $member), [
            'purchase_amount' => 500,
        ])->assertSessionHasErrors(['purchase']);
    }

    public function test_purchase_rejected_for_other_merchants_member(): void
    {
        $this->makeCampaign();
        $otherUser     = User::factory()->create(['email_verified_at' => now()]);
        $otherMerchant = Merchant::factory()->create(['user_id' => $otherUser->id]);
        $member        = Member::factory()->create([
            'merchant_id' => $otherMerchant->id,
            'status'      => MemberStatus::Active,
        ]);

        $this->actingAs($this->user)->post(route('members.purchases.store', $member), [
            'purchase_amount' => 500,
        ])->assertForbidden();
    }

    // ── Redemption flow ──────────────────────────────────────────────────

    public function test_redemption_deducts_points_and_creates_record(): void
    {
        $campaign = $this->makeCampaign();
        $reward   = Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $campaign->id,
            'status'             => 'active',
            'points_required'    => 100,
        ]);
        $member = Member::factory()->create([
            'merchant_id'  => $this->merchant->id,
            'status'       => MemberStatus::Active,
            'total_points' => 250,
        ]);

        $this->actingAs($this->user)->post(route('members.redemptions.store', $member), [
            'reward_id' => $reward->id,
        ])->assertRedirect();

        $this->assertSame(150, $member->fresh()->total_points);
        $this->assertSame(1, Redemption::where('member_id', $member->id)->count());
    }

    public function test_redemption_rejected_when_insufficient_points(): void
    {
        $campaign = $this->makeCampaign();
        $reward   = Reward::factory()->create([
            'merchant_id'        => $this->merchant->id,
            'loyalty_program_id' => $campaign->id,
            'status'             => 'active',
            'points_required'    => 1000,
        ]);
        $member = Member::factory()->create([
            'merchant_id'  => $this->merchant->id,
            'status'       => MemberStatus::Active,
            'total_points' => 50,
        ]);

        $this->actingAs($this->user)->post(route('members.redemptions.store', $member), [
            'reward_id' => $reward->id,
        ])->assertSessionHasErrors(['redemption']);

        $this->assertSame(50, $member->fresh()->total_points);
    }

    // ── Onboarding wizard ────────────────────────────────────────────────

    public function test_onboarding_business_info_creates_merchant(): void
    {
        $newUser = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($newUser)->post(route('onboarding.business-info.store'), [
            'name'          => 'New Salon',
            'business_type' => 'Hair Salon',
        ])->assertRedirect(route('onboarding.business-settings', absolute: false));

        $this->assertDatabaseHas('merchants', [
            'user_id' => $newUser->id,
            'name'    => 'New Salon',
        ]);
    }

    public function test_onboarding_business_info_requires_name_and_type(): void
    {
        $newUser = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($newUser)
            ->post(route('onboarding.business-info.store'), [])
            ->assertSessionHasErrors(['name', 'business_type']);
    }
}
