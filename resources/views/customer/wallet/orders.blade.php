@extends('layouts.customer')

@section('title', __('customer_wallet.orders_title'))
@section('shell-class', 'customer-shell-wide')
@section('wallet-nav', '1')

@section('content')
<h1 class="customer-h1 mb-1">{{ __('customer_wallet.orders_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer_wallet.orders_sub') }}</p>

@forelse ($orders as $order)
    <div class="customer-wallet-box mb-3">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
                <div class="fw-semibold">{{ $order->merchant->displayName() }}</div>
                <div class="small text-muted">
                    {{ __('customer_wallet.order_number', ['number' => $order->id]) }}
                    · {{ $order->created_at->translatedFormat('j M Y H:i') }}
                </div>
            </div>
            <span class="badge {{ $order->status === 'completed' ? 'bg-success' : ($order->status === 'cancelled' ? 'bg-secondary' : 'bg-primary') }}">
                {{ __('commerce.order_status_'.$order->status) }}
            </span>
        </div>
        <ul class="list-unstyled small mt-2 mb-2">
            @foreach ($order->items as $item)
                <li>{{ $item->qty }} × {{ $item->name }} <span class="text-muted">({{ number_format($item->price, 2) }})</span></li>
            @endforeach
        </ul>
        <div class="d-flex align-items-center justify-content-between">
            <span class="fw-semibold">{{ __('customer_wallet.order_total', ['total' => number_format($order->total, 2)]) }}</span>
            @if ($order->merchant->hasApp('commerce'))
                <a href="{{ route('storefront.show', $order->merchant->slug) }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                    {{ __('customer_wallet.reorder') }}
                </a>
            @endif
        </div>
        @if ($order->address)
            <details class="mt-2">
                <summary class="small text-muted">{{ __('customer_wallet.order_address') }}</summary>
                <div class="small mt-1" style="white-space: pre-line;">{{ $order->address }}</div>
            </details>
        @endif
    </div>
@empty
    <div class="customer-wallet-empty">
        <i class="bi bi-bag d-block mb-2" aria-hidden="true"></i>
        <p class="mb-1">{{ __('customer_wallet.empty_orders') }}</p>
        <p class="small text-muted mb-0">{{ __('customer_wallet.empty_orders_hint') }}</p>
    </div>
@endforelse
@endsection
