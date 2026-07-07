<?php

namespace App\Events\Domain;

use App\Models\Transaction;

/**
 * PLATFORM-002 Part 3 — An earn transaction was written to the loyalty ledger.
 */
class PurchaseRecorded extends DomainEvent
{
    public function __construct(public readonly Transaction $transaction)
    {
    }

    public function name(): string
    {
        return 'purchase.recorded';
    }

    public function payload(): array
    {
        return ["transaction_id" => $this->transaction->id, "member_id" => $this->transaction->member_id, "points" => $this->transaction->points, "purchase_amount" => $this->transaction->purchase_amount ?? null];
    }

    public function merchantId(): ?int
    {
        return $this->transaction->merchant_id;
    }
}
