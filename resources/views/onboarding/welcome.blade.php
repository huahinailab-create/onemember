@extends('layouts.wizard')

@section('title', __('onboarding.welcome_title') . ' – ' . config('app.name'))

@section('content')
<div class="text-center mb-4">
    <div class="mb-3" style="font-family:Arial,sans-serif;font-weight:700;font-size:2.5rem;line-height:1;letter-spacing:-0.02em;">
        <span style="color:#FF1585;">one</span><span style="color:#1A2E5A;">member</span>
    </div>
    <h1 class="fw-bold fs-2 mb-2">{{ __('onboarding.welcome_heading') }}</h1>
    <p class="text-muted fs-5 mb-0">{{ __('onboarding.welcome_subheading') }}</p>
</div>

<div class="card">
    <div class="card-body p-4 p-md-5">

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4 text-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 mx-auto mb-2"
                     style="width:48px;height:48px;background:var(--om-icon-bg);">
                    <i class="bi bi-shop fs-4" style="color:var(--om-navy);"></i>
                </div>
                <div class="fw-medium small">{{ __('onboarding.step_business') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ __('onboarding.step_business_sub') }}</div>
            </div>
            <div class="col-12 col-sm-4 text-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 mx-auto mb-2"
                     style="width:48px;height:48px;background:var(--om-icon-bg);">
                    <i class="bi bi-star fs-4" style="color:var(--om-navy);"></i>
                </div>
                <div class="fw-medium small">{{ __('onboarding.step_campaign') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ __('onboarding.step_campaign_sub') }}</div>
            </div>
            <div class="col-12 col-sm-4 text-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 mx-auto mb-2"
                     style="width:48px;height:48px;background:var(--om-icon-pink-bg);">
                    <i class="bi bi-people fs-4" style="color:var(--om-pink);"></i>
                </div>
                <div class="fw-medium small">{{ __('onboarding.step_members') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ __('onboarding.step_members_sub') }}</div>
            </div>
        </div>

        {{-- Trial confidence badge --}}
        <div class="text-center mb-4 py-2 px-3 rounded-3"
             style="background:#fff0f7;border:1px solid #ffd6eb;">
            <i class="bi bi-shield-check me-1" style="color:#FF1585;"></i>
            <span class="small fw-semibold" style="color:#1A2E5A;">
                {{ __('onboarding.trial_confidence_badge') }}
            </span>
        </div>

        <div class="d-grid">
            <a href="{{ route('onboarding.business-info') }}" class="btn btn-primary btn-lg">
                {{ __('onboarding.get_started') }}
            </a>
        </div>

    </div>
</div>

<p class="text-center text-muted small mt-3">
    {{ __('onboarding.already_setup') }} <a href="{{ route('dashboard') }}">{{ __('onboarding.go_to_dashboard') }}</a>
    &nbsp;·&nbsp;
    <a href="{{ route('onboarding.skip') }}" class="text-muted">{{ __('onboarding.skip_for_now') }}</a>
</p>
@endsection
