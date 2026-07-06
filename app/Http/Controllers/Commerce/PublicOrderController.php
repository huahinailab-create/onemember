<?php

namespace App\Http\Controllers\Commerce;

use App\Enums\MerchantStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * APP-003 — public order placement + confirmation. Prices and totals are
 * computed server-side from the merchant's catalogue (never trusted from
 * the client). Payment is direct customer→merchant: the confirmation page
 * shows the merchant's own payment QR/instructions (ADR-011).
 */
class PublicOrderController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $merchant = Merchant::where('slug', $slug)
            ->where('status', MerchantStatus::Active)
            ->firstOrFail();
        abort_unless($merchant->hasApp('commerce'), 404);

        $commerce = $merchant->settings['commerce'] ?? [];
        $enabled  = array_keys(array_filter([
            'pickup'   => (bool) ($commerce['pickup_enabled'] ?? false),
            'delivery' => (bool) ($commerce['delivery_enabled'] ?? false),
            'shipping' => (bool) ($commerce['shipping_enabled'] ?? false),
        ]));

        $validated = $request->validate([
            'customer_name'    => ['required', 'string', 'max:150'],
            'customer_phone'   => ['required', 'string', 'max:30'],
            'fulfillment_type' => ['required', Rule::in($enabled ?: ['pickup'])],
            'address'          => ['nullable', 'string', 'max:500',
                Rule::requiredIf(fn () => in_array($request->input('fulfillment_type'), ['delivery', 'shipping'], true))],
            'notes'            => ['nullable', 'string', 'max:500'],
            'qty'              => ['required', 'array'],
            'qty.*'            => ['nullable', 'integer', 'min:0', 'max:99'],
        ]);

        $quantities = array_filter(array_map('intval', $validated['qty']), fn ($q) => $q > 0);
        if ($quantities === []) {
            throw ValidationException::withMessages(['qty' => __('commerce.order_empty')]);
        }

        $order = DB::transaction(function () use ($merchant, $validated, $quantities, $commerce) {
            $products = Product::where('merchant_id', $merchant->id)
                ->where('status', 'active')
                ->whereIn('id', array_keys($quantities))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lines    = [];
            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    throw ValidationException::withMessages(['qty' => __('commerce.order_invalid_product')]);
                }
                if ($product->stock_qty !== null && $product->stock_qty < $qty) {
                    throw ValidationException::withMessages(['qty' => __('commerce.order_out_of_stock', ['product' => $product->name])]);
                }
                $subtotal += $product->price * $qty;
                $lines[]   = ['product' => $product, 'qty' => $qty];
            }

            $fee = match ($validated['fulfillment_type']) {
                'delivery' => (float) ($commerce['delivery_fee'] ?? 0),
                'shipping' => (float) ($commerce['shipping_fee'] ?? 0),
                default    => 0.0,
            };

            $order = Order::create([
                'merchant_id'      => $merchant->id,
                'customer_name'    => $validated['customer_name'],
                'customer_phone'   => $validated['customer_phone'],
                'fulfillment_type' => $validated['fulfillment_type'],
                'address'          => $validated['address'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'subtotal'         => $subtotal,
                'fulfillment_fee'  => $fee,
                'total'            => $subtotal + $fee,
            ]);

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $line['product']->id,
                    'name'       => $line['product']->name,
                    'price'      => $line['product']->price,
                    'qty'        => $line['qty'],
                ]);

                if ($line['product']->stock_qty !== null) {
                    $line['product']->decrement('stock_qty', $line['qty']);
                }
            }

            return $order;
        });

        return redirect()->route('storefront.order.show', [$merchant->slug, $order->public_uuid]);
    }

    public function show(string $slug, string $orderUuid)
    {
        $merchant = Merchant::where('slug', $slug)->firstOrFail();
        $order    = Order::where('public_uuid', $orderUuid)
            ->where('merchant_id', $merchant->id)
            ->with('items')
            ->firstOrFail();

        $locale = $merchant->settings['locale'] ?? 'th';
        app()->setLocale(in_array($locale, ['en', 'th'], true) ? $locale : 'th');

        return view('storefront.order', [
            'merchant' => $merchant,
            'order'    => $order,
            'commerce' => $merchant->settings['commerce'] ?? [],
        ]);
    }
}
