@extends('layouts.wizard')

@section('title', __('onboarding.finish_title') . ' – ' . config('app.name'))

@section('content')
<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">{{ __('onboarding.step_6_of_6') }}</span>
            <span class="text-muted small">100%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-success" style="width:100%;" role="progressbar"></div>
        </div>
    </div>

    <div class="card-body p-4 p-md-5 text-center">

        <div class="d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 mx-auto mb-4"
             style="width:80px;height:80px;">
            <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
        </div>

        <h2 class="fw-bold fs-3 mb-2">{{ __('onboarding.finish_heading') }}</h2>
        <p class="text-muted mb-1">
            <strong>{{ $merchant->name }}</strong> {{ __('onboarding.finish_setup_body') }}
        </p>
        <p class="text-muted mb-4">
            {{ __('onboarding.finish_cta') }}
        </p>

        <div class="row g-3 justify-content-center">
            <div class="col-12 col-sm-6">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-speedometer2 me-2"></i>{{ __('onboarding.go_to_dashboard') }}
                </a>
            </div>
            <div class="col-12 col-sm-6">
                <a href="{{ route('members.create') }}" class="btn btn-outline-primary btn-lg w-100">
                    <i class="bi bi-person-plus me-2"></i>{{ __('onboarding.add_first_member') }}
                </a>
            </div>
        </div>

    </div>
</div>

<p class="text-center text-muted small mt-3">
    {{ __('onboarding.finish_settings_note') }}
    <a href="{{ route('settings') }}">{{ __('navigation.settings') }}</a>.
</p>
@endsection
