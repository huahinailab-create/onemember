<x-app-layout>
    <x-slot name="title">{{ __('subscription.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('subscription.title') }}</x-slot>

    <div class="page-header">
        <h1>{{ __('subscription.title') }}</h1>
        <p>{{ __('subscription.subtitle') }}</p>
    </div>

    {{-- Trial Lifecycle Banner --}}
    <x-trial-banner :merchant="$merchant" />

    @php
        $planKeys   = array_keys($plans);
        $planOrder  = ['free', 'starter', 'professional', 'enterprise'];
        $featureMap = [
            'birthday_rewards' => __('subscription.feature_birthday_rewards'),
            'reports'          => __('subscription.feature_reports'),
            'staff_accounts'   => __('subscription.feature_staff_accounts'),
            'api_access'       => __('subscription.feature_api_access'),
            'priority_support' => __('subscription.feature_priority_support'),
            'custom_branding'  => __('subscription.feature_custom_branding'),
            'multi_location'   => __('subscription.feature_multi_location'),
            'data_export'      => __('subscription.feature_data_export'),
        ];
    @endphp

    {{-- ── Section 1: Current Plan Status ──────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Current Plan card --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-credit-card me-2"></i>{{ __('subscription.current_plan') }}
                </div>
                <div class="card-body">
                    @if ($merchant)
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted fw-normal">{{ __('subscription.plan') }}</dt>
                            <dd class="col-7 fw-semibold">
                                <span class="badge bg-primary fs-6">
                                    {{ $plans[$effectivePlan]['name'] ?? ucfirst($effectivePlan) }}
                                </span>
                                @if ($merchant->isOnTrial())
                                    <span class="badge bg-info ms-1">{{ __('subscription.trial_badge') }}</span>
                                @endif
                            </dd>

                            <dt class="col-5 text-muted fw-normal">{{ __('subscription.status') }}</dt>
                            <dd class="col-7">
                                <span class="badge {{ $merchant->subscriptionStatus()->badgeClass() }}">
                                    {{ $merchant->subscriptionStatus()->label() }}
                                </span>
                            </dd>

                            @if ($merchant->isOnTrial())
                                <dt class="col-5 text-muted fw-normal">{{ __('subscription.trial_ends') }}</dt>
                                <dd class="col-7">
                                    {{ $merchant->trial_ends_at?->format('d M Y') ?? '—' }}
                                </dd>

                                <dt class="col-5 text-muted fw-normal">{{ __('subscription.days_remaining') }}</dt>
                                <dd class="col-7">
                                    @php $days = $merchant->trialDaysRemaining(); @endphp
                                    @if ($days > 7)
                                        <span class="text-info fw-semibold">{{ trans_choice('subscription.days_count', $days, ['count' => $days]) }}</span>
                                    @elseif ($days > 0)
                                        <span class="text-warning fw-semibold">{{ trans_choice('subscription.days_count', $days, ['count' => $days]) }}</span>
                                    @else
                                        <span class="text-danger fw-semibold">{{ __('subscription.expires_today') }}</span>
                                    @endif
                                </dd>
                            @elseif ($merchant->isTrialExpired())
                                <dt class="col-5 text-muted fw-normal">{{ __('subscription.trial_ended') }}</dt>
                                <dd class="col-7">
                                    {{ $merchant->trial_ends_at?->format('d M Y') ?? '—' }}
                                    <span class="badge bg-warning text-dark ms-1">{{ __('subscription.ended_badge') }}</span>
                                </dd>
                            @endif

                            <dt class="col-5 text-muted fw-normal">{{ __('subscription.billing') }}</dt>
                            <dd class="col-7 text-muted">{{ __('subscription.billing_monthly') }}</dd>
                        </dl>

                        @if ($merchant->isOnTrial())
                            <div class="alert alert-info mt-3 mb-0 small">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ __('subscription.trial_active_notice') }}
                            </div>
                        @elseif ($merchant->isTrialExpired())
                            <div class="alert alert-secondary mt-3 mb-0 small">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ __('subscription.trial_expired_notice') }}
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('subscription.no_info') }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-primary btn-sm" disabled>
                        <i class="bi bi-arrow-up-circle me-1"></i>{{ __('buttons.upgrade_plan') }}
                    </button>
                    <span class="text-muted small ms-2">{{ __('buttons.coming_soon') }}</span>
                </div>
            </div>
        </div>

        {{-- Usage Summary card --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-bar-chart me-2"></i>{{ __('subscription.usage_summary') }}
                </div>
                <div class="card-body">
                    @if ($usageSummary && $merchant)
                        @php
                            $limitRows = [
                                'members'   => ['label' => __('subscription.usage_members'),   'icon' => 'bi-people'],
                                'campaigns' => ['label' => __('subscription.usage_campaigns'), 'icon' => 'bi-star'],
                            ];
                        @endphp

                        @foreach ($limitRows as $feature => $meta)
                            @php
                                $u     = $usageSummary[$feature];
                                $pct   = $u['unlimited'] ? 100 : min($u['percentage'] ?? 0, 100);
                                $barCl = $u['unlimited']
                                    ? 'bg-success'
                                    : ($u['level'] === 'limit_reached' ? 'bg-danger'
                                        : ($u['level'] === 'warning' ? 'bg-warning' : 'bg-primary'));
                            @endphp
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="fw-medium">
                                        <i class="bi {{ $meta['icon'] }} me-1 text-muted"></i>{{ $meta['label'] }}
                                    </span>
                                    <span class="small text-muted">
                                        @if ($u['unlimited'])
                                            {{ number_format($u['used']) }} / {{ __('subscription.unlimited') }}
                                        @else
                                            {{ number_format($u['used']) }} / {{ number_format($u['limit']) }}
                                            @if ($u['remaining'] !== null)
                                                &nbsp;·&nbsp; {{ __('subscription.remaining', ['count' => number_format($u['remaining'])]) }}
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar {{ $barCl }}" style="width:{{ $pct }}%;"></div>
                                </div>
                                @if ($u['level'] === 'warning')
                                    <div class="small text-warning mt-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ __('subscription.approaching_limit') }}
                                    </div>
                                @elseif ($u['level'] === 'limit_reached')
                                    <div class="small text-danger mt-1">
                                        <i class="bi bi-x-circle me-1"></i>
                                        {{ __('subscription.limit_reached') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Configured limits table --}}
                        <hr class="my-3">
                        <h6 class="fw-semibold mb-3 small text-uppercase text-muted">{{ __('subscription.configured_limits') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    @php
                                        $limitLabels = [
                                            'members'              => __('subscription.limit_members'),
                                            'campaigns'            => __('subscription.limit_campaigns'),
                                            'rewards_per_campaign' => __('subscription.limit_rewards_per_campaign'),
                                            'staff_users'          => __('subscription.limit_staff_users'),
                                        ];
                                        $planLimits = config("subscriptions.plans.{$effectivePlan}.limits", []);
                                    @endphp
                                    @foreach ($limitLabels as $key => $label)
                                        <tr>
                                            <td class="text-muted ps-0">{{ $label }}</td>
                                            <td class="fw-medium text-end pe-0">
                                                @if (($planLimits[$key] ?? null) === null)
                                                    <span class="badge bg-success-subtle text-success">{{ __('subscription.unlimited') }}</span>
                                                @else
                                                    {{ number_format($planLimits[$key]) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('subscription.no_usage_data') }}</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── Section 2: Plan Comparison ───────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">
            <i class="bi bi-grid me-2"></i>{{ __('subscription.plan_comparison') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:220px;">{{ __('subscription.feature') }}</th>
                            @foreach ($planOrder as $key)
                                @php $plan = $plans[$key] ?? null; if (!$plan) continue; @endphp
                                <th class="text-center {{ $key === $effectivePlan ? 'table-primary' : '' }}">
                                    <div class="fw-semibold">{{ $plan['name'] }}</div>
                                    @if ($key === $effectivePlan)
                                        <span class="badge bg-primary mt-1">{{ __('subscription.current_plan') }}</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Description row --}}
                        <tr>
                            <td class="ps-4 text-muted small">{{ __('subscription.description') }}</td>
                            @foreach ($planOrder as $key)
                                @php $plan = $plans[$key] ?? null; if (!$plan) continue; @endphp
                                <td class="text-center small {{ $key === $effectivePlan ? 'table-primary' : '' }}" style="font-size:.8rem;">
                                    {{ $plan['description'] ?? '' }}
                                </td>
                            @endforeach
                        </tr>

                        {{-- Limits section --}}
                        <tr class="table-secondary">
                            <td colspan="{{ count($planOrder) + 1 }}" class="ps-4 fw-semibold small text-uppercase text-muted py-2">
                                {{ __('subscription.usage_limits') }}
                            </td>
                        </tr>
                        @php
                            $limitLabels2 = [
                                'members'              => __('subscription.limit_members'),
                                'campaigns'            => __('subscription.limit_campaigns'),
                                'rewards_per_campaign' => __('subscription.limit_rewards_per_campaign'),
                                'staff_users'          => __('subscription.limit_staff_users'),
                            ];
                        @endphp
                        @foreach ($limitLabels2 as $key => $label)
                            <tr>
                                <td class="ps-4">{{ $label }}</td>
                                @foreach ($planOrder as $planKey)
                                    @php $plan = $plans[$planKey] ?? null; if (!$plan) continue; $val = $plan['limits'][$key] ?? null; @endphp
                                    <td class="text-center {{ $planKey === $effectivePlan ? 'table-primary' : '' }}">
                                        @if ($val === null)
                                            <i class="bi bi-infinity text-success" title="{{ __('subscription.unlimited') }}"></i>
                                        @else
                                            {{ number_format($val) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- Features section --}}
                        <tr class="table-secondary">
                            <td colspan="{{ count($planOrder) + 1 }}" class="ps-4 fw-semibold small text-uppercase text-muted py-2">
                                {{ __('subscription.features') }}
                            </td>
                        </tr>
                        @foreach ($featureMap as $fKey => $fLabel)
                            <tr>
                                <td class="ps-4">{{ $fLabel }}</td>
                                @foreach ($planOrder as $planKey)
                                    @php $plan = $plans[$planKey] ?? null; if (!$plan) continue; $enabled = $plan['features'][$fKey] ?? false; @endphp
                                    <td class="text-center {{ $planKey === $effectivePlan ? 'table-primary' : '' }}">
                                        @if ($enabled)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-dash text-muted"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- Upgrade row --}}
                        <tr>
                            <td class="ps-4"></td>
                            @foreach ($planOrder as $planKey)
                                @php $plan = $plans[$planKey] ?? null; if (!$plan) continue; @endphp
                                <td class="text-center py-3 {{ $planKey === $effectivePlan ? 'table-primary' : '' }}">
                                    @if ($planKey === $effectivePlan)
                                        <span class="badge bg-primary px-3 py-2">{{ __('subscription.current') }}</span>
                                    @elseif ($planKey === 'enterprise')
                                        <button class="btn btn-outline-dark btn-sm" disabled>
                                            <i class="bi bi-envelope me-1"></i>{{ __('buttons.contact_us') }}
                                        </button>
                                        <div class="text-muted small mt-1">{{ __('buttons.coming_soon') }}</div>
                                    @else
                                        <button class="btn btn-outline-primary btn-sm" disabled>
                                            {{ __('subscription.upgrade_to', ['plan' => $plan['name']]) }}
                                        </button>
                                        <div class="text-muted small mt-1">{{ __('buttons.coming_soon') }}</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Section 3: Help note ─────────────────────────────────── --}}
    <div class="alert alert-light border">
        <i class="bi bi-question-circle me-2 text-muted"></i>
        <span class="text-muted">{{ __('subscription.help_note') }}</span>
    </div>

</x-app-layout>
