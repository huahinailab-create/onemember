<x-app-layout>
    <x-slot name="title">{{ __('campaigns.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('campaigns.title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ __('campaigns.title') }}</h1>
            <p>{{ __('campaigns.subtitle') }}</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0 align-items-center">
            <x-ui.help-button topic="campaigns.index" />
            <a href="{{ route('campaigns.create') }}" class="btn btn-primary flex-shrink-0">
                <i class="bi bi-plus-lg me-1"></i> {{ __('campaigns.create_campaign') }}
            </a>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-3 filter-scroll">
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
                @if (request('search_name'))
                    <x-ui.empty-state icon="bi-search" :title="__('campaigns.empty_search_title')">
                        <p class="mb-0" style="font-size:0.875rem;max-width:380px;margin:0 auto;">
                            {!! __('campaigns.empty_search_body', ['link' => route('campaigns.index', ['filter' => $filter])]) !!}
                        </p>
                    </x-ui.empty-state>
                @elseif ($filter === 'archived')
                    <x-ui.empty-state icon="bi-archive" :title="__('campaigns.empty_archived_title')" :body="__('campaigns.empty_archived_body')" />
                @else
                    <x-ui.empty-state icon="bi-star" :title="__('campaigns.empty_title')" :body="__('campaigns.empty_state_body')" help-topic="campaigns.index">
                        <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('campaigns.create_campaign') }}
                        </a>
                    </x-ui.empty-state>
                @endif
            @else
                {{-- Mobile card list (xs) --}}
                <div class="d-sm-none">
                    @foreach ($campaigns as $campaign)
                        <a href="{{ route('campaigns.show', $campaign) }}"
                           class="d-flex align-items-center gap-3 px-3 py-3 border-bottom text-decoration-none {{ $campaign->trashed() ? 'text-muted' : '' }}">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                 style="width:40px;height:40px;background:var(--om-icon-bg);color:var(--om-navy);font-size:1.1rem;">
                                <i class="bi {{ $campaign->type->icon() }}"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold text-truncate" style="color:var(--om-ink);">{{ $campaign->name }}</div>
                                <div class="text-muted small">{{ $campaign->type->label() }}</div>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                @if ($campaign->trashed())
                                    <span class="badge bg-danger" style="font-size:.65rem;">{{ __('campaigns.status_archived') }}</span>
                                @else
                                    <span class="{{ $campaign->status->badgeClass() }}" style="font-size:.65rem;">{{ $campaign->status->label() }}</span>
                                @endif
                                <div class="text-muted mt-1" style="font-size:.7rem;">{{ $campaign->updated_at->format('d M Y') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Desktop table (sm+) --}}
                <div class="table-responsive d-none d-sm-block">
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
