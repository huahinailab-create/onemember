<?php

namespace App\Http\Controllers\Commerce;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** APP-003 — merchant order management (gated app.installed:commerce). */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        $status = $request->query('status', 'open');
        $query  = Order::where('merchant_id', $merchant->id)->with('items')->latest();

        if ($status === 'open') {
            $query->whereIn('status', ['placed', 'accepted', 'ready']);
        } elseif (in_array($status, Order::STATUSES, true)) {
            $query->where('status', $status);
        }

        return view('commerce.orders.index', [
            'orders'   => $query->paginate(25)->withQueryString(),
            'status'   => $status,
            'merchant' => $merchant,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $merchant = $request->user()->merchant;
        abort_unless($order->merchant_id === $merchant?->id, 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        if (! $order->canTransitionTo($validated['status'])) {
            return back()->withErrors(['status' => __('commerce.order_invalid_transition')]);
        }

        $old = $order->status;

        // Cancelling restores tracked stock
        if ($validated['status'] === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product && $item->product->stock_qty !== null) {
                    $item->product->increment('stock_qty', $item->qty);
                }
            }
        }

        $order->update(['status' => $validated['status']]);

        AuditLog::record('order.status_changed', $order, ['status' => $old], ['status' => $validated['status']], $merchant->id);

        return back()->with('success', __('commerce.order_status_updated'));
    }

    /** Manual confirmation that the merchant received payment directly (ADR-011). */
    public function markPaid(Request $request, Order $order)
    {
        $merchant = $request->user()->merchant;
        abort_unless($order->merchant_id === $merchant?->id, 403);

        $order->update(['payment_status' => 'paid']);

        AuditLog::record('order.marked_paid', $order, ['payment_status' => 'unpaid'], ['payment_status' => 'paid'], $merchant->id);

        return back()->with('success', __('commerce.order_marked_paid'));
    }
}
