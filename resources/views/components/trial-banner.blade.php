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
        $heading      = 'Your Professional Trial has ended.';
        $body         = 'Your account has been moved to the Free plan. Your existing data is safe. Upgrade anytime to unlock Professional features again.';
    } elseif ($days <= 1) {
        $bannerClass  = 'alert-danger';
        $iconClass    = 'bi-exclamation-octagon-fill text-danger';
        $dismissible  = true;
        $heading      = 'Your trial ends today!';
        $body         = 'You have less than 1 day left on your Professional Trial. Upgrade now to keep full access.';
    } elseif ($days <= 3) {
        $bannerClass  = 'alert-warning';
        $iconClass    = 'bi-exclamation-triangle-fill text-warning';
        $dismissible  = true;
        $heading      = "Only {$days} days left on your trial.";
        $body         = 'After your trial ends, your account will move to the Free plan. Upgrade to keep Professional features.';
    } elseif ($days <= 7) {
        $bannerClass  = 'alert-warning';
        $iconClass    = 'bi-clock-fill text-warning';
        $dismissible  = true;
        $heading      = "Your trial ends in {$days} days.";
        $body         = 'Upgrade before your trial ends to avoid any interruption to your loyalty programme.';
    } else {
        $bannerClass  = 'alert-info';
        $iconClass    = 'bi-info-circle-fill text-info';
        $dismissible  = true;
        $heading      = "Your Professional Trial ends in {$days} days.";
        $body         = 'Enjoying OneMember? Upgrade to keep access to all Professional features.';
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
            Upgrade Plan
        </button>
        <button type="button" class="btn-close" @click="dismiss()" aria-label="Dismiss"></button>
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
            Upgrade Plan
        </button>
    </div>
</div>
@endif
