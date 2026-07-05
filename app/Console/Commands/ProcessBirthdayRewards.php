<?php

namespace App\Console\Commands;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\TransactionType;
use App\Events\MemberBirthdayBonusAwarded;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Console\Command;

class ProcessBirthdayRewards extends Command
{
    protected $signature   = 'loyalty:process-birthday-rewards';
    protected $description = 'Award birthday bonus points to eligible members.';

    public function handle(): int
    {
        $campaigns = LoyaltyProgram::with('merchant')
            ->where('type', LoyaltyProgramType::Points)
            ->where('status', CampaignStatus::Active)
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn ($c) => ! empty($c->settings['birthday_enabled'])
                             && ! empty($c->settings['birthday_points']));

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns with birthday rewards configured.');
            return Command::SUCCESS;
        }

        $awarded = 0;

        foreach ($campaigns as $campaign) {
            $bonusPoints  = (int) $campaign->settings['birthday_points'];
            $daysBefore   = (int) ($campaign->settings['birthday_valid_days_before'] ?? 0);
            $daysAfter    = (int) ($campaign->settings['birthday_valid_days_after']  ?? 0);

            $members = Member::where('merchant_id', $campaign->merchant_id)
                ->whereNotNull('birthday')
                ->get();

            foreach ($members as $member) {
                if (! $this->isInBirthdayWindow($member, $daysBefore, $daysAfter)) {
                    continue;
                }

                // Idempotency: one birthday transaction per member per calendar year
                $alreadyAwarded = Transaction::where('member_id', $member->id)
                    ->where('loyalty_program_id', $campaign->id)
                    ->where('type', TransactionType::Birthday->value)
                    ->whereYear('created_at', now()->year)
                    ->exists();

                if ($alreadyAwarded) {
                    continue;
                }

                $balanceBefore = $member->total_points;
                $balanceAfter  = $balanceBefore + $bonusPoints;

                Transaction::create([
                    'merchant_id'        => $campaign->merchant_id,
                    'member_id'          => $member->id,
                    'loyalty_program_id' => $campaign->id,
                    'created_by'         => null,
                    'type'               => TransactionType::Birthday->value,
                    'points'             => $bonusPoints,
                    'balance_before'     => $balanceBefore,
                    'balance_after'      => $balanceAfter,
                    'note'               => 'Birthday bonus',
                    'created_at'         => now(),
                ]);

                $member->update([
                    'total_points'     => $balanceAfter,
                    'last_activity_at' => now(),
                ]);

                MemberBirthdayBonusAwarded::dispatch($member, $bonusPoints);

                $awarded++;
                $this->line("  Birthday bonus: {$member->name} +{$bonusPoints} pts (merchant {$campaign->merchant_id})");
            }
        }

        $this->info("Birthday rewards processed. {$awarded} bonus(es) awarded.");

        return Command::SUCCESS;
    }

    private function isInBirthdayWindow(Member $member, int $daysBefore, int $daysAfter): bool
    {
        if (! $member->birthday) {
            return false;
        }

        $birthday    = $member->birthday->setYear(now()->year);
        $windowStart = $birthday->copy()->subDays($daysBefore)->startOfDay();
        $windowEnd   = $birthday->copy()->addDays($daysAfter)->endOfDay();

        return now()->between($windowStart, $windowEnd);
    }
}
