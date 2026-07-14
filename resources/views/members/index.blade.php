<x-app-layout>
    <x-slot name="title">{{ __('members.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('members.title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ __('members.title') }}</h1>
            <p>{{ __('members.subtitle') }}</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0 flex-wrap align-items-center">
            <x-ui.help-button topic="members.index" />
            @if (config('features.identity'))
                <a href="{{ route('members.identity.add') }}" class="btn btn-outline-primary">
                    <i class="bi bi-qr-code-scan me-1"></i> {{ __('identity.add_button') }}
                </a>
            @endif
            <a href="{{ route('members.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> {{ __('members.add_member') }}
            </a>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-3 filter-scroll">
        <div class="btn-group btn-group-sm" role="group" aria-label="Member filter">
            @foreach (['active' => __('members.filter_active'), 'archived' => __('members.filter_archived'), 'all' => __('members.filter_all')] as $value => $label)
                <a href="{{ route('members', array_merge(request()->except(['filter', 'page']), ['filter' => $value])) }}"
                   class="btn {{ $filter === $value ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('members') }}" class="row g-2 align-items-end">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="col-12 col-md-5">
                    <label for="search_name" class="form-label form-label-sm mb-1">{{ __('members.full_name') }}</label>
                    <input type="text"
                           id="search_name"
                           name="search_name"
                           class="form-control form-control-sm"
                           placeholder="{{ __('members.search_name_ph') }}"
                           value="{{ request('search_name') }}">
                </div>
                <div class="col-12 col-md-5">
                    <label for="search_phone" class="form-label form-label-sm mb-1">{{ __('members.mobile_number') }}</label>
                    <input type="text"
                           id="search_phone"
                           name="search_phone"
                           class="form-control form-control-sm"
                           placeholder="{{ __('members.search_phone_ph') }}"
                           value="{{ request('search_phone') }}">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search me-1"></i> {{ __('buttons.search') }}
                    </button>
                    @if(request('search_name') || request('search_phone'))
                        <a href="{{ route('members', ['filter' => $filter]) }}" class="btn btn-sm btn-outline-secondary w-100">{{ __('buttons.clear') }}</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if ($members->isEmpty())
                @if(request('search_name') || request('search_phone'))
                    <x-ui.empty-state icon="bi-search" :title="__('members.empty_search_title')">
                        <p class="mb-0" style="font-size:0.875rem;max-width:380px;margin:0 auto;">
                            {!! __('members.empty_search_body', ['link' => route('members', ['filter' => $filter])]) !!}
                        </p>
                    </x-ui.empty-state>
                @elseif($filter === 'archived')
                    <x-ui.empty-state icon="bi-archive" :title="__('members.empty_archived_title')" :body="__('members.empty_archived_body')" />
                @else
                    <x-ui.empty-state icon="bi-people" :title="__('members.empty_title')" :body="__('members.empty_body')" help-topic="members.index">
                        <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-person-plus me-1" aria-hidden="true"></i> {{ __('members.add_member') }}
                        </a>
                    </x-ui.empty-state>
                @endif
            @else
                {{-- ── Mobile card list (xs only) ── --}}
                <div class="d-sm-none">
                    @foreach ($members as $member)
                        <a href="{{ route('members.show', $member) }}"
                           class="d-flex align-items-center gap-3 px-3 py-3 border-bottom text-decoration-none {{ $member->trashed() ? 'text-muted' : '' }}">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 fw-bold"
                                 style="width:40px;height:40px;font-size:0.875rem;background:var(--om-icon-bg);color:var(--om-navy);">
                                {{ strtoupper(mb_substr($member->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold text-truncate" style="color:var(--om-ink);">{{ $member->name }}</div>
                                <div class="text-muted small text-truncate">{{ $member->phone ?? '—' }}</div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold small" style="color:var(--om-navy);">{{ number_format($member->total_points) }}</div>
                                <div class="mt-1">
                                    @if ($member->trashed())
                                        <span class="badge bg-danger" style="font-size:.65rem;">{{ __('members.status_archived') }}</span>
                                    @else
                                        <span class="{{ $member->status->badgeClass() }}" style="font-size:.65rem;">{{ $member->status->label() }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- ── Desktop table (sm+) ── --}}
                <div class="table-responsive d-none d-sm-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width:80px;">{{ __('members.col_code') }}</th>
                                <th>
                                    @php $nameDir = ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                    <a href="{{ route('members', array_merge(request()->query(), ['sort' => 'name', 'direction' => $nameDir])) }}"
                                       class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                        {{ __('members.full_name') }}
                                        @if($sort === 'name')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted" style="font-size:.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>{{ __('members.nickname') }}</th>
                                <th>{{ __('members.mobile_number') }}</th>
                                <th>{{ __('members.email') }}</th>
                                <th>
                                    @php $bdDir = ($sort === 'birthday' && $direction === 'asc') ? 'desc' : 'asc'; @endphp
                                    <a href="{{ route('members', array_merge(request()->query(), ['sort' => 'birthday', 'direction' => $bdDir])) }}"
                                       class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                                        {{ __('members.date_of_birth') }}
                                        @if($sort === 'birthday')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted" style="font-size:.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-end">{{ __('members.col_points') }}</th>
                                <th>{{ __('members.col_status') }}</th>
                                <th class="text-end pe-4">{{ __('members.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr class="{{ $member->trashed() ? 'text-muted' : '' }}">
                                    <td class="ps-4">
                                        <span class="badge bg-light text-secondary border font-monospace" style="font-size:.7rem;">
                                            {{ $member->member_code }}
                                        </span>
                                    </td>
                                    <td class="fw-medium">
                                        <a href="{{ route('members.show', $member) }}" class="text-decoration-none {{ $member->trashed() ? 'text-muted' : '' }}">
                                            {{ $member->name }}
                                        </a>
                                    </td>
                                    <td>{{ $member->nickname ?? '—' }}</td>
                                    <td>{{ $member->phone ?? '—' }}</td>
                                    <td>{{ $member->email ?? '—' }}</td>
                                    <td>
                                        @if ($member->birthday)
                                            {{ $member->birthday->format('d M Y') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($member->total_points) }}
                                    </td>
                                    <td>
                                        @if ($member->trashed())
                                            <span class="badge bg-danger">{{ __('members.status_archived') }}</span>
                                        @else
                                            <span class="{{ $member->status->badgeClass() }}">
                                                {{ $member->status->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           aria-label="{{ __('buttons.view') }}: {{ $member->name }}">
                                            {{ __('buttons.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($members->hasPages())
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
                        <div class="text-muted" style="font-size:.8125rem;">
                            {{ __('members.pagination_showing', ['first' => $members->firstItem(), 'last' => $members->lastItem(), 'total' => $members->total()]) }}
                        </div>
                        <div>
                            {{ $members->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="px-4 py-3 border-top text-muted" style="font-size:.8125rem;">
                        {{ trans_choice('members.count', $members->total(), ['count' => $members->total()]) }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</x-app-layout>
