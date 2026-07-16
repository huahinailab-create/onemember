<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('commerce.order_confirmation_title') }} – {{ $merchant->displayName() }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="storefront-body">
    <main class="storefront">
        <header class="storefront-header">
            <div class="storefront-brand">{{ $merchant->displayName() }}</div>
            <div class="storefront-type">{{ __('commerce.order_confirmation_title') }}</div>
            <div class="storefront-contact">
                <span><i class="bi bi-person me-1"></i>{{ __('commerce.order_thanks', ['name' => $order->customer_name]) }}</span>
            </div>
        </header>

        <p class="text-center text-muted mb-0 mt-2" style="font-size:0.78rem;">
            <i class="bi bi-bookmark me-1"></i>{{ __('commerce.order_save_hint') }}
        </p>

        <section class="storefront-catalogue">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="storefront-section-title mb-0">
                    <i class="bi bi-receipt me-2"></i>#{{ $order->id }}
                </div>
                <span class="badge {{ $order->status === 'cancelled' ? 'bg-danger' : ($order->status === 'completed' ? 'bg-success' : 'bg-primary') }}">
                    {{ __('commerce.order_status_' . $order->status) }}
                </span>
            </div>

            <ul class="storefront-products">
                @foreach ($order->items as $item)
                    <li class="storefront-product">
                        <div class="storefront-product-media">
                            @if ($item->product?->imageUrl())
                                <img src="{{ $item->product->imageUrl() }}" alt="{{ $item->name }}" loading="lazy">
                            @else
                                <i class="bi bi-image" aria-hidden="true"></i>
                            @endif
                        </div>
                        <div class="storefront-product-body storefront-product-name">{{ $item->qty }} × {{ $item->name }}</div>
                        <div class="storefront-product-price">{{ number_format($item->price * $item->qty, 2) }}</div>
                    </li>
                @endforeach
                @if ($order->fulfillment_fee > 0)
                    <li class="storefront-product">
                        <div class="storefront-product-name">{{ __('commerce.order_fee_' . $order->fulfillment_type) }}</div>
                        <div class="storefront-product-price">{{ number_format($order->fulfillment_fee, 2) }}</div>
                    </li>
                @endif
                <li class="storefront-product">
                    <div class="storefront-product-name fw-bold">{{ __('commerce.order_total') }}</div>
                    <div class="storefront-product-price">{{ number_format($order->total, 2) }} <span class="storefront-currency">{{ $merchant->currency ?? config('app.default_currency') }}</span></div>
                </li>
            </ul>

            <div class="text-muted small mt-2">
                <i class="bi bi-truck me-1"></i>{{ __('commerce.' . ($order->fulfillment_type === 'delivery' ? 'delivery_label' : ($order->fulfillment_type === 'shipping' ? 'shipping_label' : 'pickup_label'))) }}
                @if ($order->address) — {{ $order->address }} @endif
            </div>
        </section>

        {{-- Payment: DIRECT to the merchant (ADR-011) --}}
        <section class="storefront-catalogue">
            <div class="storefront-section-title"><i class="bi bi-qr-code me-2"></i>{{ __('commerce.order_pay_title') }}</div>

            @if ($order->payment_status === 'paid')
                <div class="alert alert-success py-2 mb-0">{{ __('commerce.order_paid_badge') }}</div>
            @else
                @if (!empty($commerce['payment_qr_path']))
                    <div class="text-center mb-2">
                        <img src="{{ Storage::disk('public')->url($commerce['payment_qr_path']) }}"
                             alt="{{ __('commerce.payment_qr') }}" class="commerce-payment-qr-preview">
                    </div>
                @endif
                @if (!empty($commerce['payment_instructions']))
                    <p class="small mb-2">{{ $commerce['payment_instructions'] }}</p>
                @endif
                <p class="text-muted mb-0" style="font-size:0.72rem;">{{ __('commerce.order_pay_note', ['merchant' => $merchant->displayName()]) }}</p>
            @endif
        </section>

        <footer class="storefront-footer">
            <a href="{{ route('storefront.show', $merchant->slug) }}" class="storefront-join-link">
                <i class="bi bi-arrow-left me-1"></i>{{ __('commerce.order_back_to_store') }}
            </a>
            <div class="mt-2">{{ __('launch.poster_powered') }}</div>
        </footer>
    </main>
</body>
</html>
