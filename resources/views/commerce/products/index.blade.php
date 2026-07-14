<x-app-layout>
    <x-slot name="title">{{ __('commerce.products_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('commerce.products_title') }}</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1>{{ __('commerce.products_title') }}</h1>
            <p>{{ __('commerce.products_subtitle') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <x-ui.help-button topic="commerce.products" />
            <a href="{{ route('storefront.show', $merchant->slug) }}" target="_blank" rel="noopener"
               class="btn btn-accent">
                <i class="bi bi-shop me-1" aria-hidden="true"></i>{{ __('commerce.view_store_button') }}
                <i class="bi bi-box-arrow-up-right ms-1 small" aria-hidden="true"></i>
                <span class="visually-hidden">{{ __('commerce.opens_new_tab') }}</span>
            </a>
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
                <x-ui.empty-state icon="bi-box-seam" :title="__('commerce.no_products')" :body="__('commerce.no_products_body')" help-topic="commerce.products">
                    <a href="{{ route('commerce.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>{{ __('commerce.add_product') }}
                    </a>
                </x-ui.empty-state>
            @else
                <div class="table-responsive">
                <table class="table table-hover table-sticky align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="ps-3">{{ __('commerce.col_name') }}</th>
                            <th scope="col" class="d-none d-md-table-cell">{{ __('commerce.col_category') }}</th>
                            <th scope="col">{{ __('commerce.col_price') }}</th>
                            <th scope="col">{{ __('commerce.col_stock') }}</th>
                            <th scope="col">{{ __('commerce.col_status') }}</th>
                            <th scope="col" class="pe-3"><span class="visually-hidden">{{ __('members.col_actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="{{ $product->trashed() ? 'text-muted' : '' }}">
                                <td class="ps-3 fw-medium py-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="commerce-product-thumb flex-shrink-0">
                                            @if ($product->imageUrl())
                                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy">
                                            @else
                                                <i class="bi bi-image text-muted" aria-hidden="true"></i>
                                            @endif
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-truncate">{{ $product->name }}</div>
                                            @if ($product->trashed())
                                                <span class="badge bg-danger" style="font-size:.65rem;">{{ __('commerce.badge_archived') }}</span>
                                            @endif
                                            @if ($product->category)
                                                <div class="text-muted small fw-normal d-md-none">{{ $product->category->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted d-none d-md-table-cell">{{ $product->category?->name ?? '—' }}</td>
                                <td class="text-nowrap">{{ number_format($product->price, 2) }} {{ $product->merchant->currency ?? config('app.default_currency') }}</td>
                                <td>
                                    @if (is_null($product->stock_qty))
                                        <span class="text-muted small">{{ __('commerce.stock_untracked') }}</span>
                                    @elseif ($product->stock_qty === 0)
                                        <span class="badge bg-danger">{{ __('commerce.stock_out') }}</span>
                                    @elseif ($product->stock_qty <= 5)
                                        <span class="badge bg-warning text-dark">{{ __('commerce.stock_low', ['count' => $product->stock_qty]) }}</span>
                                    @else
                                        <span class="badge bg-light text-secondary border">{{ __('commerce.stock_in', ['count' => number_format($product->stock_qty)]) }}</span>
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
