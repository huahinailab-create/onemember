<x-app-layout>
    <x-slot name="title">{{ __('commerce.products_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('commerce.products_title') }}</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1>{{ __('commerce.products_title') }}</h1>
            <p>{{ __('commerce.products_subtitle') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('commerce.orders.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-receipt me-1"></i>{{ __('commerce.orders_button') }}
            </a>
            <a href="{{ route('commerce.settings') }}" class="btn btn-outline-secondary">
                <i class="bi bi-truck me-1"></i>{{ __('commerce.fulfillment_button') }}
            </a>
            <a href="{{ route('commerce.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>{{ __('commerce.add_product') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if ($products->isEmpty())
                <x-ui.empty-state icon="bi-box" :title="__('commerce.no_products')">
                    <a href="{{ route('commerce.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('commerce.add_product') }}
                    </a>
                </x-ui.empty-state>
            @else
                <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">{{ __('commerce.col_name') }}</th>
                            <th>{{ __('commerce.col_category') }}</th>
                            <th>{{ __('commerce.col_price') }}</th>
                            <th>{{ __('commerce.col_stock') }}</th>
                            <th>{{ __('commerce.col_status') }}</th>
                            <th class="pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="ps-3 fw-medium">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="commerce-product-thumb flex-shrink-0">
                                            @if ($product->imageUrl())
                                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
                                            @else
                                                <i class="bi bi-image text-muted" aria-hidden="true"></i>
                                            @endif
                                        </div>
                                        <span>{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $product->category?->name ?? '—' }}</td>
                                <td>{{ number_format($product->price, 2) }} {{ $product->merchant->currency ?? config('app.default_currency') }}</td>
                                <td>
                                    @if (is_null($product->stock_qty))
                                        <span class="text-muted">{{ __('commerce.stock_untracked') }}</span>
                                    @elseif ($product->stock_qty === 0)
                                        <span class="badge bg-danger">{{ __('commerce.stock_out') }}</span>
                                    @else
                                        {{ number_format($product->stock_qty) }}
                                    @endif
                                </td>
                                <td>
                                    <x-ui.status-badge :status="$product->status" :label="__('commerce.status_' . $product->status)" />
                                </td>
                                <td class="pe-3 text-end">
                                    <a href="{{ route('commerce.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary"
                                       aria-label="{{ __('buttons.edit') }}: {{ $product->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('commerce.products.archive', $product) }}" class="d-inline"
                                          onsubmit="return confirm('{{ __('commerce.archive_confirm') }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                aria-label="{{ __('buttons.archive') }}: {{ $product->name }}">
                                            <i class="bi bi-archive" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>
        @if ($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
</x-app-layout>
