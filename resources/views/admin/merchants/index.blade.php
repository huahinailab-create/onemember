<x-admin-layout title="Merchants">

    {{-- Search / Filter bar --}}
    <form method="GET" action="{{ route('admin.merchants.index') }}" class="stat-card card p-3 mb-4">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-md-4">
                <label class="form-label fw-500 mb-1" style="font-size:0.78rem;color:#1A2E5A;font-weight:500;">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm"
                       placeholder="Name, contact, or email…">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-500 mb-1" style="font-size:0.78rem;color:#1A2E5A;font-weight:500;">Plan</label>
                <select name="plan" class="form-select form-select-sm">
                    <option value="">All plans</option>
                    @foreach($plans as $p)
                    <option value="{{ $p->value }}" {{ request('plan') === $p->value ? 'selected' : '' }}>
                        {{ $p->label() }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-500 mb-1" style="font-size:0.78rem;color:#1A2E5A;font-weight:500;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach($statuses as $s)
                    <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>
                        {{ $s->label() }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label fw-500 mb-1" style="font-size:0.78rem;color:#1A2E5A;font-weight:500;">Subscription</label>
                <select name="subscription_status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($subStatuses as $s)
                    <option value="{{ $s->value }}" {{ request('subscription_status') === $s->value ? 'selected' : '' }}>
                        {{ $s->label() }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label fw-500 mb-1" style="font-size:0.78rem;color:#1A2E5A;font-weight:500;">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label fw-500 mb-1" style="font-size:0.78rem;color:#1A2E5A;font-weight:500;">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
            </div>

            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm"
                        style="background:#1A2E5A;color:#fff;font-weight:500;">
                    Filter
                </button>
                @if(request()->hasAny(['search','plan','status','subscription_status','from','to']))
                <a href="{{ route('admin.merchants.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </div>

        </div>
    </form>

    {{-- Results count --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-0 text-muted" style="font-size:0.82rem;">
            {{ number_format($merchants->total()) }} merchant{{ $merchants->total() !== 1 ? 's' : '' }} found
        </p>
    </div>

    {{-- Table --}}
    <div class="stat-card card">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" style="font-size:0.83rem;">
                <thead>
                    <tr class="text-muted border-bottom" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;">
                        <th class="px-3 py-2">Business</th>
                        <th class="py-2">Owner</th>
                        <th class="py-2">Email</th>
                        <th class="py-2">Plan</th>
                        <th class="py-2">Subscription</th>
                        <th class="py-2">Trial ends</th>
                        <th class="py-2 text-end">Members</th>
                        <th class="py-2 text-end">Transactions</th>
                        <th class="py-2">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchants as $m)
                    <tr class="border-bottom border-light">
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.merchants.show', $m) }}"
                               style="color:#1A2E5A;text-decoration:none;font-weight:600;">
                                {{ $m->name }}
                            </a>
                            @if($m->deleted_at)
                                <span class="badge bg-danger ms-1" style="font-size:0.65rem;">Deleted</span>
                            @endif
                        </td>
                        <td class="text-muted py-2">{{ $m->owner?->name ?? '—' }}</td>
                        <td class="text-muted py-2">{{ $m->owner?->email ?? '—' }}</td>
                        <td class="py-2">
                            <span class="badge" style="background:#EEF2FF;color:#4F46E5;font-size:0.7rem;font-weight:500;">
                                {{ $m->subscription_plan?->label() ?? '—' }}
                            </span>
                        </td>
                        <td class="py-2">
                            @if($m->subscription_status)
                            <span class="badge {{ $m->subscription_status->badgeClass() }}" style="font-size:0.7rem;">
                                {{ $m->subscription_status->label() }}
                            </span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-muted py-2">
                            @if($m->trial_ends_at)
                                <span class="{{ $m->trial_ends_at->isPast() ? 'text-danger' : ($m->trial_ends_at->diffInDays() <= 7 ? 'text-warning' : '') }}">
                                    {{ $m->trial_ends_at->format('d M Y') }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end py-2 fw-600">{{ number_format($m->members_count) }}</td>
                        <td class="text-end py-2 fw-600">{{ number_format($m->transactions_count) }}</td>
                        <td class="text-muted py-2">{{ $m->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No merchants found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($merchants->hasPages())
        <div class="px-3 py-2 border-top">
            {{ $merchants->links() }}
        </div>
        @endif
    </div>

</x-admin-layout>
