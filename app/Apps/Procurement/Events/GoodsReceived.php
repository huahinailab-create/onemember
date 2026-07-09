<?php

namespace App\Apps\Procurement\Events;

use App\Apps\Procurement\Models\GoodsReceipt;
use App\Events\Domain\DomainEvent;

/**
 * PLATFORM-002 Part 9 — inventory integration hook: the future Inventory
 * App listens to this to adjust stock when goods arrive.
 */
class GoodsReceived extends DomainEvent
{
    public function __construct(public readonly GoodsReceipt $receipt)
    {
    }

    public function name(): string
    {
        return 'goods.received';
    }

    public function payload(): array
    {
        return [
            'goods_receipt_id'  => $this->receipt->id,
            'purchase_order_id' => $this->receipt->purchase_order_id,
            'items'             => $this->receipt->items,
        ];
    }

    public function merchantId(): ?int
    {
        return $this->receipt->merchant_id;
    }
}
