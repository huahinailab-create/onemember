<?php

namespace App\Services;

use App\Models\LoyaltyProgram;
use App\Models\Merchant;

class SubscriptionService
{
    /**
     * The plan key that applies to this merchant right now.
     * During an active trial the merchant has Professional-tier access.
     */
    public function effectivePlanKey(Merchant $merchant): string
    {
        if ($merchant->isOnTrial()) {
            return config('subscriptions.trial.plan', 'professional');
        }

        return $merchant->subscription_plan?->value ?? 'free';
    }

    /**
     * Configured limit for a resource on the merchant's effective plan.
     * Returns null for unlimited.
     */
    public function featureLimit(Merchant $merchant, string $feature): ?int
    {
        $planKey = $this->effectivePlanKey($merchant);

        return config("subscriptions.plans.{$planKey}.limits.{$feature}");
    }

    /**
     * Current usage count for a resource.
     */
    public function usageCount(Merchant $merchant, string $feature): int
    {
        return match ($feature) {
            'members'   => $merchant->members()->count(),
            'campaigns' => $merchant->loyaltyPrograms()->whereNull('deleted_at')->count(),
            'staff_users' => 0, // Not yet implemented
            default     => 0,
        };
    }

    /**
     * Usage count for rewards on a specific campaign.
     */
    public function rewardUsageCount(LoyaltyProgram $campaign): int
    {
        return $campaign->rewards()->whereNull('deleted_at')->count();
    }

    /**
     * Whether the limit for a resource is null (unlimited).
     */
    public function isUnlimited(Merchant $merchant, string $feature): bool
    {
        return $this->featureLimit($merchant, $feature) === null;
    }

    /**
     * Remaining capacity for a resource.
     * Returns null for unlimited.
     */
    public function remaining(Merchant $merchant, string $feature): ?int
    {
        $limit = $this->featureLimit($merchant, $feature);

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - $this->usageCount($merchant, $feature));
    }

    /**
     * Remaining capacity for rewards on a specific campaign.
     */
    public function rewardRemaining(Merchant $merchant, LoyaltyProgram $campaign): ?int
    {
        $limit = $this->featureLimit($merchant, 'rewards_per_campaign');

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - $this->rewardUsageCount($campaign));
    }

    /**
     * Usage as a percentage (0–100+). Returns null for unlimited.
     */
    public function usagePercentage(Merchant $merchant, string $feature): ?int
    {
        $limit = $this->featureLimit($merchant, $feature);

        if ($limit === null || $limit === 0) {
            return null;
        }

        return (int) round(($this->usageCount($merchant, $feature) / $limit) * 100);
    }

    /**
     * Usage percentage for rewards on a specific campaign.
     */
    public function rewardUsagePercentage(Merchant $merchant, LoyaltyProgram $campaign): ?int
    {
        $limit = $this->featureLimit($merchant, 'rewards_per_campaign');

        if ($limit === null || $limit === 0) {
            return null;
        }

        return (int) round(($this->rewardUsageCount($campaign) / $limit) * 100);
    }

    /**
     * Warning level for a resource: 'normal', 'warning', or 'limit_reached'.
     */
    public function warningLevel(Merchant $merchant, string $feature): string
    {
        $pct = $this->usagePercentage($merchant, $feature);

        if ($pct === null) {
            return 'normal'; // unlimited
        }

        $warning      = (int) config('subscriptions.warning_threshold', 80);
        $limitReached = (int) config('subscriptions.limit_reached_threshold', 100);

        if ($pct >= $limitReached) {
            return 'limit_reached';
        }

        if ($pct >= $warning) {
            return 'warning';
        }

        return 'normal';
    }

    /**
     * Warning level for rewards on a specific campaign.
     */
    public function rewardWarningLevel(Merchant $merchant, LoyaltyProgram $campaign): string
    {
        $pct = $this->rewardUsagePercentage($merchant, $campaign);

        if ($pct === null) {
            return 'normal';
        }

        $warning      = (int) config('subscriptions.warning_threshold', 80);
        $limitReached = (int) config('subscriptions.limit_reached_threshold', 100);

        if ($pct >= $limitReached) {
            return 'limit_reached';
        }

        if ($pct >= $warning) {
            return 'warning';
        }

        return 'normal';
    }

    /**
     * Whether the merchant can create another member.
     */
    public function canCreateMember(Merchant $merchant): bool
    {
        $limit = $this->featureLimit($merchant, 'members');

        if ($limit === null) {
            return true;
        }

        return $this->usageCount($merchant, 'members') < $limit;
    }

    /**
     * Whether the merchant can create another campaign.
     */
    public function canCreateCampaign(Merchant $merchant): bool
    {
        $limit = $this->featureLimit($merchant, 'campaigns');

        if ($limit === null) {
            return true;
        }

        return $this->usageCount($merchant, 'campaigns') < $limit;
    }

    /**
     * Whether the merchant can add another reward to a campaign.
     */
    public function canCreateReward(Merchant $merchant, LoyaltyProgram $campaign): bool
    {
        $limit = $this->featureLimit($merchant, 'rewards_per_campaign');

        if ($limit === null) {
            return true;
        }

        return $this->rewardUsageCount($campaign) < $limit;
    }

    /**
     * Structured usage summary for the dashboard card.
     */
    public function usageSummary(Merchant $merchant): array
    {
        $membersUsed   = $this->usageCount($merchant, 'members');
        $membersLimit  = $this->featureLimit($merchant, 'members');
        $membersPct    = $this->usagePercentage($merchant, 'members');

        $campaignsUsed  = $this->usageCount($merchant, 'campaigns');
        $campaignsLimit = $this->featureLimit($merchant, 'campaigns');
        $campaignsPct   = $this->usagePercentage($merchant, 'campaigns');

        return [
            'plan_name'           => $merchant->currentPlan()->label(),
            'effective_plan_name' => config("subscriptions.plans.{$this->effectivePlanKey($merchant)}.name", 'Free'),
            'is_on_trial'         => $merchant->isOnTrial(),
            'trial_days_remaining' => $merchant->trialDaysRemaining(),
            'subscription_status' => $merchant->subscriptionStatus()->label(),
            'members' => [
                'used'       => $membersUsed,
                'limit'      => $membersLimit,
                'unlimited'  => $membersLimit === null,
                'remaining'  => $membersLimit !== null ? max(0, $membersLimit - $membersUsed) : null,
                'percentage' => $membersPct,
                'level'      => $this->warningLevel($merchant, 'members'),
            ],
            'campaigns' => [
                'used'       => $campaignsUsed,
                'limit'      => $campaignsLimit,
                'unlimited'  => $campaignsLimit === null,
                'remaining'  => $campaignsLimit !== null ? max(0, $campaignsLimit - $campaignsUsed) : null,
                'percentage' => $campaignsPct,
                'level'      => $this->warningLevel($merchant, 'campaigns'),
            ],
        ];
    }
}
