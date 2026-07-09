<?php

namespace App\Events\Domain;

use App\Models\Merchant;

/**
 * PLATFORM-002 Part 3 — A new merchant tenant was created.
 */
class MerchantRegistered extends DomainEvent
{
    public function __construct(public readonly Merchant $merchant)
    {
    }

    public function name(): string
    {
        return 'merchant.registered';
    }

    public function payload(): array
    {
        return ["merchant_id" => $this->merchant->id, "name" => $this->merchant->name, "country" => $this->merchant->country];
    }

    public function merchantId(): ?int
    {
        return $this->merchant->id;
    }
}
