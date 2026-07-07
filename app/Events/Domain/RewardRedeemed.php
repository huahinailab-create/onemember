<?php

namespace App\Events\Domain;

use App\Models\Redemption;

/**
 * PLATFORM-002 Part 3 — A member redeemed a reward.
 */
class RewardRedeemed extends DomainEvent
{
    public function __construct(public readonly Redemption $redemption)
    {
    }

    public function name(): string
    {
        return 'reward.redeemed';
    }

    public function payload(): array
    {
        return ["redemption_id" => $this->redemption->id, "member_id" => $this->redemption->member_id, "reward_id" => $this->redemption->reward_id];
    }

    public function merchantId(): ?int
    {
        return $this->redemption->merchant_id ?? $this->redemption->member?->merchant_id;
    }
}
