<x-app-layout>
    <x-slot name="title">{{ __('procurement.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('procurement.title') }}</x-slot>

    <x-ui.page-header :title="__('procurement.title')" :subtitle="__('procurement.subtitle')" />

    <x-ui.flash :session="false" :with-errors="true" />

    <div class="row g-4">
        {{-- Suppliers --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-truck me-2"></i>{{ __('procurement.suppliers') }}</div>
                <div class="card-body">
                    @if ($suppliers->isEmpty())
                        <p class="text-muted small">{{ __('procurement.no_suppliers') }}</p>
                    @endif
                    @foreach ($suppliers as $supplier)
                        <div class="d-flex justify-content-between align-items-center py-1 small">
                            <div>
                                <span class="fw-medium">{{ $supplier->name }}</span>
                                @if ($supplier->category)<span class="text-muted"> · {{ $supplier->category }}</span>@endif
                            </div>
                            <span class="text-muted">
                                @if ($supplier->rating_avg)
                                    <i class="bi bi-star-fill text-warning"></i> {{ $supplier->rating_avg }} ({{ $supplier->rating_count }})
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                    @endforeach
                    <hr>
                    <form method="POST" action="{{ route('procurement.suppliers.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="s-name">{{ __('procurement.supplier_name') }}</label>
                            <input type="text" id="s-name" name="name" required maxlength="150" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="s-category">{{ __('procurement.supplier_category') }}</label>
                            <input type="text" id="s-category" name="category" maxlength="100" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-sm" for="s-phone">{{ __('procurement.supplier_phone') }}</label>
                            <input type="tel" id="s-phone" name="phone" maxlength="30" class="form-control form-control-sm">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('procurement.add_supplier') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Purchase requests --}}
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-semibold"><i class="bi bi-clipboard-plus me-2"></i>{{ __('procurement.requests') }}</div>
                <div class="card-body p-0">
                    @if ($requests->isEmpty())
                        <x-ui.empty-state icon="bi-clipboard-plus" :title="__('procurement.no_requests')" :body="__('procurement.no_requests_body')" />
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($requests as $pr)
                                <div class="list-group-item d-flex align-items-center gap-3 py-2">
                                    <div class="flex-grow-1">
                                        <span class="fw-medium">{{ $pr->title }}</span>
                                        <div class="text-muted small">
                                            {{ $pr->supplier?->name ?? '—' }} ·
                                            {{ number_format((float) $pr->estimated_cost, 2) }}
                                        </div>
                                    </div>
                                    <x-ui.status-badge :status="$pr->status" :label="__('procurement.status_' . $pr->status)" />
                                    @if ($pr->status === 'draft')
                                        <form method="POST" action="{{ route('procurement.requests.submit', $pr) }}">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('procurement.submit') }}</button>
                                        </form>
                                    @elseif ($pr->status === 'submitted')
                                        <form method="POST" action="{{ route('procurement.requests.approve', $pr) }}">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('procurement.approve') }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Purchase orders --}}
            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-box-seam me-2"></i>{{ __('procurement.orders') }}</div>
                <div class="card-body p-0">
                    @if ($orders->isEmpty())
                        <x-ui.empty-state icon="bi-box-seam" :title="__('procurement.no_orders')" />
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($orders as $po)
                                <div class="list-group-item d-flex align-items-center gap-3 py-2">
                                    <div class="flex-grow-1">
                                        <span class="fw-medium">PO-{{ $po->id }}</span>
                                        <span class="text-muted small">· {{ $po->supplier?->name }} · {{ number_format((float) $po->total_cost, 2) }}</span>
                                    </div>
                                    <x-ui.status-badge :status="$po->status" :label="__('procurement.status_' . $po->status)" />
                                    @if ($po->status === 'ordered')
                                        <form method="POST" action="{{ route('procurement.orders.receive', $po) }}">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('procurement.receive') }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
