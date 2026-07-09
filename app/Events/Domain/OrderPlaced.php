<?php

namespace App\Events\Domain;

use App\Models\Order;

/**
 * PLATFORM-002 Part 3 — A customer placed an order on the Merchant Storefront (Commerce App).
 */
class OrderPlaced extends DomainEvent
{
    public function __construct(public readonly Order $order)
    {
    }

    public function name(): string
    {
        return 'order.placed';
    }

    public function payload(): array
    {
        return ["order_id" => $this->order->id, "total" => (string) $this->order->total, "fulfillment_type" => $this->order->fulfillment_type, "status" => $this->order->status];
    }

    public function merchantId(): ?int
    {
        return $this->order->merchant_id;
    }
}
