<x-app-layout>
    <x-slot name="title">{{ __('members.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('members.title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ __('members.title') }}</h1>
            <p>{{ __('members.subtitle') }}</p>
        </div>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> {{ __('members.add_member') }}
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-3">
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
                <div class="text-center py-5">
                    <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi bi-people text-primary"></i>
                    </div>
                    @if(request('search_name') || request('search_phone'))
                        <h5 class="fw-semibold mb-2">{{ __('members.empty_search_title') }}</h5>
                        <p class="text-muted mb-0" style="max-width:380px;margin:0 auto;">
                            {!! __('members.empty_search_body', ['link' => route('members', ['filter' => $filter])]) !!}
                        </p>
                    @elseif($filter === 'archived')
                        <h5 class="fw-semibold mb-2">{{ __('members.empty_archived_title') }}</h5>
                        <p class="text-muted mb-0" style="max-width:380px;margin:0 auto;">
                            {{ __('members.empty_archived_body') }}
                        </p>
                    @else
                        <h5 class="fw-semibold mb-2">{{ __('members.empty_title') }}</h5>
                        <p class="text-muted mb-0" style="max-width:380px;margin:0 auto;">
                            {{ __('members.empty_body') }}
                        </p>
                    @endif
                </div>
            @else
                <div class="table-responsive">
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
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary disabled"
                                                title="Coming in a future task">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
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
