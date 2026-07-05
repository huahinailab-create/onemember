<?php

namespace App\Events;

use App\Models\Member;
use App\Models\Redemption;
use App\Models\Reward;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberRewardRedeemed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Member $member,
        public readonly Redemption $redemption,
        public readonly Reward $reward,
    ) {}
}
