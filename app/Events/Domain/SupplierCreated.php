<?php

namespace App\Events\Domain;


/**
 * PLATFORM-002 Part 3 — A supplier was created (Procurement App, Part 9).
 */
class SupplierCreated extends DomainEvent
{
    public function __construct(public readonly \App\Apps\Procurement\Models\Supplier $supplier)
    {
    }

    public function name(): string
    {
        return 'supplier.created';
    }

    public function payload(): array
    {
        return ["supplier_id" => $this->supplier->id, "name" => $this->supplier->name];
    }

    public function merchantId(): ?int
    {
        return $this->supplier->merchant_id;
    }
}
