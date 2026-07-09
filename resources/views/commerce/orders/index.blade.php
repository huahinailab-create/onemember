<x-app-layout>
    <x-slot name="title">{{ __('commerce.orders_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('commerce.orders_title') }}</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between gap-2">
        <div>
            <h1>{{ __('commerce.orders_title') }}</h1>
            <p>{{ __('commerce.orders_subtitle') }}</p>
        </div>
        <x-ui.help-button topic="commerce.orders" class="flex-shrink-0" />
    </div>

    {{-- Status filter --}}
    <ul class="nav nav-tabs mb-3">
        @foreach (['open', 'completed', 'cancelled'] as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $status === $tab ? 'active' : '' }}"
                   href="{{ route('commerce.orders.index', ['status' => $tab]) }}">
                    {{ __('commerce.orders_tab_' . $tab) }}
                </a>
            </li>
        @endforeach
    </ul>

    <x-ui.flash :session="false" :with-errors="true" />

    @if ($orders->isEmpty())
        <div class="card"><div class="card-body">
            <x-ui.empty-state icon="bi-inbox" :title="__('commerce.orders_empty')" :body="__('commerce.orders_empty_body')" help-topic="commerce.orders">
                <a href="{{ route('storefront.show', $merchant->slug) }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                    <i class="bi bi-shop me-1" aria-hidden="true"></i>{{ __('commerce.view_store_button') }}
                    <span class="visually-hidden">{{ __('commerce.opens_new_tab') }}</span>
                </a>
            </x-ui.empty-state>
        </div></div>
    @else
        @foreach ($orders as $order)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold">#{{ $order->id }} · {{ $order->customer_name }}</span>
                            <span class="text-muted small ms-2"><i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}</span>
                            <div class="text-muted small mt-1">
                                {{ $order->created_at->format('d M H:i') }} ·
                                {{ __('commerce.' . ($order->fulfillment_type === 'delivery' ? 'delivery_label' : ($order->fulfillment_type === 'shipping' ? 'shipping_label' : 'pickup_label'))) }}
                                @if ($order->address) — {{ $order->address }} @endif
                            </div>
                            <ul class="small mb-0 mt-1 ps-3">
                                @foreach ($order->items as $item)
                                    <li>{{ $item->qty }} × {{ $item->name }}</li>
                                @endforeach
                            </ul>
                            @if ($order->notes)
                                <div class="small text-muted mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $order->notes }}</div>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format($order->total, 2) }} {{ $merchant->currency ?? config('app.default_currency') }}</div>
                            <x-ui.status-badge :status="$order->status" :label="__('commerce.order_status_' . $order->status)" />
                            <x-ui.status-badge :status="$order->payment_status" :label="__('commerce.order_payment_' . $order->payment_status)" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        @foreach (\App\Models\Order::TRANSITIONS[$order->status] ?? [] as $next)
                            <form method="POST" action="{{ route('commerce.orders.status', $order) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="{{ $next }}">
                                <button type="submit"
                                        class="btn btn-sm {{ $next === 'cancelled' ? 'btn-outline-danger' : 'btn-outline-primary' }}">
                                    {{ __('commerce.order_action_' . $next) }}
                                </button>
                            </form>
                        @endforeach
                        @if ($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                            <form method="POST" action="{{ route('commerce.orders.paid', $order) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-cash-coin me-1"></i>{{ __('commerce.order_action_mark_paid') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        {{ $orders->links() }}
    @endif
</x-app-layout>
