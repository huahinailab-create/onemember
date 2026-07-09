<?php

namespace App\Apps\Procurement\Http;

use App\Apps\Procurement\Models\PurchaseOrder;
use App\Apps\Procurement\Models\PurchaseRequest;
use App\Apps\Procurement\Models\Supplier;
use App\Apps\Procurement\ProcurementService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** PLATFORM-002 Part 9 — Procurement overview + basic CRUD (architecture first). */
class ProcurementController extends Controller
{
    public function __construct(private readonly ProcurementService $procurement)
    {
    }

    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        return view('apps.procurement.index', [
            'suppliers' => Supplier::where('merchant_id', $merchant->id)->orderBy('name')->get(),
            'requests'  => PurchaseRequest::where('merchant_id', $merchant->id)->latest()->take(25)->with('supplier')->get(),
            'orders'    => PurchaseOrder::where('merchant_id', $merchant->id)->latest()->take(25)->with('supplier')->get(),
        ]);
    }

    public function storeSupplier(Request $request)
    {
        $merchant  = $request->user()->merchant;
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'category'       => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'email'          => ['nullable', 'email', 'max:150'],
        ]);

        Supplier::create(array_merge($validated, ['merchant_id' => $merchant->id]));

        return redirect()->route('procurement.index')->with('success', __('procurement.supplier_created'));
    }

    public function rateSupplier(Request $request, Supplier $supplier)
    {
        abort_unless($supplier->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate(['stars' => ['required', 'integer', 'min:1', 'max:5']]);

        $supplier->rate($validated['stars']);

        return redirect()->route('procurement.index')->with('success', __('procurement.supplier_rated'));
    }

    public function storeRequest(Request $request)
    {
        $merchant  = $request->user()->merchant;
        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:200'],
            'supplier_id'    => ['nullable', 'integer'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.name'   => ['required', 'string', 'max:150'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'items.*.est_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $supplier = null;
        if (! empty($validated['supplier_id'])) {
            $supplier = Supplier::where('merchant_id', $merchant->id)->find($validated['supplier_id']);
        }

        PurchaseRequest::create([
            'merchant_id'    => $merchant->id,
            'supplier_id'    => $supplier?->id,
            'title'          => $validated['title'],
            'items'          => $validated['items'],
            'estimated_cost' => collect($validated['items'])->sum(fn ($i) => ($i['est_cost'] ?? 0) * $i['qty']),
            'requested_by'   => $request->user()->id,
        ]);

        return redirect()->route('procurement.index')->with('success', __('procurement.request_created'));
    }

    public function submitRequest(Request $request, PurchaseRequest $purchaseRequest)
    {
        abort_unless($purchaseRequest->merchant_id === $request->user()->merchant?->id, 403);

        $this->procurement->submit($purchaseRequest);

        return redirect()->route('procurement.index')->with('success', __('procurement.request_submitted'));
    }

    public function approveRequest(Request $request, PurchaseRequest $purchaseRequest)
    {
        abort_unless($purchaseRequest->merchant_id === $request->user()->merchant?->id, 403);

        $this->procurement->approve($purchaseRequest, $request->user());

        return redirect()->route('procurement.index')->with('success', __('procurement.request_approved'));
    }

    public function rejectRequest(Request $request, PurchaseRequest $purchaseRequest)
    {
        abort_unless($purchaseRequest->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $this->procurement->reject($purchaseRequest, $validated['reason']);

        return redirect()->route('procurement.index')->with('success', __('procurement.request_rejected'));
    }

    public function receiveOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->merchant_id === $request->user()->merchant?->id, 403);

        $this->procurement->receive(
            $purchaseOrder,
            $purchaseOrder->items ?? [],
            $request->user(),
            $request->input('notes'),
        );

        return redirect()->route('procurement.index')->with('success', __('procurement.order_received'));
    }
}
