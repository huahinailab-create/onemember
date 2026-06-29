<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\Merchant;
use App\Services\AnalyticsService;
use App\Services\SecurityLogger;
use Illuminate\Console\Command;

class ProcessExpiredTrials extends Command
{
    protected $signature   = 'subscriptions:process-expired-trials';
    protected $description = 'Downgrade merchants whose Professional trial has expired to the Free plan.';

    public function handle(SecurityLogger $logger, AnalyticsService $analytics): int
    {
        $expired = Merchant::where('subscription_status', SubscriptionStatus::Trial)
            ->where('trial_ends_at', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired trials to process.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($expired as $merchant) {
            $merchant->update([
                'subscription_status' => SubscriptionStatus::Expired,
                'subscription_plan'   => SubscriptionPlan::Free,
            ]);

            $analytics->track('trial_expired', ['merchant_id' => $merchant->id], null, $merchant->id);
            $logger->trialExpired($merchant->id, $merchant->name, 'professional');
            $logger->subscriptionStatusChanged($merchant->id, 'trial', 'expired');
            $logger->subscriptionPlanChanged($merchant->id, 'professional', 'free');

            $count++;

            $this->line("  Downgraded: {$merchant->name} (ID {$merchant->id})");
        }

        $this->info("Processed {$count} expired trial(s). All merchants moved to Free plan.");

        return Command::SUCCESS;
    }
}
