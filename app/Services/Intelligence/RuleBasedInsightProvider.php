<?php

namespace App\Services\Intelligence;

use App\Contracts\InsightProviderInterface;
use App\Enums\CampaignStatus;
use App\Enums\MemberStatus;
use App\Enums\SubscriptionPlan;
use App\Models\Merchant;

class RuleBasedInsightProvider implements InsightProviderInterface
{
    public function analyze(Merchant $merchant): array
    {
        // ── Load data once ───────────────────────────────────────
        $memberCount = $merchant->members()
            ->where('status', MemberStatus::Active)
            ->count();

        $activePrograms = $merchant->loyaltyPrograms()
            ->where('status', CampaignStatus::Active)
            ->with('rewards')
            ->get();

        $activeProgramCount  = $activePrograms->count();
        $activeRewardCount   = $activePrograms->sum(fn ($p) => $p->rewards->count());
        $lowestThreshold     = $activePrograms->flatMap(fn ($p) => $p->rewards)->min('points_required');
        $firstActiveProgram  = $activePrograms->first();

        $totalProgramCount       = $merchant->loyaltyPrograms()->count();
        $totalBirthdayRewardCount = $merchant->birthdayRewards()->count();

        $purchases30d     = $merchant->transactions()->where('created_at', '>=', now()->subDays(30))->count();
        $totalRedemptions = $merchant->redemptions()->count();

        $inactiveCount = $merchant->members()
            ->where('status', MemberStatus::Active)
            ->where('joined_at', '<', now()->subDays(45))
            ->where(function ($q) {
                $q->whereNull('last_activity_at')
                  ->orWhere('last_activity_at', '<', now()->subDays(45));
            })
            ->count();

        $nearRewardCount = 0;
        if ($lowestThreshold && $lowestThreshold > 0) {
            $nearMin = (int) floor($lowestThreshold * 0.8);
            $nearRewardCount = $merchant->members()
                ->where('status', MemberStatus::Active)
                ->where('total_points', '>=', $nearMin)
                ->where('total_points', '<', $lowestThreshold)
                ->count();
        }

        $birthdayCount = $merchant->members()
            ->where('status', MemberStatus::Active)
            ->whereNotNull('birthday')
            ->get(['id', 'birthday'])
            ->filter(fn ($m) => $m->birthday->copy()->setYear(now()->year)->between(
                now()->startOfDay(),
                now()->addDays(7)->endOfDay()
            ))
            ->count();

        $newMembersMonth = $merchant->members()
            ->where('joined_at', '>=', now()->startOfMonth())
            ->count();

        $isPaid = $merchant->subscription_plan !== null
               && $merchant->subscription_plan !== SubscriptionPlan::Free;

        // ── Build outputs ────────────────────────────────────────
        return [
            'insights'      => $this->buildInsights(
                $memberCount, $activeProgramCount, $activeRewardCount,
                $inactiveCount, $nearRewardCount, $birthdayCount, $newMembersMonth
            ),
            'health_score'  => $this->buildHealthScore(
                $activeProgramCount, $activeRewardCount, $memberCount,
                $purchases30d, $totalRedemptions, $isPaid
            ),
            'opportunities' => $this->buildOpportunities(
                $merchant, $totalProgramCount, $activeProgramCount, $activeRewardCount,
                $memberCount, $totalBirthdayRewardCount, $firstActiveProgram
            ),
        ];
    }

    private function buildInsights(
        int $memberCount,
        int $activeProgramCount,
        int $activeRewardCount,
        int $inactiveCount,
        int $nearRewardCount,
        int $birthdayCount,
        int $newMembersMonth
    ): array {
        $insights = [];

        if ($activeProgramCount === 0 && $memberCount > 0) {
            $insights[] = [
                'icon'         => 'bi-star-fill',
                'priority'     => 'high',
                'text'         => __('intelligence.insight_no_campaign'),
                'action_label' => __('intelligence.action_create_campaign'),
                'action_url'   => route('campaigns.create'),
            ];
        }

        if ($activeProgramCount > 0 && $activeRewardCount === 0) {
            $insights[] = [
                'icon'         => 'bi-gift',
                'priority'     => 'high',
                'text'         => __('intelligence.insight_no_rewards'),
                'action_label' => __('intelligence.action_view_campaigns'),
                'action_url'   => route('campaigns.index'),
            ];
        }

        if ($inactiveCount > 0) {
            $insights[] = [
                'icon'         => 'bi-person-x',
                'priority'     => 'high',
                'text'         => trans_choice('intelligence.insight_inactive', $inactiveCount, ['count' => $inactiveCount]),
                'action_label' => __('intelligence.action_view_members'),
                'action_url'   => route('members'),
            ];
        }

        if ($nearRewardCount > 0) {
            $insights[] = [
                'icon'         => 'bi-trophy',
                'priority'     => 'high',
                'text'         => trans_choice('intelligence.insight_near_reward', $nearRewardCount, ['count' => $nearRewardCount]),
                'action_label' => __('intelligence.action_view_members'),
                'action_url'   => route('members'),
            ];
        }

        if ($birthdayCount > 0) {
            $insights[] = [
                'icon'         => 'bi-cake2',
                'priority'     => 'medium',
                'text'         => trans_choice('intelligence.insight_birthday', $birthdayCount, ['count' => $birthdayCount]),
                'action_label' => __('intelligence.action_view_members'),
                'action_url'   => route('members'),
            ];
        }

        if ($newMembersMonth > 0) {
            $insights[] = [
                'icon'         => 'bi-people-fill',
                'priority'     => 'low',
                'text'         => trans_choice('intelligence.insight_new_members', $newMembersMonth, ['count' => $newMembersMonth]),
                'action_label' => null,
                'action_url'   => null,
            ];
        }

        $order = ['high' => 0, 'medium' => 1, 'low' => 2];
        usort($insights, fn ($a, $b) => $order[$a['priority']] <=> $order[$b['priority']]);

        return array_slice($insights, 0, 5);
    }

