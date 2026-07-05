<x-app-layout>
    <x-slot name="title">{{ __('dashboard.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('dashboard.title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header">
        <h1>{{ __('dashboard.title') }}</h1>
        <p>{{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}</p>
    </div>

    {{-- ── Trial Lifecycle Banner ───────────────────────────── --}}
    <x-trial-banner :merchant="Auth::user()->merchant" />

    {{-- ── Section 1: Business Overview ────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1" style="color:var(--om-ink);">{{ number_format($totalActiveMembers) }}</div>
                        <div class="text-muted small mt-1">{{ __('dashboard.active_members') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-star"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1" style="color:var(--om-ink);">{{ number_format($activeCampaignCount) }}</div>
                        <div class="text-muted small mt-1">{{ __('dashboard.active_campaigns') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 stat-card stat-card-pink">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon stat-icon-pink">
                        <i class="bi bi-gift"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1" style="color:var(--om-ink);">{{ number_format($redeemedToday) }}</div>
                        <div class="text-muted small mt-1">{{ __('dashboard.rewards_redeemed_today') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 stat-card stat-card-pink">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon stat-icon-pink">
                        <i class="bi bi-lightning"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold lh-1" style="color:var(--om-ink);">{{ number_format($pointsIssuedToday) }}</div>
                        <div class="text-muted small mt-1">{{ __('dashboard.points_issued_today') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Quick Actions ──────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">{{ __('dashboard.quick_actions') }}</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="{{ route('members.create') }}"
                       class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-person-plus fs-3"></i>
                        <span class="fw-medium">{{ __('dashboard.add_member') }}</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('members') }}"
                       class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-bag-check fs-3"></i>
                        <span class="fw-medium">{{ __('dashboard.record_purchase') }}</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('members') }}"
                       class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-gift fs-3"></i>
                        <span class="fw-medium">{{ __('dashboard.redeem_reward') }}</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('campaigns.create') }}"
                       class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="bi bi-star fs-3"></i>
                        <span class="fw-medium">{{ __('dashboard.create_campaign') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 2b: Subscription Usage ────────────────────── --}}
    @if ($subscriptionUsage)
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold">{{ __('dashboard.subscription') }}</span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary">{{ $subscriptionUsage['effective_plan_name'] }}</span>
                @if ($subscriptionUsage['is_on_trial'])
                    <span class="badge bg-info text-dark">{{ __('dashboard.trial') }}</span>
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
                            <span class="fw-medium">{{ trans_choice('dashboard.days_remaining', $subscriptionUsage['trial_days_remaining'], ['days' => $subscriptionUsage['trial_days_remaining']]) }}</span>
                        </div>
                        <p class="text-muted small mb-3">
                            {{ __('dashboard.trial_all_features') }}
                        </p>
                    @else
                        <p class="text-muted small mb-3">
                            {{ __('dashboard.plan_label') }} <strong>{{ $subscriptionUsage['effective_plan_name'] }}</strong><br>
                            {{ __('dashboard.status_label') }} <span class="text-capitalize">{{ $subscriptionUsage['subscription_status'] }}</span>
                        </p>
                    @endif
                    <button class="btn btn-sm btn-outline-primary" disabled>
                        <i class="bi bi-arrow-up-circle me-1"></i>{{ __('dashboard.upgrade_plan') }}
                    </button>
                </div>

                {{-- Members Usage --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="small fw-medium">{{ __('dashboard.members_label') }}</span>
                        <span class="small text-muted">
                            @if ($subscriptionUsage['members']['unlimited'])
                                {{ number_format($subscriptionUsage['members']['used']) }} / {{ __('dashboard.unlimited') }}
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
                            <div class="text-warning small mt-1">{{ __('dashboard.pct_used', ['pct' => $subscriptionUsage['members']['percentage']]) }}</div>
                        @elseif ($mLevel === 'limit_reached')
                            <div class="text-danger small mt-1">{{ __('dashboard.limit_reached') }}</div>
                        @endif
                    @else
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:100%;"></div>
                        </div>
                        <div class="text-muted small mt-1">{{ __('dashboard.unlimited') }}</div>
                    @endif
                </div>

                {{-- Campaigns Usage --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="small fw-medium">{{ __('dashboard.campaigns_label') }}</span>
                        <span class="small text-muted">
                            @if ($subscriptionUsage['campaigns']['unlimited'])
                                {{ number_format($subscriptionUsage['campaigns']['used']) }} / {{ __('dashboard.unlimited') }}
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
                            <div class="text-warning small mt-1">{{ __('dashboard.pct_used', ['pct' => $subscriptionUsage['campaigns']['percentage']]) }}</div>
                        @elseif ($cLevel === 'limit_reached')
                            <div class="text-danger small mt-1">{{ __('dashboard.limit_reached') }}</div>
                        @endif
                    @else
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width:100%;"></div>
                        </div>
                        <div class="text-muted small mt-1">{{ __('dashboard.unlimited') }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ── Section 2c: Business Insights ─────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold">{{ __('intelligence.card_title') }}</span>
            <span class="badge {{ $healthScore['badge_class'] }}">
                {{ $healthScore['label_text'] }} &middot; {{ $healthScore['score'] }}/100
            </span>
        </div>
        @if (count($insights) > 0)
            <div class="list-group list-group-flush">
                @foreach ($insights as $insight)
                    <div class="list-group-item d-flex align-items-start gap-3 py-3">
                        <i class="bi {{ $insight['icon'] }} text-primary mt-1 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <span class="small">{{ $insight['text'] }}</span>
                            @if ($insight['action_url'])
                                <a href="{{ $insight['action_url'] }}"
                                   class="small ms-2 fw-medium text-decoration-none">
                                    {{ $insight['action_label'] }} &rarr;
                                </a>
                            @endif
                        </div>
                        @if ($insight['priority'] === 'high')
                            <span class="badge text-bg-danger flex-shrink-0">{{ __('intelligence.priority_high') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="card-body">
                <p class="text-muted small mb-0">{{ __('intelligence.no_insights') }}</p>
            </div>
        @endif
        @if (count($opportunities) > 0)
            <div class="card-footer bg-transparent border-top">
                <div class="row g-3">
                    @foreach ($opportunities as $opp)
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi {{ $opp['icon'] }} text-muted mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="small fw-medium">{{ $opp['title'] }}</div>
                                    <div class="text-muted small">{{ $opp['description'] }}</div>
                                    <a href="{{ $opp['action_url'] }}"
                                       class="small text-primary fw-medium text-decoration-none">
                                        {{ $opp['action_label'] }} &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ── Sections 3 & 4: Recent Activity + Top Members ─────── --}}
    <div class="row g-4 mb-4">

        {{-- Recent Activity --}}
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header fw-semibold">{{ __('dashboard.recent_activity') }}</div>
                @if ($recentActivity->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clock-history text-muted fs-1 d-block mb-3"></i>
                        <p class="text-muted mb-0">{{ __('dashboard.no_activity') }}</p>
                    </div>
                @else
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">{{ __('dashboard.member_col') }}</th>
                                        <th>{{ __('dashboard.type_col') }}</th>
                                        <th class="d-none d-sm-table-cell">{{ __('dashboard.campaign_col') }}</th>
                                        <th class="text-end">{{ __('dashboard.points_col') }}</th>
                                        <th class="text-end pe-3 d-none d-sm-table-cell">{{ __('dashboard.date_time_col') }}</th>
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
                                            <td class="text-muted small d-none d-sm-table-cell">
                                                {{ $activity->loyaltyProgram?->name ?? '—' }}
                                            </td>
                                            <td class="text-end fw-medium {{ $activity->points >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $activity->points >= 0 ? '+' : '' }}{{ number_format($activity->points) }}
                                            </td>
                                            <td class="text-end pe-3 text-muted small d-none d-sm-table-cell">
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
                <div class="card-header fw-semibold">{{ __('dashboard.top_members') }}</div>
                @if ($topMembers->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people text-muted fs-1 d-block mb-3"></i>
                        @if (! $hasAnyMembers)
                            <p class="text-muted mb-3">{{ __('dashboard.no_members_yet') }}</p>
                            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-plus me-1"></i> {{ __('dashboard.add_first_member') }}
                            </a>
                        @else
                            <p class="text-muted mb-0">{{ __('dashboard.no_member_data') }}</p>
                        @endif
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($topMembers as $i => $member)
                            <a href="{{ route('members.show', $member) }}"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 px-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold flex-shrink-0"
                                     style="width:32px;height:32px;font-size:0.8rem;background:var(--om-icon-bg);color:var(--om-navy);">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-medium text-truncate">{{ $member->name }}</div>
                                    <div class="text-muted small">
                                        {{ __('dashboard.lifetime_pts', ['pts' => number_format($member->lifetime_points)]) }}
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold text-primary">{{ number_format($member->total_points) }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ __('members.pts') }}</div>
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
        <div class="card-header fw-semibold">{{ __('dashboard.active_campaigns_card') }}</div>
        @if ($activeCampaigns->isEmpty())
            <div class="card-body text-center py-5">
                <i class="bi bi-star text-muted fs-1 d-block mb-3"></i>
                @if (! $hasAnyCampaigns)
                    <p class="text-muted mb-3">{{ __('dashboard.no_campaigns_yet') }}</p>
                    <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('dashboard.create_first_campaign') }}
                    </a>
                @else
                    <p class="text-muted mb-3">{{ __('dashboard.no_active_campaigns') }}</p>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
                        {{ __('dashboard.view_all_campaigns') }}
                    </a>
                @endif
            </div>
        @else
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">{{ __('dashboard.campaign_name_col') }}</th>
                                <th class="d-none d-sm-table-cell">{{ __('dashboard.type_col2') }}</th>
                                <th>{{ __('dashboard.status_col') }}</th>
                                <th class="d-none d-sm-table-cell">{{ __('dashboard.rewards_col') }}</th>
                                <th class="pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeCampaigns as $campaign)
                                <tr>
                                    <td class="ps-3 fw-medium">{{ $campaign->name }}</td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge bg-secondary">
                                            {{ $campaign->type->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $campaign->status->badgeClass() }}">
                                            {{ $campaign->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-muted d-none d-sm-table-cell">{{ $campaign->rewards_count }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            {{ __('buttons.view') }}
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
                            <span class="text-muted small">{{ __('dashboard.no_rewards_yet') }}</span>
                            @if ($firstCampaignId)
                                <a href="{{ route('campaigns.rewards.create', $firstCampaignId) }}"
                                   class="btn btn-primary btn-sm ms-auto">
                                    <i class="bi bi-plus-lg me-1"></i> {{ __('dashboard.add_first_reward') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

</x-app-layout>
