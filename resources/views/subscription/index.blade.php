<x-app-layout>
    <x-slot name="title">Subscription – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Subscription</x-slot>

    <div class="page-header">
        <h1>Subscription</h1>
        <p>View your current plan, usage, and available upgrades.</p>
    </div>

    {{-- Trial Lifecycle Banner --}}
    <x-trial-banner :merchant="$merchant" />

    @php
        $planKeys   = array_keys($plans);
        $planOrder  = ['free', 'starter', 'professional', 'enterprise'];
        $featureMap = [
            'birthday_rewards' => 'Birthday Rewards',
            'reports'          => 'Reports & Analytics',
            'staff_accounts'   => 'Staff Accounts',
            'api_access'       => 'API Access',
            'priority_support' => 'Priority Support',
            'custom_branding'  => 'Custom Branding',
            'multi_location'   => 'Multi-Location',
            'data_export'      => 'Data Export',
        ];
    @endphp

    {{-- ── Section 1: Current Plan Status ──────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Current Plan card --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-credit-card me-2"></i>Current Plan
                </div>
                <div class="card-body">
                    @if ($merchant)
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted fw-normal">Plan</dt>
                            <dd class="col-7 fw-semibold">
                                <span class="badge bg-primary fs-6">
                                    {{ $plans[$effectivePlan]['name'] ?? ucfirst($effectivePlan) }}
                                </span>
                                @if ($merchant->isOnTrial())
                                    <span class="badge bg-info ms-1">Trial</span>
                                @endif
                            </dd>

                            <dt class="col-5 text-muted fw-normal">Status</dt>
                            <dd class="col-7">
                                <span class="badge {{ $merchant->subscriptionStatus()->badgeClass() }}">
                                    {{ $merchant->subscriptionStatus()->label() }}
                                </span>
                            </dd>

                            @if ($merchant->isOnTrial())
                                <dt class="col-5 text-muted fw-normal">Trial Ends</dt>
                                <dd class="col-7">
                                    {{ $merchant->trial_ends_at?->format('d M Y') ?? '—' }}
                                </dd>

                                <dt class="col-5 text-muted fw-normal">Days Remaining</dt>
                                <dd class="col-7">
                                    @php $days = $merchant->trialDaysRemaining(); @endphp
                                    @if ($days > 7)
                                        <span class="text-info fw-semibold">{{ $days }} days</span>
                                    @elseif ($days > 0)
                                        <span class="text-warning fw-semibold">{{ $days }} {{ Str::plural('day', $days) }}</span>
                                    @else
                                        <span class="text-danger fw-semibold">Expires today</span>
                                    @endif
                                </dd>
                            @elseif ($merchant->isTrialExpired())
                                <dt class="col-5 text-muted fw-normal">Trial Ended</dt>
                                <dd class="col-7">
                                    {{ $merchant->trial_ends_at?->format('d M Y') ?? '—' }}
                                    <span class="badge bg-warning text-dark ms-1">Ended</span>
                                </dd>
                            @endif

                            <dt class="col-5 text-muted fw-normal">Billing</dt>
                            <dd class="col-7 text-muted">Monthly</dd>
                        </dl>

                        @if ($merchant->isOnTrial())
                            <div class="alert alert-info mt-3 mb-0 small">
                                <i class="bi bi-info-circle me-1"></i>
                                You are on a free Professional Trial. You have full access to all Professional features until your trial ends.
                            </div>
                        @elseif ($merchant->isTrialExpired())
                            <div class="alert alert-secondary mt-3 mb-0 small">
                                <i class="bi bi-info-circle me-1"></i>
                                Your trial has ended. Your account is on the Free plan. Your data is safe — upgrade anytime to restore Professional features.
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">No subscription information available.</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-primary btn-sm" disabled>
                        <i class="bi bi-arrow-up-circle me-1"></i>Upgrade Plan
                    </button>
                    <span class="text-muted small ms-2">Coming Soon</span>
                </div>
            </div>
        </div>

        {{-- Usage Summary card --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    <i class="bi bi-bar-chart me-2"></i>Usage Summary
                </div>
                <div class="card-body">
                    @if ($usageSummary && $merchant)
                        @php
                            $limitRows = [
                                'members'   => ['label' => 'Members',   'icon' => 'bi-people'],
                                'campaigns' => ['label' => 'Campaigns', 'icon' => 'bi-star'],
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
                                            {{ number_format($u['used']) }} / Unlimited
                                        @else
                                            {{ number_format($u['used']) }} / {{ number_format($u['limit']) }}
                                            @if ($u['remaining'] !== null)
                                                &nbsp;·&nbsp; {{ number_format($u['remaining']) }} remaining
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
                                        Approaching limit. Consider upgrading.
                                    </div>
                                @elseif ($u['level'] === 'limit_reached')
                                    <div class="small text-danger mt-1">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Limit reached. Upgrade to add more.
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Configured limits table --}}
                        <hr class="my-3">
                        <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Configured Limits</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    @php
                                        $limitLabels = [
                                            'members'              => 'Members',
                                            'campaigns'            => 'Campaigns',
                                            'rewards_per_campaign' => 'Rewards per Campaign',
                                            'staff_users'          => 'Staff Users',
                                        ];
                                        $planLimits = config("subscriptions.plans.{$effectivePlan}.limits", []);
                                    @endphp
                                    @foreach ($limitLabels as $key => $label)
                                        <tr>
                                            <td class="text-muted ps-0">{{ $label }}</td>
                                            <td class="fw-medium text-end pe-0">
                                                @if (($planLimits[$key] ?? null) === null)
                                                    <span class="badge bg-success-subtle text-success">Unlimited</span>
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
                        <p class="text-muted mb-0">No usage data available.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── Section 2: Plan Comparison ───────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">
            <i class="bi bi-grid me-2"></i>Plan Comparison
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:220px;">Feature</th>
                            @foreach ($planOrder as $key)
                                @php $plan = $plans[$key] ?? null; if (!$plan) continue; @endphp
                                <th class="text-center {{ $key === $effectivePlan ? 'table-primary' : '' }}">
                                    <div class="fw-semibold">{{ $plan['name'] }}</div>
                                    @if ($key === $effectivePlan)
                                        <span class="badge bg-primary mt-1">Current Plan</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Description row --}}
                        <tr>
                            <td class="ps-4 text-muted small">Description</td>
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
                                Usage Limits
                            </td>
                        </tr>
                        @php
                            $limitLabels2 = [
                                'members'              => 'Members',
                                'campaigns'            => 'Campaigns',
                                'rewards_per_campaign' => 'Rewards per Campaign',
                                'staff_users'          => 'Staff Users',
                            ];
                        @endphp
                        @foreach ($limitLabels2 as $key => $label)
                            <tr>
                                <td class="ps-4">{{ $label }}</td>
                                @foreach ($planOrder as $planKey)
                                    @php $plan = $plans[$planKey] ?? null; if (!$plan) continue; $val = $plan['limits'][$key] ?? null; @endphp
                                    <td class="text-center {{ $planKey === $effectivePlan ? 'table-primary' : '' }}">
                                        @if ($val === null)
                                            <i class="bi bi-infinity text-success" title="Unlimited"></i>
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
                                Features
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
                                        <span class="badge bg-primary px-3 py-2">Current</span>
                                    @elseif ($planKey === 'enterprise')
                                        <button class="btn btn-outline-dark btn-sm" disabled>
                                            <i class="bi bi-envelope me-1"></i>Contact Us
                                        </button>
                                        <div class="text-muted small mt-1">Coming Soon</div>
                                    @else
                                        <button class="btn btn-outline-primary btn-sm" disabled>
                                            Upgrade to {{ $plan['name'] }}
                                        </button>
                                        <div class="text-muted small mt-1">Coming Soon</div>
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
        <span class="text-muted">Questions about your plan? Contact us at
            <strong>support@onemember.app</strong>
        </span>
    </div>

</x-app-layout>
