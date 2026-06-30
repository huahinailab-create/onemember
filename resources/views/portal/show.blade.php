<x-portal-layout :branding="$branding"
    :title="$portal['merchant_name'] . ' — ' . __('portal.page_title')">

    {{-- ── Portal Header ─────────────────────────────────────── --}}
    <header class="portal-header text-white text-center py-4 px-3">
        @if ($branding->logo())
            <img src="{{ $branding->logo() }}"
                 alt="{{ $branding->displayName() }}"
                 class="portal-logo mb-2"
                 style="max-height:64px;max-width:180px;object-fit:contain;border-radius:6px;">
            <br>
        @endif
        <h1 class="h5 fw-bold mb-0 text-white">{{ $branding->displayName() }}</h1>
        @if ($branding->tagline())
            <p class="mb-0 opacity-75 small">{{ $branding->tagline() }}</p>
        @endif
    </header>

    {{-- ── Birthday banner ──────────────────────────────────── --}}
    @if ($portal['birthday'])
        <div class="alert alert-warning border-0 rounded-0 mb-0 text-center py-3" role="alert">
            <span class="fs-4 me-2">🎂</span>
            <strong>
                @if ($portal['birthday']['is_today'])
                    {{ __('portal.birthday_today_title') }}
                @else
                    {{ __('portal.birthday_month_title') }}
                @endif
            </strong>
            <p class="mb-0 small">{{ $portal['birthday']['description'] }}</p>
        </div>
    @endif

    {{-- ── Main content ─────────────────────────────────────── --}}
    <div class="container py-4" style="max-width:480px">

        {{-- Member summary card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 fs-5"
                         style="width:52px;height:52px;background:var(--portal-primary);">
                        {{ strtoupper(substr($portal['member_name'], 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-bold fs-6 text-truncate">{{ $portal['member_name'] }}</div>
                        <div class="text-muted small font-monospace">{{ $portal['member_code'] }}</div>
                    </div>
                    <a href="{{ route('portal.card', $publicUuid) }}"
                       class="btn btn-outline-secondary btn-sm flex-shrink-0"
                       aria-label="{{ __('portal.view_card') }}">
                        <i class="bi bi-credit-card me-1"></i>{{ __('portal.view_card') }}
                    </a>
                </div>

                <hr class="my-3">

                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="text-muted small">{{ __('portal.member_since') }}</div>
                        <div class="fw-semibold small">{{ $portal['member_since']?->format('d M Y') ?? '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">{{ __('portal.last_visit') }}</div>
                        <div class="fw-semibold small">{{ $portal['last_visit']?->diffForHumans() ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Campaigns --}}
        @forelse ($portal['campaigns'] as $campaign)
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi {{ $campaign['type'] === 'stamps' ? 'bi-grid-3x3-gap-fill' : 'bi-star-fill' }} text-warning"></i>
                    <span class="fw-semibold">{{ $campaign['name'] }}</span>
                    <span class="badge bg-secondary ms-auto">{{ $campaign['type_label'] }}</span>
                </div>
                <div class="card-body">

                    {{-- Balance / stamp progress --}}
                    @if ($campaign['type'] === 'stamps' && $campaign['stamps_goal'])
                        @php
                            $stamps     = $campaign['balance'];
                            $goal       = $campaign['stamps_goal'];
                            $pct        = min(100, (int) round($stamps / $goal * 100));
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">{{ __('portal.stamps_collected') }}</span>
                                <span class="fw-bold">{{ $stamps }} / {{ $goal }}</span>
                            </div>
                            <div class="progress" style="height:10px;" role="progressbar"
                                 aria-valuenow="{{ $stamps }}" aria-valuemin="0" aria-valuemax="{{ $goal }}">
                                <div class="progress-bar portal-progress-bar" style="width:{{ $pct }}%"></div>
                            </div>
                            <div class="d-flex gap-1 mt-2 flex-wrap">
                                @for ($s = 1; $s <= $goal; $s++)
                                    <span class="stamp-dot {{ $s <= $stamps ? 'stamp-dot--filled' : '' }}"
                                          aria-label="{{ $s <= $stamps ? __('portal.stamp_collected') : __('portal.stamp_empty') }}"></span>
                                @endfor
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="display-6 fw-bold" style="color:var(--portal-primary);">{{ number_format($campaign['balance']) }}</div>
                            <div class="text-muted small">{{ __('portal.points_balance') }}</div>
                        </div>
                    @endif

                    {{-- Available rewards --}}
                    @if (! empty($campaign['available']))
                        <div class="mb-3">
                            <div class="small fw-semibold text-success mb-2">
                                <i class="bi bi-gift-fill me-1"></i>{{ __('portal.rewards_available') }}
                            </div>
                            @foreach ($campaign['available'] as $reward)
                                <div class="portal-reward-item portal-reward-item--available mb-2 p-3 rounded border border-success-subtle">
                                    <div class="fw-semibold small">{{ $reward['name'] }}</div>
                                    @if ($reward['description'])
                                        <div class="text-muted small">{{ $reward['description'] }}</div>
                                    @endif
                                    <span class="badge bg-success mt-1">{{ __('portal.ready_to_redeem') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Locked rewards --}}
                    @if (! empty($campaign['locked']))
                        <div>
                            <div class="small fw-semibold text-muted mb-2">
                                <i class="bi bi-lock me-1"></i>{{ __('portal.rewards_locked') }}
                            </div>
                            @foreach ($campaign['locked'] as $reward)
                                <div class="portal-reward-item portal-reward-item--locked mb-2 p-3 rounded border bg-light">
                                    <div class="fw-semibold small text-muted">{{ $reward['name'] }}</div>
                                    @if ($reward['description'])
                                        <div class="text-muted small">{{ $reward['description'] }}</div>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <div class="progress flex-grow-1" style="height:6px;" role="progressbar"
                                             aria-valuenow="{{ $reward['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-secondary" style="width:{{ $reward['progress'] }}%"></div>
                                        </div>
                                        <span class="small text-muted text-nowrap">
                                            {{ __('portal.progress_remaining', ['n' => number_format($reward['remaining']), 'unit' => $campaign['type'] === 'stamps' ? __('portal.stamps') : __('portal.points')]) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (empty($campaign['available']) && empty($campaign['locked']))
                        <p class="text-muted small mb-0">{{ __('portal.no_rewards') }}</p>
                    @endif

                </div>
            </div>
        @empty
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center text-muted py-4">
                    <i class="bi bi-star fs-2 d-block mb-2"></i>
                    <p class="mb-0">{{ __('portal.no_campaigns') }}</p>
                </div>
            </div>
        @endforelse

        {{-- Recent redemptions --}}
        @if (! empty($portal['redemptions']))
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <span class="fw-semibold"><i class="bi bi-clock-history me-1"></i>{{ __('portal.recent_redemptions') }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($portal['redemptions'] as $redemption)
                        <li class="list-group-item d-flex justify-content-between align-items-center small">
                            <span>{{ $redemption['reward_name'] }}</span>
                            <span class="text-muted">{{ $redemption['redeemed_at']?->format('d M Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>

</x-portal-layout>
