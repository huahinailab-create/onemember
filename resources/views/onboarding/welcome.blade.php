@extends('layouts.wizard')

@section('title', __('onboarding.welcome_title') . ' – ' . config('app.name'))

@section('content')
<div class="text-center mb-4">
    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 mx-auto mb-3"
         style="width:72px;height:72px;">
        <i class="bi bi-hexagon-fill text-primary" style="font-size:2rem;"></i>
    </div>
    <h1 class="fw-bold fs-2 mb-2">{{ __('onboarding.welcome_heading') }}</h1>
    <p class="text-muted fs-5 mb-0">{{ __('onboarding.welcome_subheading') }}</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4 p-md-5">

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4 text-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 mx-auto mb-2"
                     style="width:48px;height:48px;">
                    <i class="bi bi-shop text-primary fs-4"></i>
                </div>
                <div class="fw-medium small">{{ __('onboarding.step_business') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ __('onboarding.step_business_sub') }}</div>
            </div>
            <div class="col-12 col-sm-4 text-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 mx-auto mb-2"
                     style="width:48px;height:48px;">
                    <i class="bi bi-star text-success fs-4"></i>
                </div>
                <div class="fw-medium small">{{ __('onboarding.step_campaign') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ __('onboarding.step_campaign_sub') }}</div>
            </div>
            <div class="col-12 col-sm-4 text-center">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning bg-opacity-10 mx-auto mb-2"
                     style="width:48px;height:48px;">
                    <i class="bi bi-people text-warning fs-4"></i>
                </div>
                <div class="fw-medium small">{{ __('onboarding.step_members') }}</div>
                <div class="text-muted" style="font-size:0.8rem;">{{ __('onboarding.step_members_sub') }}</div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('onboarding.business-info') }}" class="btn btn-primary btn-lg">
                {{ __('onboarding.get_started') }}
            </a>
            <form method="GET" action="{{ route('onboarding.skip') }}">
                <button type="submit" class="btn btn-link text-muted w-100">
                    {{ __('onboarding.skip_for_now') }}
                </button>
            </form>
        </div>

    </div>
</div>

<p class="text-center text-muted small mt-3">
    {{ __('onboarding.already_setup') }} <a href="{{ route('dashboard') }}">{{ __('onboarding.go_to_dashboard') }}</a>
</p>
@endsection
