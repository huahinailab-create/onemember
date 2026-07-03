<x-app-layout>
    <x-slot name="title">{{ __('campaigns.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('campaigns.title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ __('campaigns.title') }}</h1>
            <p>{{ __('campaigns.subtitle') }}</p>
        </div>
        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> {{ __('campaigns.create_campaign') }}
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-3">
        <div class="btn-group btn-group-sm" role="group" aria-label="Campaign filter">
            @foreach ([
                'active'   => __('campaigns.filter_active'),
                'draft'    => __('campaigns.filter_draft'),
                'paused'   => __('campaigns.filter_paused'),
                'archived' => __('campaigns.filter_archived'),
                'all'      => __('campaigns.filter_all'),
            ] as $value => $label)
                <a href="{{ route('campaigns.index', array_merge(request()->except(['filter', 'page']), ['filter' => $value])) }}"
                   class="btn {{ $filter === $value ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Search --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('campaigns.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="col-12 col-md-8">
                    <label for="search_name" class="form-label form-label-sm mb-1">{{ __('campaigns.campaign_name') }}</label>
                    <input type="text"
                           id="search_name"
                           name="search_name"
                           class="form-control form-control-sm"
                           placeholder="{{ __('campaigns.search_ph') }}"
                           value="{{ request('search_name') }}">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search me-1"></i> {{ __('buttons.search') }}
                    </button>
                    @if (request('search_name'))
                        <a href="{{ route('campaigns.index', ['filter' => $filter]) }}" class="btn btn-sm btn-outline-secondary w-100">{{ __('buttons.clear') }}</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if ($campaigns->isEmpty())
                <div class="text-center py-5">
                    <div class="coming-soon-icon mx-auto">
                        <i class="bi bi-star text-primary"></i>
                    </div>
                    @if (request('search_name'))
                        <h5 class="fw-semibold mb-2">{{ __('campaigns.empty_search_title') }}</h5>
                        <p class="text-muted mb-0" style="max-width:380px;margin:0 auto;">
                            {!! __('campaigns.empty_search_body', ['link' => route('campaigns.index', ['filter' => $filter])]) !!}
                        </p>
                    @elseif ($filter === 'archived')
                        <h5 class="fw-semibold mb-2">{{ __('campaigns.empty_archived_title') }}</h5>
                        <p class="text-muted mb-0" style="max-width:380px;margin:0 auto;">
                            {{ __('campaigns.empty_archived_body') }}
                        </p>
                    @else
                        <h5 class="fw-semibold mb-2">{{ __('campaigns.empty_title') }}</h5>
                        <p class="text-muted mb-0" style="max-width:380px;margin:0 auto;">
                            {!! __('campaigns.empty_body', ['link' => route('campaigns.create')]) !!}
                        </p>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">{{ __('campaigns.col_name') }}</th>
                                <th>{{ __('campaigns.col_type') }}</th>
                                <th>{{ __('campaigns.col_status') }}</th>
                                <th>{{ __('campaigns.col_updated') }}</th>
                                <th class="text-end pe-4">{{ __('campaigns.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $campaign)
                                <tr class="{{ $campaign->trashed() ? 'text-muted' : '' }}">
                                    <td class="ps-4 fw-medium">
                                        <a href="{{ route('campaigns.show', $campaign) }}"
                                           class="text-decoration-none {{ $campaign->trashed() ? 'text-muted' : '' }}">
                                            {{ $campaign->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="d-flex align-items-center gap-1">
                                            <i class="bi {{ $campaign->type->icon() }} text-muted" style="font-size:.875rem;"></i>
                                            {{ $campaign->type->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($campaign->trashed())
                                            <span class="badge bg-danger">{{ __('campaigns.status_archived') }}</span>
                                        @else
                                            <span class="{{ $campaign->status->badgeClass() }}">
                                                {{ $campaign->status->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted" style="font-size:.875rem;">
                                        {{ $campaign->updated_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('campaigns.show', $campaign) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>{{ __('buttons.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($campaigns->hasPages())
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                        <div class="text-muted" style="font-size:.8125rem;">
                            {{ __('campaigns.pagination_showing', ['first' => $campaigns->firstItem(), 'last' => $campaigns->lastItem(), 'total' => $campaigns->total()]) }}
                        </div>
                        <div>
                            {{ $campaigns->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="px-4 py-3 border-top text-muted" style="font-size:.8125rem;">
                        {{ trans_choice('campaigns.count', $campaigns->total(), ['count' => $campaigns->total()]) }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-app-layout>
