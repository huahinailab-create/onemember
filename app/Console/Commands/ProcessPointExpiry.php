<?php

namespace App\Console\Commands;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\TransactionType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Console\Command;

class ProcessPointExpiry extends Command
{
    protected $signature   = 'loyalty:process-point-expiry';
    protected $description = 'Expire points for members who have exceeded the inactivity window.';

    public function handle(): int
    {
        $campaigns = LoyaltyProgram::where('type', LoyaltyProgramType::Points)
            ->where('status', CampaignStatus::Active)
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn ($c) => ($c->settings['expiration_type'] ?? 'never') !== 'never'
                             && ! empty($c->settings['expiration_duration']));

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns with point expiry configured.');
            return Command::SUCCESS;
        }

        $expired = 0;

        foreach ($campaigns as $campaign) {
            $type     = $campaign->settings['expiration_type'];
            $duration = (int) $campaign->settings['expiration_duration'];

            $cutoff = $type === 'months'
                ? now()->subMonths($duration)
                : now()->subYears($duration);

            // lazyById keeps memory flat regardless of member count (B-05)
            $members = Member::where('merchant_id', $campaign->merchant_id)
                ->where('total_points', '>', 0)
                ->where('last_activity_at', '<', $cutoff)
                ->lazyById(1000);

            foreach ($members as $member) {
                // Idempotency: skip if an Expire transaction was already created today
                $alreadyExpired = Transaction::where('member_id', $member->id)
                    ->where('loyalty_program_id', $campaign->id)
                    ->where('type', TransactionType::Expire->value)
                    ->where('created_at', '>=', now()->subDay())
                    ->exists();

                if ($alreadyExpired) {
                    continue;
                }

                $pointsToExpire = $member->total_points;
                $balanceBefore  = $pointsToExpire;

                Transaction::create([
                    'merchant_id'        => $campaign->merchant_id,
                    'member_id'          => $member->id,
                    'loyalty_program_id' => $campaign->id,
                    'created_by'         => null,
                    'type'               => TransactionType::Expire->value,
                    'points'             => -$pointsToExpire,
                    'balance_before'     => $balanceBefore,
                    'balance_after'      => 0,
                    'note'               => "Points expired after {$duration} {$type} of inactivity",
                    'created_at'         => now(),
                ]);

                // last_activity_at is intentionally NOT updated — expiry is not activity
                $member->update(['total_points' => 0]);

                $expired++;
                $this->line("  Points expired: {$member->name} -{$pointsToExpire} pts (merchant {$campaign->merchant_id})");
            }
        }

        $this->info("Point expiry processed. {$expired} member(s) expired.");

        return Command::SUCCESS;
    }
}
