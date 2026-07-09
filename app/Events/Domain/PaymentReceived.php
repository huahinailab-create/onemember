<?php

namespace App\Events\Domain;

use App\Models\Order;

/**
 * PLATFORM-002 Part 3 — The MERCHANT marked an order as paid. Merchant self-reported: payment goes directly customer to merchant; OneMember never receives, holds or settles money (ADR-011).
 */
class PaymentReceived extends DomainEvent
{
    public function __construct(public readonly Order $order)
    {
    }

    public function name(): string
    {
        return 'payment.received';
    }

    public function payload(): array
    {
        return ["order_id" => $this->order->id, "total" => (string) $this->order->total];
    }

    public function merchantId(): ?int
    {
        return $this->order->merchant_id;
    }
}
