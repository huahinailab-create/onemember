@props(['merchant' => null])

@php
    if (! $merchant) return;

    $isExpired = $merchant->isTrialExpired();
    $isOnTrial = $merchant->isOnTrial();
    $days      = $merchant->trialDaysRemaining();

    // Only show for active trials ≤ 14 days or expired trials
    $showBanner = $isExpired || ($isOnTrial && $days <= 14);
    if (! $showBanner) return;

    if ($isExpired) {
        $bannerStyle  = 'background:#f8f9ff;border:1px solid #cbd5e1;color:#1A1A2E;';
        $iconClass    = 'bi-info-circle';
        $iconStyle    = 'color:#1A2E5A;';
        $dismissible  = false;
        $heading      = __('dashboard.trial_expired_heading');
        $body         = __('dashboard.trial_expired_body');
    } elseif ($days <= 1) {
        $bannerStyle  = 'background:#fff0f7;border:1px solid #ffd6eb;color:#1A1A2E;';
        $iconClass    = 'bi-exclamation-octagon-fill';
        $iconStyle    = 'color:#FF1585;';
        $dismissible  = true;
        $heading      = __('dashboard.trial_ends_today');
        $body         = __('dashboard.trial_ends_today_body');
    } elseif ($days <= 3) {
        $bannerStyle  = 'background:#fff0f7;border:1px solid #ffd6eb;color:#1A1A2E;';
        $iconClass    = 'bi-exclamation-triangle-fill';
        $iconStyle    = 'color:#FF1585;';
        $dismissible  = true;
        $heading      = __('dashboard.trial_days_left', ['days' => $days]);
        $body         = __('dashboard.trial_expiring_soon_body');
    } elseif ($days <= 7) {
        $bannerStyle  = 'background:#fff0f7;border:1px solid #ffd6eb;color:#1A1A2E;';
        $iconClass    = 'bi-clock-fill';
        $iconStyle    = 'color:#FF1585;';
        $dismissible  = true;
        $heading      = __('dashboard.trial_ends_in_days', ['days' => $days]);
        $body         = __('dashboard.trial_7day_body');
    } else {
        $bannerStyle  = 'background:#eef2ff;border:1px solid #c7d2fe;color:#1A1A2E;';
        $iconClass    = 'bi-gift-fill';
        $iconStyle    = 'color:#1A2E5A;';
        $dismissible  = true;
        $heading      = __('dashboard.trial_ends_in_days', ['days' => $days]);
        $body         = __('dashboard.trial_14day_body');
    }
@endphp

@if ($dismissible)
<div
    x-data="{
        dismissed: sessionStorage.getItem('trial_banner_dismissed') === '1',
        dismiss() { sessionStorage.setItem('trial_banner_dismissed', '1'); this.dismissed = true; }
    }"
    x-show="!dismissed"
    x-cloak
    class="rounded-3 d-flex align-items-start gap-3 mb-4 p-3"
    style="{{ $bannerStyle }}"
    role="alert">
    <i class="bi {{ $iconClass }} fs-5 flex-shrink-0 mt-1" style="{{ $iconStyle }}"></i>
    <div class="flex-grow-1">
        <div class="fw-semibold small">{{ $heading }}</div>
        <div class="small mt-1 text-muted">{{ $body }}</div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" class="btn btn-sm btn-primary" disabled>
            {{ __('buttons.upgrade_plan') }}
        </button>
        <button type="button"
                class="btn-close btn-close-sm"
                @click="dismiss()"
                style="font-size:0.75rem;"
                aria-label="{{ __('buttons.dismiss') }}"></button>
    </div>
</div>
@else
{{-- Expired banner — not dismissible --}}
<div class="rounded-3 d-flex align-items-start gap-3 mb-4 p-3"
     style="{{ $bannerStyle }}"
     role="alert">
    <i class="bi {{ $iconClass }} fs-5 flex-shrink-0 mt-1" style="{{ $iconStyle }}"></i>
    <div class="flex-grow-1">
        <div class="fw-semibold small">{{ $heading }}</div>
        <div class="small mt-1 text-muted">{{ $body }}</div>
    </div>
    <div class="flex-shrink-0">
        <button type="button" class="btn btn-sm btn-primary" disabled>
            {{ __('buttons.upgrade_plan') }}
        </button>
    </div>
</div>
@endif
