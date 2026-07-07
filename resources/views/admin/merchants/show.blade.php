<x-admin-layout :title="$merchant->name">

    {{-- Back --}}
    <div class="mb-3">
        <a href="{{ route('admin.merchants.index') }}"
           style="font-size:0.83rem;color:#6c757d;text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i>All Merchants
        </a>
    </div>

    {{-- Header --}}
    <div class="stat-card card p-4 mb-4 d-flex flex-row align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:56px;height:56px;background:#EEF2FF;color:#4F46E5;font-size:1.4rem;font-weight:700;">
            {{ mb_substr($merchant->name, 0, 1) }}
        </div>
        <div class="flex-fill">
            <h5 class="mb-0 fw-700" style="color:#1A2E5A;font-weight:700;">
                {{ $merchant->name }}
                @if($merchant->deleted_at)
                    <span class="badge bg-danger ms-2">Deleted</span>
                @endif
            </h5>
            <div class="text-muted" style="font-size:0.83rem;">
                {{ $merchant->email ?? $merchant->owner?->email ?? '—' }}
                @if($merchant->phone) · {{ $merchant->phone }} @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            @if($merchant->status)
            <span class="badge {{ $merchant->status->badgeClass() }}">
                {{ $merchant->status->label() }}
            </span>
            @endif
            @if($merchant->subscription_status)
            <span class="badge {{ $merchant->subscription_status->badgeClass() }}">
                {{ $merchant->subscription_status->label() }}
            </span>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">

        {{-- Merchant Profile --}}
        <div class="col-md-6">
            <div class="stat-card card p-3 h-100">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-shop me-2" style="color:#FF1585;"></i>Merchant Profile
                </h6>
                <table class="table table-sm mb-0" style="font-size:0.82rem;">
                    <tbody>
                        <tr><td class="text-muted border-0 py-1">Business name</td><td class="border-0 py-1 fw-500">{{ $merchant->name }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Contact person</td><td class="border-0 py-1">{{ $merchant->contact_person ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Email</td><td class="border-0 py-1">{{ $merchant->email ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Phone</td><td class="border-0 py-1">{{ $merchant->phone ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">City</td><td class="border-0 py-1">{{ $merchant->city ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Country</td><td class="border-0 py-1">{{ $merchant->country ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Website</td><td class="border-0 py-1">{{ $merchant->website ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Registered</td><td class="border-0 py-1">{{ $merchant->created_at->format('d M Y H:i') }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Onboarded</td><td class="border-0 py-1">
                            @if($merchant->onboarding_completed_at)
                                <span class="text-success">{{ $merchant->onboarding_completed_at->format('d M Y') }}</span>
                            @else
                                <span class="text-warning">Not completed</span>
                            @endif
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Owner + Subscription --}}
        <div class="col-md-6 d-flex flex-column gap-3">

            <div class="stat-card card p-3">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-person me-2" style="color:#FF1585;"></i>Owner
                </h6>
                <table class="table table-sm mb-0" style="font-size:0.82rem;">
                    <tbody>
                        <tr><td class="text-muted border-0 py-1">Name</td><td class="border-0 py-1 fw-500">{{ $merchant->owner?->name ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Email</td><td class="border-0 py-1">{{ $merchant->owner?->email ?? '—' }}</td></tr>
                        <tr><td class="text-muted border-0 py-1">Email verified</td><td class="border-0 py-1">
                            @if($merchant->owner?->email_verified_at)
                                <span class="text-success">{{ $merchant->owner->email_verified_at->format('d M Y') }}</span>
                            @else
                                <span class="text-danger">Not verified</span>
                            @endif
                        </td></tr>
                    </tbody>
                </table>
            </div>

            <div class="stat-card card p-3">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-credit-card me-2" style="color:#FF1585;"></i>Plan / Subscription
                </h6>
                <table class="table table-sm mb-0" style="font-size:0.82rem;">
                    <tbody>
                        <tr><td class="text-muted border-0 py-1">Plan</td><td class="border-0 py-1">
                            <span class="badge" style="background:#EEF2FF;color:#4F46E5;font-weight:500;">
                                {{ $merchant->subscription_plan?->label() ?? '—' }}
                            </span>
                        </td></tr>
                        <tr><td class="text-muted border-0 py-1">Status</td><td class="border-0 py-1">
                            @if($merchant->subscription_status)
                            <span class="badge {{ $merchant->subscription_status->badgeClass() }}">
                                {{ $merchant->subscription_status->label() }}
                            </span>
                            @endif
                        </td></tr>
                        <tr><td class="text-muted border-0 py-1">Trial ends</td><td class="border-0 py-1">
                            {{ $merchant->trial_ends_at?->format('d M Y') ?? '—' }}
                            @if($merchant->isOnTrial())
                                <span class="text-info ms-1">({{ $merchant->trialDaysRemaining() }}d left)</span>
                            @endif
                        </td></tr>
                        <tr><td class="text-muted border-0 py-1">Stripe ID</td><td class="border-0 py-1" style="font-family:monospace;font-size:0.78rem;">
                            {{ $merchant->stripe_customer_id ?? '—' }}
                        </td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- TRIAL-001: Trial extension (admin) --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-hourglass-split me-2"></i>Trial Extension</div>
        <div class="card-body">
            {{-- Session success is rendered once by the admin layout; keep form errors local --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.merchants.extend-trial', $merchant) }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Extend by</label>
                    <select name="preset" class="form-select form-select-sm" id="trial-preset" style="width:auto;">
                        <option value="30">+30 days</option>
                        <option value="60">+60 days</option>
                        <option value="custom">Custom…</option>
                    </select>
                </div>
                <div class="col-auto" id="trial-custom-wrap" style="display:none;">
                    <label class="form-label form-label-sm mb-1">Days</label>
                    <input type="number" name="custom_days" min="1" max="365" class="form-control form-control-sm" style="width:100px;">
                </div>
                <div class="col">
                    <label class="form-label form-label-sm mb-1">Reason <span class="text-danger">*</span></label>
                    <input type="text" name="reason" maxlength="255" required class="form-control form-control-sm"
                           placeholder="e.g. pilot merchant, onboarding delay">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Extend trial</button>
                </div>
            </form>

            @if ($merchant->trialExtensions->isNotEmpty())
                <table class="table table-sm mt-3 mb-0">
                    <thead><tr style="font-size:0.75rem;color:#6B7280;">
                        <th>When</th><th>Days</th><th>New end</th><th>Reason</th><th>By</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($merchant->trialExtensions as $ext)
                            <tr style="font-size:0.82rem;">
                                <td>{{ $ext->created_at->format('d M Y H:i') }}</td>
                                <td>+{{ $ext->days }}</td>
                                <td>{{ $ext->new_trial_ends_at->format('d M Y') }}</td>
                                <td>{{ $ext->reason }}</td>
                                <td>{{ $ext->admin?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('trial-preset')?.addEventListener('change', function () {
            document.getElementById('trial-custom-wrap').style.display = this.value === 'custom' ? '' : 'none';
        });
    </script>

    {{-- Counts row --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Members', $merchant->members_count, 'bi-people', '#FDF2F8', '#FF1585'],
            ['Active Members', $activeMembers, 'bi-person-check', '#ECFDF5', '#059669'],
            ['Transactions', $merchant->transactions_count, 'bi-arrow-left-right', '#ECFDF5', '#059669'],
            ['Redemptions', $merchant->redemptions_count, 'bi-star', '#FFF7ED', '#EA580C'],
            ['Campaigns', $campaignCount, 'bi-megaphone', '#EFF6FF', '#3B82F6'],
            ['Rewards', $rewardCount, 'bi-gift', '#F5F3FF', '#7C3AED'],
        ] as [$label, $value, $icon, $bg, $color])
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3 text-center">
                <div class="mx-auto mb-2 stat-icon" style="background:{{ $bg }};color:{{ $color }};">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div class="stat-value" style="font-size:1.5rem;">{{ number_format($value) }}</div>
                <div class="stat-label mt-1">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Recent Members + Transactions --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="stat-card card p-3">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-people me-2" style="color:#FF1585;"></i>Recent Members
                </h6>
                @if($recentMembers->isEmpty())
                    <p class="text-muted mb-0" style="font-size:0.83rem;">No members yet.</p>
                @else
                <table class="table table-sm mb-0" style="font-size:0.82rem;">
                    <thead>
                        <tr class="text-muted" style="font-size:0.72rem;">
                            <th>Name</th><th>Phone</th><th>Joined</th><th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMembers as $mbr)
                        <tr>
                            <td class="fw-500">{{ $mbr->name }}</td>
                            <td class="text-muted">{{ $mbr->phone ?? '—' }}</td>
                            <td class="text-muted">{{ $mbr->joined_at?->format('d M Y') ?? '—' }}</td>
                            <td class="fw-600">{{ number_format($mbr->total_points) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card card p-3">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-arrow-left-right me-2" style="color:#FF1585;"></i>Recent Transactions
                </h6>
                @if($recentTransactions->isEmpty())
                    <p class="text-muted mb-0" style="font-size:0.83rem;">No transactions yet.</p>
                @else
                <table class="table table-sm mb-0" style="font-size:0.82rem;">
                    <thead>
                        <tr class="text-muted" style="font-size:0.72rem;">
                            <th>Member</th><th>Type</th><th>Points</th><th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $tx)
                        <tr>
                            <td class="fw-500">{{ $tx->member?->name ?? '—' }}</td>
                            <td>
                                <span class="badge"
                                      style="background:#F0F0F4;color:#1A2E5A;font-size:0.7rem;">
                                    {{ $tx->type?->value ?? $tx->type ?? '—' }}
                                </span>
                            </td>
                            <td class="fw-600" style="color:{{ ($tx->points ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                                {{ ($tx->points ?? 0) >= 0 ? '+' : '' }}{{ number_format($tx->points ?? 0) }}
                            </td>
                            <td class="text-muted">{{ $tx->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

</x-admin-layout>
