<?php

namespace App\Events\Domain;


/**
 * PLATFORM-002 Part 3 — A purchase order passed the approval workflow (Procurement App, Part 9).
 */
class PurchaseOrderApproved extends DomainEvent
{
    public function __construct(public readonly \App\Apps\Procurement\Models\PurchaseOrder $purchaseOrder)
    {
    }

    public function name(): string
    {
        return 'purchase_order.approved';
    }

    public function payload(): array
    {
        return ["purchase_order_id" => $this->purchaseOrder->id, "supplier_id" => $this->purchaseOrder->supplier_id, "total_cost" => (string) $this->purchaseOrder->total_cost];
    }

    public function merchantId(): ?int
    {
        return $this->purchaseOrder->merchant_id;
    }
}
