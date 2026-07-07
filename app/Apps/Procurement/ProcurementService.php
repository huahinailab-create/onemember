<?php

namespace App\Apps\Procurement;

use App\Apps\Procurement\Events\GoodsReceived;
use App\Apps\Procurement\Models\GoodsReceipt;
use App\Apps\Procurement\Models\PurchaseOrder;
use App\Apps\Procurement\Models\PurchaseRequest;
use App\Events\Domain\PurchaseOrderApproved;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * PLATFORM-002 Part 9 — Procurement workflow service.
 * Approval workflow lives here so the rules stay identical for UI and any
 * future API surface. Cost tracking: estimated (PR) vs actual (PO).
 */
class ProcurementService
{
    public function submit(PurchaseRequest $request): void
    {
        $this->transition($request, 'submitted');
        $request->save();
    }

    public function approve(PurchaseRequest $request, User $approver): PurchaseOrder
    {
        $this->transition($request, 'approved');
        $request->approved_by = $approver->id;
        $request->approved_at = now();
        $request->save();

        // Approval immediately raises the PO (single-step flow for now;
        // multi-level approval chains are a future extension point).
        $order = PurchaseOrder::create([
            'merchant_id'         => $request->merchant_id,
            'purchase_request_id' => $request->id,
            'supplier_id'         => $request->supplier_id,
            'items'               => $request->items,
            'total_cost'          => $request->estimated_cost,
            'status'              => 'ordered',
        ]);

        $request->update(['status' => 'ordered']);

        event(new PurchaseOrderApproved($order));

        return $order;
    }

    public function reject(PurchaseRequest $request, string $reason): void
    {
        $this->transition($request, 'rejected');
        $request->rejection_reason = $reason;
        $request->save();
    }

    /** Receiving goods closes the loop and fires the inventory hook. */
    public function receive(PurchaseOrder $order, array $items, ?User $receiver = null, ?string $notes = null): GoodsReceipt
    {
        $receipt = GoodsReceipt::create([
            'merchant_id'       => $order->merchant_id,
            'purchase_order_id' => $order->id,
            'items'             => $items,
            'notes'             => $notes,
            'received_by'       => $receiver?->id,
        ]);

        $order->update(['status' => 'received']);

        event(new GoodsReceived($receipt));

        return $receipt;
    }

    private function transition(PurchaseRequest $request, string $to): void
    {
        if (! $request->canTransitionTo($to)) {
            throw ValidationException::withMessages([
                'status' => __('procurement.invalid_transition'),
            ]);
        }

        $request->status = $to;
    }
}
