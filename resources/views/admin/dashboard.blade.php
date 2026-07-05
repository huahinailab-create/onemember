<x-admin-layout title="Dashboard">

    {{-- ── Platform Overview ─────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Merchants --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Total Merchants</span>
                    <div class="stat-icon" style="background:#EEF2FF;color:#4F46E5;">
                        <i class="bi bi-shop"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($totalMerchants) }}</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Active</span>
                    <div class="stat-icon" style="background:#ECFDF5;color:#059669;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value text-success">{{ number_format($activeMerchants) }}</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">On Trial</span>
                    <div class="stat-icon" style="background:#EFF6FF;color:#3B82F6;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <div class="stat-value" style="color:#3B82F6;">{{ number_format($trialMerchants) }}</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Paid</span>
                    <div class="stat-icon" style="background:#FFF7ED;color:#EA580C;">
                        <i class="bi bi-credit-card"></i>
                    </div>
                </div>
                <div class="stat-value" style="color:#EA580C;">{{ number_format($paidMerchants) }}</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Free Plan</span>
                    <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED;">
                        <i class="bi bi-gift"></i>
                    </div>
                </div>
                <div class="stat-value" style="color:#7C3AED;">{{ number_format($freeMerchants) }}</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Inactive</span>
                    <div class="stat-icon" style="background:#FEF2F2;color:#DC2626;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <div class="stat-value text-danger">{{ number_format($inactiveMerchants) }}</div>
            </div>
        </div>

    </div>

    {{-- ── Members & Transactions ─────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Total Members</span>
                    <div class="stat-icon" style="background:#FDF2F8;color:#FF1585;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($totalMembers) }}</div>
                <div class="text-muted mt-1" style="font-size:0.75rem;">
                    +{{ number_format($membersToday) }} today
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Transactions</span>
                    <div class="stat-icon" style="background:#ECFDF5;color:#059669;">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($totalTransactions) }}</div>
                <div class="text-muted mt-1" style="font-size:0.75rem;">
                    +{{ number_format($transactionsToday) }} today
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Rewards Redeemed</span>
                    <div class="stat-icon" style="background:#FFF7ED;color:#EA580C;">
                        <i class="bi bi-star"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($rewardsRedeemed) }}</div>
                <div class="text-muted mt-1" style="font-size:0.75rem;">
                    +{{ number_format($redemptionsToday) }} today
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">New Members</span>
                    <div class="stat-icon" style="background:#EFF6FF;color:#3B82F6;">
                        <i class="bi bi-person-plus"></i>
                    </div>
                </div>
                <div class="stat-value" style="color:#3B82F6;">{{ number_format($newMembersToday) }}</div>
                <div class="text-muted mt-1" style="font-size:0.75rem;">
                    {{ number_format($newMembersThisWeek) }} this week ·
                    {{ number_format($newMembersThisMonth) }} this month
                </div>
            </div>
        </div>

    </div>

    {{-- ── Analytics + Attention ──────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- New Merchants Trend --}}
        <div class="col-md-6 col-xl-4">
            <div class="stat-card card p-3 h-100">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-graph-up me-2" style="color:#FF1585;"></i>New Merchants
                </h6>
                <div class="d-flex gap-3">
                    <div class="text-center flex-fill">
                        <div style="font-size:1.6rem;font-weight:700;color:#1A2E5A;">{{ $newMerchantsToday }}</div>
                        <div style="font-size:0.7rem;color:#6c757d;font-weight:500;">TODAY</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div style="font-size:1.6rem;font-weight:700;color:#1A2E5A;">{{ $newMerchantsThisWeek }}</div>
                        <div style="font-size:0.7rem;color:#6c757d;font-weight:500;">THIS WEEK</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div style="font-size:1.6rem;font-weight:700;color:#1A2E5A;">{{ $newMerchantsThisMonth }}</div>
                        <div style="font-size:0.7rem;color:#6c757d;font-weight:500;">THIS MONTH</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- New Members Trend --}}
        <div class="col-md-6 col-xl-4">
            <div class="stat-card card p-3 h-100">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-people me-2" style="color:#FF1585;"></i>New Members
                </h6>
                <div class="d-flex gap-3">
                    <div class="text-center flex-fill">
                        <div style="font-size:1.6rem;font-weight:700;color:#1A2E5A;">{{ $newMembersToday }}</div>
                        <div style="font-size:0.7rem;color:#6c757d;font-weight:500;">TODAY</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div style="font-size:1.6rem;font-weight:700;color:#1A2E5A;">{{ $newMembersThisWeek }}</div>
                        <div style="font-size:0.7rem;color:#6c757d;font-weight:500;">THIS WEEK</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div style="font-size:1.6rem;font-weight:700;color:#1A2E5A;">{{ $newMembersThisMonth }}</div>
                        <div style="font-size:0.7rem;color:#6c757d;font-weight:500;">THIS MONTH</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Health + Attention --}}
        <div class="col-md-12 col-xl-4">
            <div class="stat-card card p-3 h-100">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-activity me-2" style="color:#FF1585;"></i>Attention Needed
                </h6>
                <div class="d-flex flex-column gap-2" style="font-size:0.83rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Zero members</span>
                        <span class="fw-600" style="color:{{ $zeroMembers > 0 ? '#EA580C' : '#059669' }};">
                            {{ $zeroMembers }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Not onboarded</span>
                        <span class="fw-600" style="color:{{ $notOnboarded > 0 ? '#EA580C' : '#059669' }};">
                            {{ $notOnboarded }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Trial ending &lt;7 days</span>
                        <span class="fw-600" style="color:{{ $trialEndingSoon > 0 ? '#DC2626' : '#059669' }};">
                            {{ $trialEndingSoon }}
                        </span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Database</span>
                        <span class="badge"
                              style="background:{{ ['ok'=>'#059669','warn'=>'#D97706','error'=>'#DC2626'][$health['database']['status']] ?? '#9CA3AF' }};color:#fff;font-size:0.7rem;">
                            {{ $health['database']['label'] }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Email</span>
                        <span class="badge"
                              style="background:{{ ['ok'=>'#059669','warn'=>'#D97706','error'=>'#DC2626'][$health['email']['status']] ?? '#9CA3AF' }};color:#fff;font-size:0.7rem;">
                            {{ $health['email']['label'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Top Performers ──────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card card p-3">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-trophy me-2" style="color:#FF1585;"></i>Top by Members
                </h6>
                <table class="table table-sm mb-0" style="font-size:0.83rem;">
                    <tbody>
                        @forelse($topByMembers as $m)
                        <tr>
                            <td>
                                <a href="{{ route('admin.merchants.show', $m) }}"
                                   style="color:#1A2E5A;text-decoration:none;font-weight:500;">
                                    {{ $m->name }}
                                </a>
                            </td>
                            <td class="text-end fw-600">{{ number_format($m->members_count) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted">No merchants yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card card p-3">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-arrow-left-right me-2" style="color:#FF1585;"></i>Top by Transactions
                </h6>
                <table class="table table-sm mb-0" style="font-size:0.83rem;">
                    <tbody>
                        @forelse($topByTransactions as $m)
                        <tr>
                            <td>
                                <a href="{{ route('admin.merchants.show', $m) }}"
                                   style="color:#1A2E5A;text-decoration:none;font-weight:500;">
                                    {{ $m->name }}
                                </a>
                            </td>
                            <td class="text-end fw-600">{{ number_format($m->transactions_count) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted">No data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Activation Funnel + Platform Health ───────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Activation Funnel --}}
        <div class="col-md-7">
            <div class="stat-card card p-3 h-100">
                <h6 class="fw-600 mb-1" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-funnel me-2" style="color:#FF1585;"></i>Merchant Activation Funnel
                </h6>
                <p class="text-muted mb-3" style="font-size:0.75rem;">
                    Tracks how merchants progress from registration to first paid conversion.
                </p>
                @php
                    $maxCount = $funnel[0]['count'] ?: 1;
                @endphp
                <div class="d-flex flex-column gap-2">
                    @foreach($funnel as $i => $stage)
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                      style="width:20px;height:20px;background:#EEF2FF;color:#4F46E5;font-size:0.65rem;font-weight:700;">
                                    {{ $i + 1 }}
                                </span>
                                <span style="font-size:0.82rem;font-weight:500;color:#1A2E5A;">
                                    {{ $stage['label'] }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                @if($i > 0 && $stage['pct_of_prev'] !== null)
                                <span class="text-muted" style="font-size:0.72rem;">
                                    {{ $stage['pct_of_prev'] }}% from prev
                                </span>
                                @endif
                                <span style="font-size:0.85rem;font-weight:700;color:#1A2E5A;min-width:3rem;text-align:right;">
                                    {{ number_format($stage['count']) }}
                                </span>
                            </div>
                        </div>
                        <div class="progress" style="height:6px;border-radius:3px;background:#F0F0F4;">
                            <div class="progress-bar"
                                 style="width:{{ $stage['pct_of_total'] }}%;background:{{ $i === 5 ? '#FF1585' : '#1A2E5A' }};border-radius:3px;"
                                 role="progressbar"
                                 aria-valuenow="{{ $stage['pct_of_total'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($funnel[0]['count'] > 0)
                <div class="mt-3 pt-2 border-top d-flex gap-3" style="font-size:0.75rem;color:#6c757d;">
                    <span>
                        Overall conversion:
                        <strong style="color:#FF1585;">{{ $funnel[5]['pct_of_total'] }}%</strong>
                        registered → paid
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Platform Health --}}
        <div class="col-md-5">
            <div class="stat-card card p-3 h-100">
                <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
                    <i class="bi bi-heart-pulse me-2" style="color:#FF1585;"></i>Platform Health
                </h6>
                @php
                    $statusColor = ['ok' => '#059669', 'warn' => '#D97706', 'error' => '#DC2626', 'unknown' => '#9CA3AF'];
                    $statusIcon  = ['ok' => 'bi-check-circle-fill', 'warn' => 'bi-exclamation-triangle-fill', 'error' => 'bi-x-circle-fill', 'unknown' => 'bi-dash-circle'];
                    $rows = [
                        ['key' => 'database',  'name' => 'Database'],
                        ['key' => 'email',     'name' => 'Email Service'],
                        ['key' => 'queue',     'name' => 'Queue'],
                        ['key' => 'storage',   'name' => 'Storage'],
                        ['key' => 'scheduler', 'name' => 'Scheduler'],
                        ['key' => 'backup',    'name' => 'Last Backup'],
                    ];
                @endphp
                <div class="d-flex flex-column gap-2">
                    @foreach($rows as $row)
                    @php $h = $health[$row['key']]; $s = $h['status']; @endphp
                    <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $statusIcon[$s] ?? 'bi-dash-circle' }}"
                               style="color:{{ $statusColor[$s] ?? '#9CA3AF' }};font-size:0.85rem;"></i>
                            <span style="font-size:0.82rem;font-weight:500;color:#1A2E5A;">{{ $row['name'] }}</span>
                            @if(! empty($h['note']))
                            <i class="bi bi-info-circle text-muted" style="font-size:0.7rem;"
                               title="{{ $h['detail'] ?? '' }}"></i>
                            @endif
                        </div>
                        <div class="text-end">
                            <span style="font-size:0.8rem;font-weight:600;color:{{ $statusColor[$s] ?? '#9CA3AF' }};">
                                {{ $h['label'] }}
                            </span>
                            @if(! empty($h['detail']))
                            <div class="text-muted" style="font-size:0.7rem;">{{ $h['detail'] }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- App version --}}
                    <div class="d-flex align-items-center justify-content-between py-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-code-square" style="color:#9CA3AF;font-size:0.85rem;"></i>
                            <span style="font-size:0.82rem;font-weight:500;color:#1A2E5A;">App Version</span>
                        </div>
                        <div class="text-end">
                            <span style="font-size:0.8rem;font-weight:600;color:#1A2E5A;">
                                v{{ $health['version']['label'] }}
                            </span>
                            <div class="text-muted" style="font-size:0.7rem;">{{ $health['version']['detail'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Merchant Registrations ──────────────────────────────── --}}
    <div class="stat-card card p-3">
        <h6 class="fw-600 mb-3" style="color:#1A2E5A;font-size:0.85rem;font-weight:600;">
            <i class="bi bi-clock-history me-2" style="color:#FF1585;"></i>Recent Merchant Registrations
        </h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0" style="font-size:0.83rem;">
                <thead>
                    <tr class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">
                        <th>Business</th>
                        <th>Owner</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMerchants as $m)
                    <tr>
                        <td>
                            <a href="{{ route('admin.merchants.show', $m) }}"
                               style="color:#1A2E5A;text-decoration:none;font-weight:500;">
                                {{ $m->name }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $m->owner?->name ?? '—' }}</td>
                        <td>
                            <span class="badge"
                                  style="background:#EEF2FF;color:#4F46E5;font-weight:500;font-size:0.7rem;">
                                {{ $m->subscription_plan?->label() ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $m->status?->badgeClass() ?? 'badge bg-secondary' }}"
                                  style="font-size:0.7rem;">
                                {{ $m->status?->label() ?? '—' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $m->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center py-3">No merchants registered yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2 text-end">
            <a href="{{ route('admin.merchants.index') }}" style="font-size:0.8rem;color:#FF1585;">
                View all merchants →
            </a>
        </div>
    </div>

    {{-- Geographic Analytics: Top Postal Codes --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt" style="color:#FF1585;"></i> Top Postal Codes by Member Count
        </div>
        <div class="card-body p-0">
            @if($topPostalCodes->isEmpty())
                <div class="text-muted text-center py-4" style="font-size:0.85rem;">
                    No postal code data yet. Members will appear here once they provide their postal code.
                </div>
            @else
                @php $maxPostal = $topPostalCodes->first()->member_count ?: 1; @endphp
                <table class="table table-sm mb-0">
                    <thead>
                        <tr style="font-size:0.78rem;color:#6B7280;">
                            <th class="ps-3">#</th>
                            <th>Postal Code</th>
                            <th>Members</th>
                            <th class="pe-3" style="width:40%;">Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPostalCodes as $i => $row)
                        <tr style="font-size:0.85rem;">
                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                            <td class="fw-medium font-monospace">{{ $row->postal_code }}</td>
                            <td>{{ number_format($row->member_count) }}</td>
                            <td class="pe-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex:1;height:6px;background:#F0F0F4;border-radius:3px;overflow:hidden;">
                                        <div style="width:{{ round($row->member_count / $maxPostal * 100) }}%;height:100%;background:#FF1585;border-radius:3px;"></div>
                                    </div>
                                    <span class="text-muted" style="font-size:0.75rem;min-width:2rem;">{{ round($row->member_count / $maxPostal * 100) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</x-admin-layout>
