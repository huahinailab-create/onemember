<?php

namespace App\Events\Domain;

use App\Models\Merchant;

/**
 * PLATFORM-002 Part 3 — A merchant subscription status or plan changed.
 */
class SubscriptionChanged extends DomainEvent
{
    public function __construct(public readonly Merchant $merchant, public readonly ?string $from, public readonly ?string $to)
    {
    }

    public function name(): string
    {
        return 'subscription.changed';
    }

    public function payload(): array
    {
        return ["merchant_id" => $this->merchant->id, "from" => $this->from, "to" => $this->to];
    }

    public function merchantId(): ?int
    {
        return $this->merchant->id;
    }
}
