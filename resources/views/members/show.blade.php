<x-app-layout>
    <x-slot name="title">{{ $member->name }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Members</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="mb-1">
                <a href="{{ route('members') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Back to Members
                </a>
            </div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                {{ $member->name }}
                <span class="{{ $member->status->badgeClass() }} fs-6 fw-normal">
                    {{ $member->status->label() }}
                </span>
            </h1>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <button type="button" class="btn btn-outline-primary disabled" title="Coming in a future task">
                <i class="bi bi-pencil me-1"></i>Edit
            </button>
            <a href="{{ route('members') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Profile Card --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person text-primary"></i>
                    <span class="fw-semibold">Profile</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0" style="row-gap:.75rem;">
                        <dt class="col-5 text-muted fw-normal small">Full Name</dt>
                        <dd class="col-7 mb-0 fw-medium">{{ $member->name }}</dd>

                        <dt class="col-5 text-muted fw-normal small">Nickname</dt>
                        <dd class="col-7 mb-0">{{ $member->nickname ?? '—' }}</dd>

                        <dt class="col-5 text-muted fw-normal small">Mobile Number</dt>
                        <dd class="col-7 mb-0">{{ $member->phone ?? '—' }}</dd>

                        <dt class="col-5 text-muted fw-normal small">Email</dt>
                        <dd class="col-7 mb-0">
                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}" class="text-decoration-none">{{ $member->email }}</a>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-5 text-muted fw-normal small">Birthday</dt>
                        <dd class="col-7 mb-0">
                            {{ $member->birthday ? $member->birthday->format('d M Y') : '—' }}
                        </dd>

                        <dt class="col-5 text-muted fw-normal small">Member Since</dt>
                        <dd class="col-7 mb-0">{{ $member->joined_at->format('d M Y') }}</dd>

                        <dt class="col-5 text-muted fw-normal small">Status</dt>
                        <dd class="col-7 mb-0">
                            <span class="{{ $member->status->badgeClass() }}">{{ $member->status->label() }}</span>
                        </dd>

                        @if ($member->notes)
                            <dt class="col-5 text-muted fw-normal small">Notes</dt>
                            <dd class="col-7 mb-0" style="white-space:pre-line;">{{ $member->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7 d-flex flex-column gap-3">

            {{-- QR Card --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-qr-code text-primary"></i>
                    <span class="fw-semibold">QR Code</span>
                </div>
                <div class="card-body text-center py-4">
                    <div class="coming-soon-icon bg-secondary bg-opacity-10 mx-auto">
                        <i class="bi bi-qr-code text-secondary"></i>
                    </div>
                    <p class="text-muted mb-1 fw-medium">QR Code — Coming Soon</p>
                    <p class="text-muted mb-0 small">
                        Member code: <span class="font-monospace fw-semibold">{{ $member->member_code }}</span>
                    </p>
                </div>
            </div>

            {{-- Loyalty Card --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-star text-primary"></i>
                    <span class="fw-semibold">Loyalty</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 rounded bg-primary bg-opacity-10">
                                <div class="fs-3 fw-bold text-primary">{{ number_format($member->total_points) }}</div>
                                <div class="text-muted small mt-1">Current Points</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-secondary bg-opacity-10">
                                <div class="fs-3 fw-bold text-secondary">{{ number_format($member->lifetime_points) }}</div>
                                <div class="text-muted small mt-1">Lifetime Points</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs px-3" id="memberTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-profile" data-bs-toggle="tab"
                            data-bs-target="#pane-profile" type="button" role="tab">
                        <i class="bi bi-person me-1"></i>Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-points" data-bs-toggle="tab"
                            data-bs-target="#pane-points" type="button" role="tab">
                        <i class="bi bi-clock-history me-1"></i>Points History
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-rewards" data-bs-toggle="tab"
                            data-bs-target="#pane-rewards" type="button" role="tab">
                        <i class="bi bi-gift me-1"></i>Rewards
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-transactions" data-bs-toggle="tab"
                            data-bs-target="#pane-transactions" type="button" role="tab">
                        <i class="bi bi-arrow-left-right me-1"></i>Transactions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-notes" data-bs-toggle="tab"
                            data-bs-target="#pane-notes" type="button" role="tab">
                        <i class="bi bi-journal-text me-1"></i>Notes
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="memberTabsContent">

            {{-- Profile Tab --}}
            <div class="tab-pane fade show active p-4" id="pane-profile" role="tabpanel">
                <dl class="row mb-0" style="row-gap:.75rem;">
                    <dt class="col-sm-3 text-muted fw-normal">Full Name</dt>
                    <dd class="col-sm-9 mb-0 fw-medium">{{ $member->name }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">Nickname</dt>
                    <dd class="col-sm-9 mb-0">{{ $member->nickname ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">Mobile Number</dt>
                    <dd class="col-sm-9 mb-0">{{ $member->phone ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">Email</dt>
                    <dd class="col-sm-9 mb-0">
                        @if ($member->email)
                            <a href="mailto:{{ $member->email }}" class="text-decoration-none">{{ $member->email }}</a>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">Birthday</dt>
                    <dd class="col-sm-9 mb-0">
                        {{ $member->birthday ? $member->birthday->format('d M Y') : '—' }}
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">Member Since</dt>
                    <dd class="col-sm-9 mb-0">{{ $member->joined_at->format('d M Y') }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">Status</dt>
                    <dd class="col-sm-9 mb-0">
                        <span class="{{ $member->status->badgeClass() }}">{{ $member->status->label() }}</span>
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">Member Code</dt>
                    <dd class="col-sm-9 mb-0 font-monospace">{{ $member->member_code }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">Notes</dt>
                    <dd class="col-sm-9 mb-0">
                        @if ($member->notes)
                            <span style="white-space:pre-line;">{{ $member->notes }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>
                </dl>
            </div>

            {{-- Coming Soon Tabs --}}
            @foreach ([
                'pane-points'       => ['icon' => 'bi-clock-history', 'label' => 'Points History'],
                'pane-rewards'      => ['icon' => 'bi-gift',          'label' => 'Rewards'],
                'pane-transactions' => ['icon' => 'bi-arrow-left-right', 'label' => 'Transactions'],
                'pane-notes'        => ['icon' => 'bi-journal-text',  'label' => 'Notes'],
            ] as $paneId => $meta)
                <div class="tab-pane fade text-center py-5" id="{{ $paneId }}" role="tabpanel">
                    <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi {{ $meta['icon'] }} text-primary"></i>
                    </div>
                    <h6 class="fw-semibold mb-1">{{ $meta['label'] }} — Coming Soon</h6>
                    <p class="text-muted mb-0 small">This feature will be available in a future sprint.</p>
                </div>
            @endforeach

        </div>
    </div>

</x-app-layout>
