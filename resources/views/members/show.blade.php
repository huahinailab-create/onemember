<x-app-layout>
    <x-slot name="title">{{ $member->name }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('members.title') }}</x-slot>

    @php $isArchived = $member->trashed(); @endphp

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start justify-content-between gap-3">
        <div>
            <div class="mb-1">
                <a href="{{ route('members') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('members.back_to_members') }}
                </a>
            </div>
            <h1 class="d-flex align-items-center gap-2 flex-wrap">
                {{ $member->name }}
                @if ($isArchived)
                    <span class="badge bg-danger fs-6 fw-normal">{{ __('members.status_archived') }}</span>
                @else
                    <span class="{{ $member->status->badgeClass() }} fs-6 fw-normal">
                        {{ $member->status->label() }}
                    </span>
                @endif
            </h1>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            @if ($isArchived)
                <button type="button" class="btn btn-outline-success disabled" title="Coming in a future sprint">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('members.restore_member') }}
                    <span class="badge bg-secondary ms-1" style="font-size:.65rem;">{{ __('buttons.coming_soon') }}</span>
                </button>
            @else
                <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#archiveModal">
                    <i class="bi bi-archive me-1"></i>{{ __('members.archive_member') }}
                </button>
            @endif
            <a href="{{ route('members') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Profile Card --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person text-primary"></i>
                    <span class="fw-semibold">{{ __('members.profile_tab') }}</span>
                    @if ($isArchived)
                        <span class="badge bg-danger ms-auto" style="font-size:.65rem;">{{ __('members.read_only') }}</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('members.update', $member) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Full Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label form-label-sm">
                                {{ __('members.full_name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name', $member->name) }}"
                                   maxlength="150"
                                   required
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nickname --}}
                        <div class="mb-3">
                            <label for="nickname" class="form-label form-label-sm">{{ __('members.nickname') }}</label>
                            <input type="text"
                                   id="nickname"
                                   name="nickname"
                                   class="form-control form-control-sm @error('nickname') is-invalid @enderror"
                                   value="{{ old('nickname', $member->nickname) }}"
                                   maxlength="50"
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('nickname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label form-label-sm">
                                {{ __('members.mobile_number') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $member->phone) }}"
                                   maxlength="30"
                                   required
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label form-label-sm">{{ __('members.email') }}</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control form-control-sm @error('email') is-invalid @enderror"
                                   value="{{ old('email', $member->email) }}"
                                   maxlength="255"
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Birthday --}}
                        <div class="mb-3">
                            <label for="birthday" class="form-label form-label-sm">
                                {{ __('members.date_of_birth') }} <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="birthday"
                                   name="birthday"
                                   class="form-control form-control-sm @error('birthday') is-invalid @enderror"
                                   value="{{ old('birthday', $member->birthday?->format('Y-m-d')) }}"
                                   required
                                   {{ $isArchived ? 'disabled' : '' }}>
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label for="notes" class="form-label form-label-sm">{{ __('members.notes') }}</label>
                            <textarea id="notes"
                                      name="notes"
                                      class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                      rows="3"
                                      maxlength="500"
                                      {{ $isArchived ? 'disabled' : '' }}>{{ old('notes', $member->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        {{-- Read-only fields --}}
                        <dl class="row mb-0 small" style="row-gap:.5rem;">
                            <dt class="col-5 text-muted fw-normal">{{ __('members.member_since') }}</dt>
                            <dd class="col-7 mb-0">{{ $member->joined_at->format('d M Y') }}</dd>

                            <dt class="col-5 text-muted fw-normal">{{ __('members.col_status') }}</dt>
                            <dd class="col-7 mb-0">
                                @if ($isArchived)
                                    <span class="badge bg-danger">{{ __('members.status_archived') }}</span>
                                @else
                                    <span class="{{ $member->status->badgeClass() }}">{{ $member->status->label() }}</span>
                                @endif
                            </dd>

                            <dt class="col-5 text-muted fw-normal">{{ __('members.member_code') }}</dt>
                            <dd class="col-7 mb-0 font-monospace">{{ $member->member_code }}</dd>
                        </dl>

                    </div>

                    @unless ($isArchived)
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i>{{ __('buttons.save_changes') }}
                            </button>
                            <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary btn-sm">
                                {{ __('buttons.discard') }}
                            </a>
                        </div>
                    @endunless

                </form>
            </div>
        </div>

        <div class="col-12 col-lg-7 d-flex flex-column gap-3">

            {{-- QR Card --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-qr-code text-primary"></i>
                    <span class="fw-semibold">{{ __('members.col_code') }}</span>
                </div>
                <div class="card-body text-center py-4">
                    <div class="coming-soon-icon bg-secondary bg-opacity-10 mx-auto">
                        <i class="bi bi-qr-code text-secondary"></i>
                    </div>
                    <p class="text-muted mb-1 fw-medium">{{ __('members.qr_coming_soon') }}</p>
                    <p class="text-muted mb-0 small">
                        {{ __('members.member_code_label') }}: <span class="font-monospace fw-semibold">{{ $member->member_code }}</span>
                    </p>
                </div>
            </div>

            {{-- Loyalty Card --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-star text-primary"></i>
                    <span class="fw-semibold">{{ __('members.loyalty_tab') }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 rounded bg-primary bg-opacity-10">
                                <div class="fs-3 fw-bold text-primary">{{ number_format($member->total_points) }}</div>
                                <div class="text-muted small mt-1">{{ __('members.total_points') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-secondary bg-opacity-10">
                                <div class="fs-3 fw-bold text-secondary">{{ number_format($member->lifetime_points) }}</div>
                                <div class="text-muted small mt-1">{{ __('members.lifetime_points') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Redemption Success Alert --}}
    @if (session('redemption_success'))
        @php $rs = session('redemption_success'); @endphp
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="fw-semibold mb-2">
                <i class="bi bi-check-circle-fill me-2"></i>Reward Redeemed Successfully
            </div>
            <div class="row g-1 small">
                <div class="col-sm-6">
                    <span class="fw-medium">Reward:</span>
                    {{ $rs['reward_name'] }}
                </div>
                <div class="col-sm-6">
                    <span class="fw-medium">{{ $rs['type'] === 'stamps' ? 'Stamps Used:' : 'Points Used:' }}</span>
                    {{ number_format($rs['points_used']) }}
                </div>
                <div class="col-sm-6">
                    <span class="fw-medium">Current Balance:</span>
                    {{ number_format($rs['balance']) }} {{ $rs['type'] === 'stamps' ? 'Stamps' : 'Points' }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Purchase Success Alert --}}
    @if (session('purchase_success'))
        @php $ps = session('purchase_success'); @endphp
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="fw-semibold mb-2">
                <i class="bi bi-check-circle-fill me-2"></i>Purchase Recorded Successfully
            </div>
            <div class="row g-1 small">
                <div class="col-sm-6">
                    <span class="fw-medium">Purchase:</span>
                    {{ number_format($ps['amount'], 2) }} {{ $ps['currency'] }}
                </div>
                <div class="col-sm-6">
                    <span class="fw-medium">Campaign:</span>
                    {{ $ps['campaign_name'] }}
                </div>
                <div class="col-sm-6">
                    <span class="fw-medium">Earned:</span>
                    {{ $ps['earned'] }} {{ $ps['type'] === 'points'
                        ? ($ps['earned'] === 1 ? 'Point' : 'Points')
                        : ($ps['earned'] === 1 ? 'Stamp' : 'Stamps') }}
                </div>
                <div class="col-sm-6">
                    <span class="fw-medium">Current Balance:</span>
                    {{ number_format($ps['balance']) }} {{ $ps['type'] === 'points' ? 'Points' : 'Stamps' }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Redemption Error Alert --}}
    @if ($errors->has('redemption'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('redemption') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Purchase Error Alert --}}
    @if ($errors->has('purchase'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('purchase') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Record Purchase Card --}}
    <div id="record-purchase-card" class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-bag-plus text-primary"></i>
            <span class="fw-semibold">{{ __('members.record_purchase') }}</span>
        </div>

        @if ($isArchived)
            <div class="card-body py-3">
                <p class="text-muted mb-0 small">
                    <i class="bi bi-lock me-1"></i>{{ __('members.archived_no_purchase') }}
                </p>
            </div>
        @elseif ($member->status !== \App\Enums\MemberStatus::Active)
            <div class="card-body py-3">
                <p class="text-muted mb-0 small">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ __('members.inactive_no_purchase') }}
                </p>
            </div>
        @else
            <form method="POST" action="{{ route('members.purchases.store', $member) }}" novalidate>
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <label for="purchase_amount" class="form-label form-label-sm">
                                {{ __('members.purchase_amount') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number"
                                       id="purchase_amount"
                                       name="purchase_amount"
                                       class="form-control @error('purchase_amount') is-invalid @enderror"
                                       step="0.01"
                                       min="0.01"
                                       value="{{ old('purchase_amount') }}"
                                       placeholder="e.g. 550"
                                       required>
                                <span class="input-group-text">{{ $member->merchant->currency ?? 'THB' }}</span>
                                @error('purchase_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="invoice_number" class="form-label form-label-sm">{{ __('members.invoice_number') }}</label>
                            <input type="text"
                                   id="invoice_number"
                                   name="invoice_number"
                                   class="form-control form-control-sm @error('invoice_number') is-invalid @enderror"
                                   value="{{ old('invoice_number') }}"
                                   maxlength="100"
                                   placeholder="Optional">
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="purchase_note" class="form-label form-label-sm">{{ __('members.notes') }}</label>
                            <input type="text"
                                   id="purchase_note"
                                   name="note"
                                   class="form-control form-control-sm @error('note') is-invalid @enderror"
                                   value="{{ old('note') }}"
                                   maxlength="500"
                                   placeholder="Optional">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-bag-plus me-1"></i>{{ __('members.record_purchase') }}
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- Redeem Reward Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-ticket-perforated text-primary"></i>
            <span class="fw-semibold">{{ __('members.redeem_reward') }}</span>
        </div>

        @if ($isArchived)
            <div class="card-body py-3">
                <p class="text-muted mb-0 small">
                    <i class="bi bi-lock me-1"></i>{{ __('members.archived_no_redeem') }}
                </p>
            </div>
        @elseif ($member->status !== \App\Enums\MemberStatus::Active)
            <div class="card-body py-3">
                <p class="text-muted mb-0 small">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ __('members.inactive_no_redeem') }}
                </p>
            </div>
        @elseif (! $activeCampaign)
            <div class="card-body py-3">
                <p class="text-muted mb-0 small">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ __('members.no_active_campaign') }}
                </p>
            </div>
        @else
            <div class="card-body py-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <p class="text-muted mb-0 small">
                    @if ($eligibleRewards->isEmpty())
                        No rewards are currently available for this member.
                        @if ($activeCampaign->type->value === 'points')
                            The member may need more points to unlock a reward.
                        @else
                            The member may need to complete the stamp card first.
                        @endif
                    @else
                        <span class="text-success fw-medium">
                            {{ $eligibleRewards->count() }} reward{{ $eligibleRewards->count() === 1 ? '' : 's' }} available
                        </span>
                        for this member.
                    @endif
                </p>
                @unless ($eligibleRewards->isEmpty())
                    <button type="button"
                            class="btn btn-primary btn-sm flex-shrink-0"
                            data-bs-toggle="modal"
                            data-bs-target="#redeemModal">
                        <i class="bi bi-ticket-perforated me-1"></i>{{ __('members.redeem_reward') }}
                    </button>
                @endunless
            </div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs px-3" id="memberTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-profile" data-bs-toggle="tab"
                            data-bs-target="#pane-profile" type="button" role="tab">
                        <i class="bi bi-person me-1"></i>{{ __('members.profile_tab') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-points" data-bs-toggle="tab"
                            data-bs-target="#pane-points" type="button" role="tab">
                        <i class="bi bi-clock-history me-1"></i>{{ __('members.points_history_tab') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-rewards" data-bs-toggle="tab"
                            data-bs-target="#pane-rewards" type="button" role="tab">
                        <i class="bi bi-gift me-1"></i>{{ __('members.rewards_tab') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-activity" data-bs-toggle="tab"
                            data-bs-target="#pane-activity" type="button" role="tab">
                        <i class="bi bi-lightning-charge me-1"></i>{{ __('members.activity_tab') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-notes" data-bs-toggle="tab"
                            data-bs-target="#pane-notes" type="button" role="tab">
                        <i class="bi bi-journal-text me-1"></i>{{ __('members.notes_tab') }}
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="memberTabsContent">

            {{-- Profile Tab — read-only summary --}}
            <div class="tab-pane fade show active p-4" id="pane-profile" role="tabpanel">
                <dl class="row mb-0" style="row-gap:.75rem;">
                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.full_name') }}</dt>
                    <dd class="col-sm-9 mb-0 fw-medium">{{ $member->name }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.nickname') }}</dt>
                    <dd class="col-sm-9 mb-0">{{ $member->nickname ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.mobile_number') }}</dt>
                    <dd class="col-sm-9 mb-0">{{ $member->phone ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.email') }}</dt>
                    <dd class="col-sm-9 mb-0">
                        @if ($member->email)
                            <a href="mailto:{{ $member->email }}" class="text-decoration-none">{{ $member->email }}</a>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.birthday') }}</dt>
                    <dd class="col-sm-9 mb-0">
                        {{ $member->birthday ? $member->birthday->format('d M Y') : '—' }}
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.member_since') }}</dt>
                    <dd class="col-sm-9 mb-0">{{ $member->joined_at->format('d M Y') }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.col_status') }}</dt>
                    <dd class="col-sm-9 mb-0">
                        @if ($isArchived)
                            <span class="badge bg-danger">{{ __('members.status_archived') }}</span>
                        @else
                            <span class="{{ $member->status->badgeClass() }}">{{ $member->status->label() }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.member_code_label') }}</dt>
                    <dd class="col-sm-9 mb-0 font-monospace">{{ $member->member_code }}</dd>

                    <dt class="col-sm-3 text-muted fw-normal">{{ __('members.notes') }}</dt>
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
                'pane-points'  => ['icon' => 'bi-clock-history', 'label' => __('members.points_history_tab')],
                'pane-rewards' => ['icon' => 'bi-gift',          'label' => __('members.rewards_tab')],
                'pane-notes'   => ['icon' => 'bi-journal-text',  'label' => __('members.notes_tab')],
            ] as $paneId => $meta)
                <div class="tab-pane fade text-center py-5" id="{{ $paneId }}" role="tabpanel">
                    <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi {{ $meta['icon'] }} text-primary"></i>
                    </div>
                    <h6 class="fw-semibold mb-1">{{ $meta['label'] }} — {{ __('buttons.coming_soon') }}</h6>
                    <p class="text-muted mb-0 small">{{ __('members.coming_soon_note') }}</p>
                </div>
            @endforeach

            {{-- ── Activity Tab ──────────────────────────────── --}}
            <div class="tab-pane fade" id="pane-activity" role="tabpanel">

                {{-- Immutable notice --}}
                <div class="px-4 pt-3 pb-0">
                    <span class="text-muted small">
                        <i class="bi bi-lock me-1"></i>{{ __('members.activity_readonly') }}
                    </span>
                </div>

                {{-- Filter buttons --}}
                <div class="p-3 border-bottom d-flex align-items-center gap-2 flex-wrap">
                    @php
                        $filterOptions = [
                            'all'         => __('members.activity_all'),
                            'purchases'   => __('members.activity_purchases'),
                            'rewards'     => __('members.activity_rewards'),
                            'birthday'    => __('members.activity_birthday'),
                            'adjustments' => __('members.activity_adjustments'),
                            'expired'     => __('members.activity_expired'),
                        ];
                    @endphp
                    <div class="btn-group btn-group-sm" role="group" aria-label="Activity filter">
                        @foreach ($filterOptions as $val => $lbl)
                            <a href="{{ route('members.show', $member) . '?' . http_build_query(['activity_filter' => $val, 'active_tab' => 'activity']) }}"
                               class="btn {{ $activityFilter === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ $lbl }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Activity list --}}
                @if ($transactions->isEmpty())
                    <div class="text-center py-5">
                        <div class="coming-soon-icon bg-primary bg-opacity-10 mx-auto">
                            <i class="bi bi-lightning-charge text-primary"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">{{ __('members.no_activity') }}</h6>
                        <p class="text-muted mb-3 small">
                            {{ __('members.no_activity_hint') }}
                        </p>
                        @unless ($isArchived)
                            <a href="#record-purchase-card" class="btn btn-sm btn-primary">
                                <i class="bi bi-bag-plus me-1"></i>{{ __('members.record_purchase') }}
                            </a>
                        @endunless
                    </div>
                @else
                    <div class="px-4">
                        @foreach ($transactions as $tx)
                            @php
                                $txMeta = [
                                    'earn'     => ['icon' => 'bi-cart-check',        'bg' => 'bg-success',   'text' => 'text-success',  'label' => 'Purchase',          'desc' => 'Purchase recorded'],
                                    'birthday' => ['icon' => 'bi-gift',              'bg' => 'bg-danger',    'text' => 'text-danger',   'label' => 'Birthday Bonus',     'desc' => 'Birthday bonus awarded'],
                                    'redeem'   => ['icon' => 'bi-ticket-perforated', 'bg' => 'bg-warning',   'text' => 'text-warning',  'label' => 'Reward Redemption',  'desc' => 'Reward redeemed'],
                                    'adjust'   => ['icon' => 'bi-pencil-square',     'bg' => 'bg-primary',   'text' => 'text-primary',  'label' => 'Manual Adjustment',  'desc' => 'Points adjusted'],
                                    'expire'   => ['icon' => 'bi-clock-history',     'bg' => 'bg-secondary', 'text' => 'text-secondary','label' => 'Points Expired',     'desc' => 'Points expired'],
                                ];
                                $m       = $txMeta[$tx->type->value] ?? $txMeta['earn'];
                                $isStamp = $tx->loyaltyProgram?->type->value === 'stamps';
                                $unit    = $isStamp ? 'Stamp' : 'Point';
                                $pts     = abs($tx->points);
                                $sign    = $tx->points >= 0 ? '+' : '-';
                            @endphp
                            <div class="d-flex gap-3 py-3 border-bottom">

                                {{-- Type icon --}}
                                <div class="flex-shrink-0 mt-1">
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $m['bg'] }} bg-opacity-10"
                                          style="width:38px;height:38px;">
                                        <i class="bi {{ $m['icon'] }} {{ $m['text'] }}"></i>
                                    </span>
                                </div>

                                {{-- Details --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                        <span class="fw-semibold small">{{ $m['label'] }}</span>
                                        <span class="text-muted" style="font-size:.75rem;white-space:nowrap;">
                                            {{ $tx->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-2" style="font-size:.8rem;">
                                        {{ $tx->note ?? $m['desc'] }}
                                    </p>
                                    <div class="d-flex flex-wrap gap-3" style="font-size:.8rem;">
                                        @if ($tx->loyaltyProgram)
                                            <div>
                                                <div class="text-muted" style="font-size:.7rem;">Campaign</div>
                                                <div>{{ $tx->loyaltyProgram->name }}</div>
                                            </div>
                                        @endif
                                        @if ($tx->invoice_number)
                                            <div>
                                                <div class="text-muted" style="font-size:.7rem;">Invoice</div>
                                                <div>{{ $tx->invoice_number }}</div>
                                            </div>
                                        @endif
                                        @if ($tx->purchase_amount !== null)
                                            <div>
                                                <div class="text-muted" style="font-size:.7rem;">Purchase</div>
                                                <div>{{ number_format((float) $tx->purchase_amount, 2) }} {{ $member->merchant->currency ?? 'THB' }}</div>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-muted" style="font-size:.7rem;">
                                                {{ $tx->points >= 0 ? 'Earned' : 'Deducted' }}
                                            </div>
                                            <div class="{{ $m['text'] }} fw-medium">
                                                {{ $sign }}{{ number_format($pts) }} {{ $pts === 1 ? $unit : $unit . 's' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-muted" style="font-size:.7rem;">Balance</div>
                                            <div>{{ number_format($tx->balance_before) }} → {{ number_format($tx->balance_after) }}</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    @if ($transactions->hasPages())
                        <div class="px-4 py-3">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @endif

            </div>
            {{-- ── /Activity Tab ──────────────────────────────── --}}

        </div>
    </div>

    {{-- Archive Confirmation Modal --}}
    @unless ($isArchived)
        <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger" id="archiveModalLabel">
                            <i class="bi bi-archive me-2"></i>{{ __('members.archive_member') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">{{ __('members.archive_confirm', ['name' => $member->name]) }}</p>
                        <p class="text-muted small mb-0">
                            {{ __('members.archive_note') }}
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('buttons.cancel') }}</button>
                        <form method="POST" action="{{ route('members.archive', $member) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-archive me-1"></i>{{ __('members.archive_member') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endunless

    {{-- Redeem Reward Modal --}}
    @if (! $isArchived && $member->status === \App\Enums\MemberStatus::Active && $activeCampaign && $eligibleRewards->isNotEmpty())
        <div class="modal fade" id="redeemModal" tabindex="-1" aria-labelledby="redeemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="redeemModalLabel">
                            <i class="bi bi-ticket-perforated me-2 text-primary"></i>{{ __('members.redeem_reward') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="px-3 py-2 bg-light border-bottom small text-muted">
                            {{ $member->name }} &mdash;
                            @if ($activeCampaign->type->value === 'points')
                                {{ number_format($member->total_points) }} points available
                            @else
                                {{ number_format($member->total_points) }} stamps collected
                            @endif
                        </div>
                        @foreach ($eligibleRewards as $reward)
                            <div class="d-flex align-items-start gap-3 p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">{{ $reward->name }}</div>
                                    <div class="badge bg-secondary fw-normal mb-2" style="font-size:.7rem;">
                                        {{ $reward->type->label() }}
                                    </div>
                                    @if ($reward->description)
                                        <p class="text-muted small mb-2">{{ $reward->description }}</p>
                                    @endif
                                    <div class="d-flex flex-wrap gap-3 small">
                                        @if ($activeCampaign->type->value === 'points')
                                            <span>
                                                <span class="text-muted">Points Required:</span>
                                                {{ number_format($reward->points_required) }} pts
                                            </span>
                                        @else
                                            <span>
                                                <span class="text-muted">Stamps Required:</span>
                                                {{ $activeCampaign->settings['stamps_required'] ?? '?' }} stamps
                                            </span>
                                        @endif
                                        @if ($reward->quantity_available !== null)
                                            <span>
                                                <span class="text-muted">Remaining:</span>
                                                {{ max(0, $reward->quantity_available - $reward->quantity_redeemed) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <form method="POST"
                                          action="{{ route('members.redemptions.store', $member) }}">
                                        @csrf
                                        <input type="hidden" name="reward_id" value="{{ $reward->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-check-lg me-1"></i>Redeem
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tab = new URLSearchParams(window.location.search).get('active_tab');
    if (tab) {
        const el = document.getElementById('tab-' + tab);
        if (el) bootstrap.Tab.getOrCreateInstance(el).show();
    }
});
</script>

</x-app-layout>
