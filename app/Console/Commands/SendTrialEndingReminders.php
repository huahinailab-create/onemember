<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Events\TrialEnding;
use App\Models\Merchant;
use Illuminate\Console\Command;

class SendTrialEndingReminders extends Command
{
    protected $signature   = 'subscriptions:send-trial-ending-reminders';
    protected $description = 'Send trial-ending reminder emails to merchants approaching the end of their trial.';

    public function handle(): int
    {
        $merchants = Merchant::where('subscription_status', SubscriptionStatus::Trial)
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
            ->get()
            ->filter(fn ($m) => empty($m->settings['trial_reminder_sent']));

        if ($merchants->isEmpty()) {
            $this->info('No trial-ending reminders to send.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($merchants as $merchant) {
            $daysRemaining = (int) ceil(now()->diffInHours($merchant->trial_ends_at) / 24);

            TrialEnding::dispatch($merchant, $daysRemaining);

            $settings = $merchant->settings;
            $settings['trial_reminder_sent'] = true;
            $merchant->update(['settings' => $settings]);

            $count++;
            $this->line("  Reminder queued: {$merchant->name} ({$daysRemaining} days remaining)");
        }

        $this->info("Trial-ending reminders sent: {$count}.");

        return Command::SUCCESS;
    }
}
