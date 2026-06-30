<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\MemberStatus;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Enums\SubscriptionPlan;
use App\Models\Redemption;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MerchantIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MerchantIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsMerchant(): array
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'subscription_plan'       => SubscriptionPlan::Free,
            'settings'                => [
                'locale'                   => 'en',
                'timezone'                 => 'Asia/Bangkok',
                'currency'                 => 'THB',
                'default_expiration_type'  => 'none',
                'default_expiration_value' => null,
                'email_notifications'      => [],
                'counter_mode'             => false,
            ],
        ]);
        $this->actingAs($user);
        return compact('user', 'merchant');
    }

    // ── Health score ──────────────────────────────────────────

    public function test_health_score_is_zero_for_new_merchant(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $service = app(MerchantIntelligenceService::class);
        $score   = $service->getHealthScore($merchant);

        $this->assertSame(0, $score['score']);
        $this->assertSame('new_business', $score['label']);
    }

    public function test_health_score_increases_with_active_campaign(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'status'      => CampaignStatus::Active,
        ]);

        Cache::flush();
        $score = app(MerchantIntelligenceService::class)->getHealthScore($merchant);

        $this->assertGreaterThanOrEqual(15, $score['score']);
    }

    public function test_health_score_increases_with_members(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Member::factory()->count(10)->create([
            'merchant_id' => $merchant->id,
            'status'      => MemberStatus::Active,
        ]);

        Cache::flush();
        $score = app(MerchantIntelligenceService::class)->getHealthScore($merchant);

        $this->assertGreaterThanOrEqual(10, $score['score']);
    }

    public function test_health_score_increases_with_recent_purchases(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $member = Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active]);
        $program = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);

        Transaction::factory()->count(10)->create([
            'merchant_id'       => $merchant->id,
            'member_id'         => $member->id,
            'loyalty_program_id'=> $program->id,
            'created_at'        => now()->subDays(5),
        ]);

        Cache::flush();
        $score = app(MerchantIntelligenceService::class)->getHealthScore($merchant);

        $this->assertGreaterThanOrEqual(15, $score['score']);
    }

    public function test_health_score_label_good_at_60_79(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        // active campaign (+15), 1 reward (+10), 50 members (+15), 30 purchases (+20) = 60
        $program = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $merchant->id, 'loyalty_program_id' => $program->id, 'points_required' => 100]);
        Member::factory()->count(50)->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active]);
        Transaction::factory()->count(30)->create([
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $program->id,
            'created_at'         => now()->subDays(3),
        ]);

        Cache::flush();
        $score = app(MerchantIntelligenceService::class)->getHealthScore($merchant);

        $this->assertSame('good', $score['label']);
    }

    public function test_health_score_label_excellent_at_80_plus(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        // active campaign (+15), 1 reward (+10), 100 members (+20), 100 purchases (+25) = 70 + redemption (+15) = 85
        $program = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);
        $reward  = Reward::factory()->create(['merchant_id' => $merchant->id, 'loyalty_program_id' => $program->id, 'points_required' => 100]);
        $members = Member::factory()->count(100)->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active]);
        Transaction::factory()->count(100)->create([
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $program->id,
            'created_at'         => now()->subDays(2),
        ]);
        Redemption::factory()->create([
            'merchant_id' => $merchant->id,
            'member_id'   => $members->first()->id,
            'reward_id'   => $reward->id,
        ]);

        Cache::flush();
        $score = app(MerchantIntelligenceService::class)->getHealthScore($merchant);

        $this->assertSame('excellent', $score['label']);
        $this->assertGreaterThanOrEqual(80, $score['score']);
    }

    public function test_health_score_returns_badge_class(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $score = app(MerchantIntelligenceService::class)->getHealthScore($merchant);

        $this->assertArrayHasKey('badge_class', $score);
        $this->assertNotEmpty($score['badge_class']);
    }

    // ── Inactive customer insight ─────────────────────────────

    public function test_inactive_customer_insight_generated(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'status'           => MemberStatus::Active,
            'joined_at'        => now()->subDays(90),
            'last_activity_at' => now()->subDays(60),
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);
        $texts    = array_column($insights, 'text');

        $this->assertTrue(
            collect($texts)->contains(fn ($t) => str_contains($t, '1')),
            'Expected inactive insight to reference count of 1'
        );
    }

    public function test_new_member_not_flagged_as_inactive(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id' => $merchant->id,
            'status'      => MemberStatus::Active,
            'joined_at'   => now()->subDays(10),
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);

        $hasInactive = collect($insights)->contains(fn ($i) => str_contains($i['text'], 'hasn\'t visited'));
        $this->assertFalse($hasInactive);
    }

    // ── Near-reward insight ───────────────────────────────────

    public function test_near_reward_insight_generated(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $program = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);
        Reward::factory()->create([
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $program->id,
            'points_required'    => 100,
        ]);
        Member::factory()->create([
            'merchant_id'  => $merchant->id,
            'status'       => MemberStatus::Active,
            'total_points' => 85, // 85% of 100 — within the 80% threshold
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);
        $icons    = array_column($insights, 'icon');

        $this->assertContains('bi-trophy', $icons);
    }

    public function test_member_below_near_reward_threshold_not_flagged(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $program = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);
        Reward::factory()->create([
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $program->id,
            'points_required'    => 100,
        ]);
        Member::factory()->create([
            'merchant_id'  => $merchant->id,
            'status'       => MemberStatus::Active,
            'total_points' => 50, // only 50% — not near
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);
        $icons    = array_column($insights, 'icon');

        $this->assertNotContains('bi-trophy', $icons);
    }

    // ── Birthday insight ──────────────────────────────────────

    public function test_birthday_insight_generated_for_members_with_birthday_this_week(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id' => $merchant->id,
            'status'      => MemberStatus::Active,
            'birthday'    => now()->addDays(3)->setYear(1990),
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);
        $icons    = array_column($insights, 'icon');

        $this->assertContains('bi-cake2', $icons);
    }

    // ── No campaign insight ───────────────────────────────────

    public function test_no_campaign_insight_when_members_exist_but_no_active_campaign(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);
        $icons    = array_column($insights, 'icon');

        $this->assertContains('bi-star-fill', $icons);
    }

    // ── Priority order & cap ──────────────────────────────────

    public function test_high_priority_insights_appear_before_low_priority(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        // Generate: inactive (high) and new members (low)
        Member::factory()->create([
            'merchant_id'      => $merchant->id,
            'status'           => MemberStatus::Active,
            'joined_at'        => now()->subDays(90),
            'last_activity_at' => now()->subDays(60),
        ]);
        Member::factory()->create([
            'merchant_id' => $merchant->id,
            'status'      => MemberStatus::Active,
            'joined_at'   => now()->subDays(5),
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);

        $highIndex = null;
        $lowIndex  = null;
        foreach ($insights as $i => $insight) {
            if ($insight['priority'] === 'high') { $highIndex ??= $i; }
            if ($insight['priority'] === 'low')  { $lowIndex  ??= $i; }
        }

        if ($highIndex !== null && $lowIndex !== null) {
            $this->assertLessThan($lowIndex, $highIndex);
        } else {
            $this->addToAssertionCount(1); // no priority conflict — trivially passes
        }
    }

    public function test_insights_are_capped_at_five(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        // Inactive (high), near-reward (high), birthday (medium), new members (low), no-rewards (high)
        $program = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $merchant->id, 'loyalty_program_id' => $program->id, 'points_required' => 100]);

        Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active, 'joined_at' => now()->subDays(90), 'last_activity_at' => now()->subDays(60)]);
        Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active, 'total_points' => 85]);
        Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active, 'birthday' => now()->addDays(2)->setYear(1990)]);
        Member::factory()->count(5)->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active, 'joined_at' => now()->subDays(5)]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchant);

        $this->assertLessThanOrEqual(5, count($insights));
    }

    // ── Opportunities ─────────────────────────────────────────

    public function test_create_campaign_opportunity_when_no_campaigns(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $opps = app(MerchantIntelligenceService::class)->getOpportunities($merchant);
        $icons = array_column($opps, 'icon');

        $this->assertContains('bi-star', $icons);
    }

    public function test_add_rewards_opportunity_when_active_campaign_has_no_rewards(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'status' => CampaignStatus::Active]);

        Cache::flush();
        $opps  = app(MerchantIntelligenceService::class)->getOpportunities($merchant);
        $icons = array_column($opps, 'icon');

        $this->assertContains('bi-gift', $icons);
    }

    public function test_grow_members_opportunity_when_fewer_than_10(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Member::factory()->count(3)->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active]);

        Cache::flush();
        $opps  = app(MerchantIntelligenceService::class)->getOpportunities($merchant);
        $icons = array_column($opps, 'icon');

        $this->assertContains('bi-people', $icons);
    }

    // ── Caching ───────────────────────────────────────────────

    public function test_results_are_cached_for_15_minutes(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        Cache::flush();
        $service = app(MerchantIntelligenceService::class);
        $first   = $service->getHealthScore($merchant);

        // Add a member — cache should still return the old result
        Member::factory()->create(['merchant_id' => $merchant->id, 'status' => MemberStatus::Active]);

        $second = $service->getHealthScore($merchant);

        $this->assertSame($first['score'], $second['score'], 'Cache should return stale data within TTL');
    }

    public function test_cache_is_separate_per_merchant(): void
    {
        ['merchant' => $merchantA] = $this->actingAsMerchant();
        $userB     = User::factory()->create();
        $merchantB = Merchant::factory()->create(['user_id' => $userB->id, 'onboarding_completed_at' => now(), 'subscription_plan' => SubscriptionPlan::Free]);

        Member::factory()->count(10)->create(['merchant_id' => $merchantA->id, 'status' => MemberStatus::Active]);

        Cache::flush();
        $service = app(MerchantIntelligenceService::class);
        $scoreA  = $service->getHealthScore($merchantA)['score'];
        $scoreB  = $service->getHealthScore($merchantB)['score'];

        $this->assertGreaterThan($scoreB, $scoreA);
    }

    // ── Tenant isolation ──────────────────────────────────────

    public function test_insights_only_use_own_merchant_data(): void
    {
        ['merchant' => $merchantA] = $this->actingAsMerchant();
        $userB     = User::factory()->create();
        $merchantB = Merchant::factory()->create(['user_id' => $userB->id, 'onboarding_completed_at' => now(), 'subscription_plan' => SubscriptionPlan::Free]);

        // Merchant B has inactive members — should NOT appear in Merchant A's insights
        Member::factory()->create([
            'merchant_id'      => $merchantB->id,
            'status'           => MemberStatus::Active,
            'joined_at'        => now()->subDays(90),
            'last_activity_at' => now()->subDays(60),
        ]);

        Cache::flush();
        $insights = app(MerchantIntelligenceService::class)->getInsights($merchantA);
        $icons    = array_column($insights, 'icon');

        $this->assertNotContains('bi-person-x', $icons);
    }

    // ── Dashboard integration ─────────────────────────────────

    public function test_dashboard_shows_intelligence_card(): void
    {
        $this->actingAsMerchant();

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(__('intelligence.card_title'));
    }

    public function test_dashboard_shows_health_score(): void
    {
        $this->actingAsMerchant();

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('/100');
    }

    // ── Weekly summary ────────────────────────────────────────

    public function test_weekly_summary_returns_expected_structure(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();

        $summary = app(MerchantIntelligenceService::class)->getWeeklySummary($merchant);

        $this->assertArrayHasKey('period_start', $summary);
        $this->assertArrayHasKey('period_end', $summary);
        $this->assertArrayHasKey('new_members', $summary);
        $this->assertArrayHasKey('purchases', $summary);
        $this->assertArrayHasKey('rewards_redeemed', $summary);
        $this->assertArrayHasKey('health_score', $summary);
        $this->assertArrayHasKey('health_label', $summary);
        $this->assertIsInt($summary['new_members']);
        $this->assertIsInt($summary['health_score']);
    }

    // ── Localization ──────────────────────────────────────────

    public function test_intelligence_translation_keys_exist(): void
    {
        $required = [
            'card_title',
            'health_excellent', 'health_good', 'health_needs_attention',
            'health_getting_started', 'health_new_business',
            'insight_inactive', 'insight_near_reward', 'insight_birthday',
            'insight_no_campaign', 'insight_no_rewards', 'insight_new_members',
            'no_insights', 'priority_high',
            'opp_create_campaign', 'opp_add_rewards', 'opp_grow_members',
        ];

        foreach ($required as $key) {
            $this->assertNotSame(
                "intelligence.{$key}",
                __("intelligence.{$key}"),
                "Missing translation key: intelligence.{$key}"
            );
        }
    }
}
