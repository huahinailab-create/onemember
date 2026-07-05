<?php

namespace App\Events;

use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberPointsEarned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Member $member,
        public readonly Transaction $transaction,
        public readonly LoyaltyProgram $campaign,
    ) {}
}
