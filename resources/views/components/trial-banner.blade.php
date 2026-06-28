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
        $bannerClass  = 'alert-secondary';
        $iconClass    = 'bi-info-circle text-secondary';
        $dismissible  = false;
        $heading      = __('dashboard.trial_expired_heading');
        $body         = __('dashboard.trial_expired_body');
    } elseif ($days <= 1) {
        $bannerClass  = 'alert-danger';
        $iconClass    = 'bi-exclamation-octagon-fill text-danger';
        $dismissible  = true;
        $heading      = __('dashboard.trial_ends_today');
        $body         = __('dashboard.trial_ends_today_body');
    } elseif ($days <= 3) {
        $bannerClass  = 'alert-warning';
        $iconClass    = 'bi-exclamation-triangle-fill text-warning';
        $dismissible  = true;
        $heading      = __('dashboard.trial_days_left', ['days' => $days]);
        $body         = __('dashboard.trial_expiring_soon_body');
    } elseif ($days <= 7) {
        $bannerClass  = 'alert-warning';
        $iconClass    = 'bi-clock-fill text-warning';
        $dismissible  = true;
        $heading      = __('dashboard.trial_ends_in_days', ['days' => $days]);
        $body         = __('dashboard.trial_7day_body');
    } else {
        $bannerClass  = 'alert-info';
        $iconClass    = 'bi-info-circle-fill text-info';
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
    class="alert {{ $bannerClass }} d-flex align-items-start gap-3 alert-dismissible mb-4"
    role="alert">
    <i class="bi {{ $iconClass }} fs-5 flex-shrink-0 mt-1"></i>
    <div class="flex-grow-1">
        <div class="fw-semibold">{{ $heading }}</div>
        <div class="small mt-1">{{ $body }}</div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" class="btn btn-sm btn-outline-primary" disabled>
            {{ __('buttons.upgrade_plan') }}
        </button>
        <button type="button" class="btn-close" @click="dismiss()" aria-label="{{ __('buttons.dismiss') }}"></button>
    </div>
</div>
@else
{{-- Expired banner — not dismissible --}}
<div class="alert {{ $bannerClass }} d-flex align-items-start gap-3 mb-4" role="alert">
    <i class="bi {{ $iconClass }} fs-5 flex-shrink-0 mt-1"></i>
    <div class="flex-grow-1">
        <div class="fw-semibold">{{ $heading }}</div>
        <div class="small mt-1">{{ $body }}</div>
    </div>
    <div class="flex-shrink-0">
        <button type="button" class="btn btn-sm btn-outline-primary" disabled>
            {{ __('buttons.upgrade_plan') }}
        </button>
    </div>
</div>
@endif
