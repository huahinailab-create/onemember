<x-app-layout>
    <x-slot name="title">{{ __('commerce.products_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('commerce.products_title') }}</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1>{{ __('commerce.products_title') }}</h1>
            <p>{{ __('commerce.products_subtitle') }}</p>
        </div>
        <div class="d-flex gap-2">
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
                <div class="text-center text-muted py-5">
                    <i class="bi bi-box fs-1 d-block mb-2"></i>
                    {{ __('commerce.no_products') }}
                </div>
            @else
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
                                <td class="ps-3 fw-medium">{{ $product->name }}</td>
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
                                    <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ __('commerce.status_' . $product->status) }}
                                    </span>
                                </td>
                                <td class="pe-3 text-end">
                                    <a href="{{ route('commerce.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('commerce.products.archive', $product) }}" class="d-inline"
                                          onsubmit="return confirm('{{ __('commerce.archive_confirm') }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-archive"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        @if ($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
</x-app-layout>
