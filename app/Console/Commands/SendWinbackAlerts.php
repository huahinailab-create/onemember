<?php

namespace App\Console\Commands;

use App\Enums\MemberStatus;
use App\Events\WinbackAlertReady;
use App\Models\Member;
use App\Models\Merchant;
use Illuminate\Console\Command;

class SendWinbackAlerts extends Command
{
    protected $signature   = 'loyalty:send-winback-alerts';
    protected $description = 'Alert merchants about members who crossed their inactivity threshold in the past day.';

    public function handle(): int
    {
        $alerted = 0;

        Merchant::whereNotNull('settings')->each(function (Merchant $merchant) use (&$alerted) {
            $days = (int) ($merchant->settings['winback_days'] ?? 0);
            if ($days < 1) {
                return;
            }

            // Members whose last activity crossed the threshold within the past
            // day. The window moves daily, so each member is reported once.
            $members = Member::where('merchant_id', $merchant->id)
                ->where('status', MemberStatus::Active)
                ->whereBetween('last_activity_at', [
                    now()->subDays($days + 1),
                    now()->subDays($days),
                ])
                ->orderBy('last_activity_at')
                ->get();

            if ($members->isEmpty()) {
                return;
            }

            WinbackAlertReady::dispatch($merchant, $members, $days);
            $alerted++;
            $this->line("  Win-back alert: merchant {$merchant->id} — {$members->count()} member(s)");
        });

        $this->info("Win-back alerts processed. {$alerted} merchant(s) alerted.");

        return Command::SUCCESS;
    }
}
