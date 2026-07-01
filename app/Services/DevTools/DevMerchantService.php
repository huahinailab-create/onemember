<?php

namespace App\Services\DevTools;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\Merchant;
use Illuminate\Support\Facades\DB;

class DevMerchantService
{
    public function deleteMerchant(Merchant $merchant): void
    {
        DB::transaction(function () use ($merchant) {
            $this->deleteAllData($merchant);
            $merchant->forceDelete();
        });
    }

    public function archiveMerchant(Merchant $merchant): void
    {
        $merchant->delete();
    }

    public function restoreMerchant(Merchant $merchant): void
    {
        $merchant->restore();
    }

    public function resetOnboarding(Merchant $merchant): void
    {
        $merchant->forceFill(['onboarding_completed_at' => null])->save();
    }

    public function resetSubscription(Merchant $merchant): void
    {
        $merchant->forceFill([
            'subscription_plan'      => null,
            'subscription_status'    => null,
            'trial_ends_at'          => null,
            'stripe_subscription_id' => null,
            'subscription_renews_at' => null,
            'cancel_at_period_end'   => false,
        ])->save();
    }

    public function changePlan(Merchant $merchant, string $plan): void
    {
        $merchant->forceFill(['subscription_plan' => SubscriptionPlan::from($plan)])->save();
    }

    public function activateTrial(Merchant $merchant, int $days = 30): void
    {
        $merchant->forceFill([
            'subscription_plan'   => SubscriptionPlan::Professional,
            'subscription_status' => SubscriptionStatus::Trialing,
            'trial_ends_at'       => now()->addDays($days),
        ])->save();
    }

    public function expireTrial(Merchant $merchant): void
    {
        $merchant->forceFill(['trial_ends_at' => now()->subDay()])->save();
    }

    public function resetBilling(Merchant $merchant): void
    {
        $merchant->forceFill([
            'stripe_customer_id'     => null,
            'stripe_subscription_id' => null,
            'stripe_price_id'        => null,
            'billing_email'          => null,
        ])->save();
    }

    public function resetLoyaltyProgram(Merchant $merchant): void
    {
        if ($merchant->loyaltyProgram) {
            $merchant->loyaltyProgram->forceFill([
                'points_per_purchase' => 1,
                'stamps_per_card'     => 10,
            ])->save();
        }
    }

    public function resetCampaigns(Merchant $merchant): void
    {
        // Campaigns relationship not present on Merchant — stub for future use
    }

    public function deleteAllData(Merchant $merchant): void
    {
        DB::transaction(function () use ($merchant) {
            $memberIds = $merchant->members()->withTrashed()->pluck('id');
            if ($memberIds->isNotEmpty()) {
                DB::table('transactions')->whereIn('member_id', $memberIds)->delete();
                DB::table('redemptions')->whereIn('member_id', $memberIds)->delete();
            }
            $merchant->members()->withTrashed()->forceDelete();
            $merchant->rewards()->withTrashed()->forceDelete();
        });
    }
}
