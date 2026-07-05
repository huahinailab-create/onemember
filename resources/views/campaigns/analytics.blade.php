<x-app-layout>
    <x-slot name="title">{{ __('campaigns.analytics_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('campaigns.analytics_title') }}</x-slot>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1>{{ $campaign->name }}</h1>
            <p>
                <a href="{{ route('campaigns.show', $campaign) }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('campaigns.back_to_campaign') }}
                </a>
            </p>
        </div>
        <span class="badge {{ $campaign->status->value === 'active' ? 'bg-success' : 'bg-secondary' }}">
            {{ $campaign->status->value }}
        </span>
    </div>

    {{-- ── Campaign breakdown ─────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['icon' => 'bi-arrow-up-circle',   'label' => __('campaigns.analytics_points_issued'),   'value' => number_format($pointsIssued)],
            ['icon' => 'bi-gift',              'label' => __('campaigns.analytics_points_redeemed'), 'value' => number_format($pointsRedeemed)],
            ['icon' => 'bi-hourglass-bottom',  'label' => __('campaigns.analytics_points_expired'),  'value' => number_format($pointsExpired)],
            ['icon' => 'bi-receipt',           'label' => __('campaigns.analytics_purchases'),       'value' => number_format($purchaseCount)],
            ['icon' => 'bi-cash-stack',        'label' => __('campaigns.analytics_purchase_total'),  'value' => number_format($purchaseTotal, 2)],
            ['icon' => 'bi-people',            'label' => __('campaigns.analytics_members'),         'value' => number_format($participatingMembers)],
        ] as $stat)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100">
                    <div class="card-body py-3">
                        <div class="text-muted d-flex align-items-center gap-2" style="font-size:0.75rem;">
                            <i class="bi {{ $stat['icon'] }}" style="color:#FF1585;"></i>{{ $stat['label'] }}
                        </div>
                        <div class="fs-4 fw-semibold mt-1">{{ $stat['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- ── 30-day activity trend ──────────────────────────── --}}
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-graph-up me-2" style="color:#FF1585;"></i>{{ __('campaigns.analytics_trend_title') }}
                </div>
                <div class="card-body">
                    @php $maxCount = max(1, $trend->max('count')); @endphp
                    <div class="d-flex align-items-end gap-1" style="height:160px;">
                        @foreach ($trend as $point)
                            <div class="flex-fill d-flex flex-column justify-content-end h-100"
                                 title="{{ $point['day'] }}: {{ $point['count'] }}">
                                <div style="height:{{ max(2, round($point['count'] / $maxCount * 100)) }}%;background:{{ $point['count'] > 0 ? '#FF1585' : '#F0F0F4' }};border-radius:2px 2px 0 0;"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between text-muted mt-2" style="font-size:0.7rem;">
                        <span>{{ now()->subDays(29)->format('d M') }}</span>
                        <span>{{ now()->format('d M') }}</span>
                    </div>
                    <div class="text-muted mt-2" style="font-size:0.8rem;">
                        {{ __('campaigns.analytics_active_30', ['count' => number_format($activeLast30)]) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Member engagement ──────────────────────────────── --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-star me-2" style="color:#FF1585;"></i>{{ __('campaigns.analytics_top_members') }}
                </div>
                <div class="card-body p-0">
                    @if ($topMembers->isEmpty())
                        <div class="text-muted text-center py-4" style="font-size:0.85rem;">
                            {{ __('campaigns.analytics_no_activity') }}
                        </div>
                    @else
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr style="font-size:0.78rem;color:#6B7280;">
                                    <th class="ps-3">{{ __('campaigns.analytics_col_member') }}</th>
                                    <th>{{ __('campaigns.analytics_col_visits') }}</th>
                                    <th class="pe-3 text-end">{{ __('campaigns.analytics_col_points') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topMembers as $row)
                                    <tr style="font-size:0.85rem;">
                                        <td class="ps-3">
                                            @if ($row->member)
                                                <a href="{{ route('members.show', $row->member) }}" class="text-decoration-none">
                                                    {{ $row->member->name }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ number_format($row->visit_count) }}</td>
                                        <td class="pe-3 text-end fw-medium">{{ number_format($row->points_earned) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Reward performance ─────────────────────────────── --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-trophy me-2" style="color:#FF1585;"></i>{{ __('campaigns.analytics_reward_perf') }}
                </div>
                <div class="card-body p-0">
                    @if ($rewardPerformance->isEmpty())
                        <div class="text-muted text-center py-4" style="font-size:0.85rem;">
                            {{ __('campaigns.analytics_no_rewards') }}
                        </div>
                    @else
                        @php $maxRedeem = max(1, $rewardPerformance->max('redemptions_count')); @endphp
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr style="font-size:0.78rem;color:#6B7280;">
                                    <th class="ps-3">{{ __('campaigns.analytics_col_reward') }}</th>
                                    <th>{{ __('campaigns.analytics_col_status') }}</th>
                                    <th>{{ __('campaigns.analytics_col_redemptions') }}</th>
                                    <th class="pe-3" style="width:35%;">{{ __('campaigns.analytics_col_share') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rewardPerformance as $reward)
                                    <tr style="font-size:0.85rem;">
                                        <td class="ps-3 fw-medium">{{ $reward->name }}</td>
                                        <td>
                                            @if ($reward->trashed())
                                                <span class="badge bg-danger">{{ __('campaigns.analytics_status_archived') }}</span>
                                            @else
                                                <span class="badge {{ $reward->status->value === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $reward->status->value }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($reward->redemptions_count) }}</td>
                                        <td class="pe-3">
                                            <div style="height:6px;background:#F0F0F4;border-radius:3px;overflow:hidden;">
                                                <div style="width:{{ round($reward->redemptions_count / $maxRedeem * 100) }}%;height:100%;background:#1A2E5A;border-radius:3px;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
