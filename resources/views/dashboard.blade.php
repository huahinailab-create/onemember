<x-app-layout>
    <x-slot name="title">Dashboard – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>

    {{-- Page Header --}}
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ Auth::user()->name }}. Here's what's happening today.</p>
    </div>

    {{-- ── Section 1: Business Overview ────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-people text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1">{{ number_format($totalActiveMembers) }}</div>
                        <div class="text-muted small mt-1">Active Members</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-star text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1">{{ number_format($activeCampaignCount) }}</div>
                        <div class="text-muted small mt-1">Active Campaigns</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning bg-opacity-10 flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-gift text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1">{{ number_format($redeemedToday) }}</div>
                        <div class="text-muted small mt-1">Rewards Redeemed Today</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info bg-opacity-10 flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-lightning text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1">{{ number_format($pointsIssuedToday) }}</div>
                        <div class="text-muted small mt-1">Points Issued Today</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Quick Actions ──────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">Quick Actions</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="{{ route('members.create') }}"
                       class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-person-plus fs-3"></i>
                        <span class="fw-medium">Add Member</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('members') }}"
                       class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-bag-check fs-3"></i>
                        <span class="fw-medium">Record Purchase</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('members') }}"
                       class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-gift fs-3"></i>
                        <span class="fw-medium">Redeem Reward</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('campaigns.create') }}"
                       class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-star fs-3"></i>
                        <span class="fw-medium">Create Campaign</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 2b: Subscription Usage ────────────────────── --}}
    @if ($subscriptionUsage)
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold">Subscription</span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary">{{ $subscriptionUsage['effective_plan_name'] }}</span>
                @if ($subscriptionUsage['is_on_trial'])
                    <span class="badge bg-info text-dark">Trial</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4 align-items-start">

                {{-- Plan & Trial Info --}}
                <div class="col-12 col-md-4">
                    @if ($subscriptionUsage['is_on_trial'])
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-clock text-info"></i>
                            <span class="fw-medium">{{ $subscriptionUsage['trial_days_remaining'] }} {{ Str::plural('day', $subscriptionUsage['trial_days_remaining']) }} remaining</span>
                        </div>
                        <p class="text-muted small mb-3">
                            You're on the Professional trial. All features are unlocked.
                        </p>
                    @else
                        <p class="text-muted small mb-3">
                            Plan: <strong>{{ $subscriptionUsage['effective_plan_name'] }}</strong><br>
                            Status: <span class="text-capitalize">{{ $subscriptionUsage['subscription_status'] }}</span>
                        </p>
                    @endif
                    <button class="btn btn-sm btn-outline-primary" disabled>
                        <i class="bi bi-arrow-up-circle me-1"></i>Upgrade Plan
                    </button>
                </div>

                {{-- Members Usage --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="small fw-medium">Members</span>
                        <span class="small text-muted">
                            @if ($subscriptionUsage['members']['unlimited'])
                                {{ number_format($subscriptionUsage['members']['used']) }} / Unlimited
                            @else
                                {{ number_format($subscriptionUsage['members']['used']) }} / {{ number_format($subscriptionUsage['members']['limit']) }}
                            @endif
                        </span>
                    </div>
                    @if (! $subscriptionUsage['members']['unlimited'])
                        @php
                            $mPct   = min($subscriptionUsage['members']['percentage'], 100);
                            $mLevel = $subscriptionUsage['members']['level'];
                            $mBar   = $mLevel === 'limit_reached' ? 'bg-danger' : ($mLevel === 'warning' ? 'bg-warning' : 'bg-primary');
                        @endphp
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar {{ $mBar }}" role="progressbar"
                                 style="width:{{ $mPct }}%;" aria-valuenow="{{ $mPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @if ($mLevel === 'warning')
                            <div class="text-warning small mt-1">{{ $subscriptionUsage['members']['percentage'] }}% used</div>
                        @elseif ($mLevel === 'limit_reached')
                            <div class="text-danger small mt-1">Limit reached</div>
                        @endif
                    @else
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:100%;"></div>
                        </div>
                        <div class="text-muted small mt-1">Unlimited</div>
                    @endif
                </div>

                {{-- Campaigns Usage --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="small fw-medium">Campaigns</span>
                        <span class="small text-muted">
                            @if ($subscriptionUsage['campaigns']['unlimited'])
                                {{ number_format($subscriptionUsage['campaigns']['used']) }} / Unlimited
                            @else
                                {{ number_format($subscriptionUsage['campaigns']['used']) }} / {{ number_format($subscriptionUsage['campaigns']['limit']) }}
                            @endif
                        </span>
                    </div>
                    @if (! $subscriptionUsage['campaigns']['unlimited'])
                        @php
                            $cPct   = min($subscriptionUsage['campaigns']['percentage'], 100);
                            $cLevel = $subscriptionUsage['campaigns']['level'];
                            $cBar   = $cLevel === 'limit_reached' ? 'bg-danger' : ($cLevel === 'warning' ? 'bg-warning' : 'bg-primary');
                        @endphp
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar {{ $cBar }}" role="progressbar"
                                 style="width:{{ $cPct }}%;" aria-valuenow="{{ $cPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @if ($cLevel === 'warning')
                            <div class="text-warning small mt-1">{{ $subscriptionUsage['campaigns']['percentage'] }}% used</div>
                        @elseif ($cLevel === 'limit_reached')
                            <div class="text-danger small mt-1">Limit reached</div>
                        @endif
                    @else
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:100%;"></div>
                        </div>
                        <div class="text-muted small mt-1">Unlimited</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ── Sections 3 & 4: Recent Activity + Top Members ─────── --}}
    <div class="row g-4 mb-4">

        {{-- Recent Activity --}}
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header fw-semibold">Recent Activity</div>
                @if ($recentActivity->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clock-history text-muted fs-1 d-block mb-3"></i>
                        <p class="text-muted mb-0">No activity recorded yet.</p>
                    </div>
                @else
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Member</th>
                                        <th>Type</th>
                                        <th>Campaign</th>
                                        <th class="text-end">Points</th>
                                        <th class="text-end pe-3">Date &amp; Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentActivity as $activity)
                                        <tr>
                                            <td class="ps-3">
                                                @if ($activity->member)
                                                    <a href="{{ route('members.show', $activity->member) }}"
                                                       class="text-decoration-none fw-medium">
                                                        {{ $activity->member->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $activity->type->badgeClass() }}">
                                                    {{ $activity->type->label() }}
                                                </span>
                                            </td>
                                            <td class="text-muted small">
                                                {{ $activity->loyaltyProgram?->name ?? '—' }}
                                            </td>
                                            <td class="text-end fw-medium {{ $activity->points >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $activity->points >= 0 ? '+' : '' }}{{ number_format($activity->points) }}
                                            </td>
                                            <td class="text-end pe-3 text-muted small">
                                                {{ $activity->created_at->format('d M Y, H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Top Members --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold">Top Members</div>
                @if ($topMembers->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people text-muted fs-1 d-block mb-3"></i>
                        @if (! $hasAnyMembers)
                            <p class="text-muted mb-3">You haven't added any members yet.</p>
                            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-plus me-1"></i> Add Your First Member
                            </a>
                        @else
                            <p class="text-muted mb-0">No member data available yet.</p>
                        @endif
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($topMembers as $i => $member)
                            <a href="{{ route('members.show', $member) }}"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 px-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold flex-shrink-0"
                                     style="width:32px;height:32px;font-size:0.8rem;">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-medium text-truncate">{{ $member->name }}</div>
                                    <div class="text-muted small">
                                        Lifetime: {{ number_format($member->lifetime_points) }} pts
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold text-primary">{{ number_format($member->total_points) }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">pts</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Section 5: Active Campaigns ─────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">Active Campaigns</div>
        @if ($activeCampaigns->isEmpty())
            <div class="card-body text-center py-5">
                <i class="bi bi-star text-muted fs-1 d-block mb-3"></i>
                @if (! $hasAnyCampaigns)
                    <p class="text-muted mb-3">You haven't created a campaign yet.</p>
                    <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create Your First Campaign
                    </a>
                @else
                    <p class="text-muted mb-3">No active campaigns at the moment.</p>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
                        View All Campaigns
                    </a>
                @endif
            </div>
        @else
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Campaign Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Rewards</th>
                                <th class="pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeCampaigns as $campaign)
                                <tr>
                                    <td class="ps-3 fw-medium">{{ $campaign->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $campaign->type->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $campaign->status->badgeClass() }}">
                                            {{ $campaign->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $campaign->rewards_count }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if (! $hasAnyRewards)
                    <div class="px-3 py-3 border-top">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-gift text-muted fs-5"></i>
                            <span class="text-muted small">Your campaigns don't have rewards yet.</span>
                            @if ($firstCampaignId)
                                <a href="{{ route('campaigns.rewards.create', $firstCampaignId) }}"
                                   class="btn btn-primary btn-sm ms-auto">
                                    <i class="bi bi-plus-lg me-1"></i> Add Your First Reward
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

</x-app-layout>
