<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\Merchant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessExpiredTrials extends Command
{
    protected $signature   = 'subscriptions:process-expired-trials';
    protected $description = 'Downgrade merchants whose Professional trial has expired to the Free plan.';

    public function handle(): int
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
            $count++;

            $this->line("  Downgraded: {$merchant->name} (ID {$merchant->id})");
        }

        $this->info("Processed {$count} expired trial(s). All merchants moved to Free plan.");

        Log::info('ProcessExpiredTrials completed.', [
            'merchants_processed' => $count,
            'run_at'              => now()->toIso8601String(),
        ]);

        return Command::SUCCESS;
    }
}