    private function buildHealthScore(
        int $activeProgramCount,
        int $activeRewardCount,
        int $memberCount,
        int $purchases30d,
        int $totalRedemptions,
        bool $isPaid
    ): array {
        $score = 0;

        if ($activeProgramCount >= 1) { $score += 15; }
        if ($activeRewardCount >= 1)  { $score += 10; }

        $score += match (true) {
            $memberCount >= 100 => 20,
            $memberCount >= 50  => 15,
            $memberCount >= 10  => 10,
            $memberCount >= 1   => 5,
            default             => 0,
        };

        $score += match (true) {
            $purchases30d >= 100 => 25,
            $purchases30d >= 30  => 20,
            $purchases30d >= 10  => 15,
            $purchases30d >= 1   => 5,
            default              => 0,
        };

        if ($totalRedemptions > 0) { $score += 15; }
        if ($isPaid)               { $score += 15; }

        $label = match (true) {
            $score >= 80 => 'excellent',
            $score >= 60 => 'good',
            $score >= 40 => 'needs_attention',
            $score >= 20 => 'getting_started',
            default      => 'new_business',
        };

        return [
            'score'       => $score,
            'label'       => $label,
            'label_text'  => __("intelligence.health_{$label}"),
            'explanation' => __("intelligence.health_{$label}_explanation"),
            'badge_class' => match ($label) {
                'excellent'       => 'bg-success',
                'good'            => 'bg-primary',
                'needs_attention' => 'bg-warning text-dark',
                default           => 'bg-secondary',
            },
        ];
    }

    private function buildOpportunities(
        Merchant $merchant,
        int $totalProgramCount,
        int $activeProgramCount,
        int $activeRewardCount,
        int $memberCount,
        int $totalBirthdayRewardCount,
        mixed $firstActiveProgram
    ): array {
        $opportunities = [];

        if ($totalProgramCount === 0) {
            $opportunities[] = [
                'icon'         => 'bi-star',
                'title'        => __('intelligence.opp_create_campaign'),
                'description'  => __('intelligence.opp_create_campaign_desc'),
                'action_label' => __('intelligence.opp_action_create'),
                'action_url'   => route('campaigns.create'),
                'priority'     => 'high',
            ];
        }

        if ($activeProgramCount > 0 && $activeRewardCount === 0) {
            $rewardUrl = $firstActiveProgram
                ? route('campaigns.rewards.create', $firstActiveProgram)
                : route('campaigns.index');

            $opportunities[] = [
                'icon'         => 'bi-gift',
                'title'        => __('intelligence.opp_add_rewards'),
                'description'  => __('intelligence.opp_add_rewards_desc'),
                'action_label' => __('intelligence.opp_action_add_reward'),
                'action_url'   => $rewardUrl,
                'priority'     => 'high',
            ];
        }

        if ($totalBirthdayRewardCount === 0 && $memberCount > 0) {
            $opportunities[] = [
                'icon'         => 'bi-cake2',
                'title'        => __('intelligence.opp_birthday_reward'),
                'description'  => __('intelligence.opp_birthday_reward_desc'),
                'action_label' => __('intelligence.opp_action_view_campaigns'),
                'action_url'   => route('campaigns.index'),
                'priority'     => 'medium',
            ];
        }

        if ($memberCount < 10) {
            $opportunities[] = [
                'icon'         => 'bi-people',
                'title'        => __('intelligence.opp_grow_members'),
                'description'  => __('intelligence.opp_grow_members_desc'),
                'action_label' => __('intelligence.opp_action_add_member'),
                'action_url'   => route('members.create'),
                'priority'     => 'low',
            ];
        }

        return $opportunities;
    }
}
