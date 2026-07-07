@extends('layouts.wizard')

@section('title', __('onboarding.business_info_title') . ' – ' . config('app.name'))

@section('header-action')
    <a href="{{ route('onboarding.skip') }}" class="btn btn-sm btn-outline-secondary">
        {{ __('onboarding.skip_for_now') }}
    </a>
@endsection

@section('content')
<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">{{ __('onboarding.step_2_of_6') }}</span>
            <span class="text-muted small">33%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-primary" style="width:33%;" role="progressbar"
                 aria-label="{{ __('onboarding.step_2_of_6') }}" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <div class="card-body p-4">
        <h2 class="fw-bold fs-4 mb-1">{{ __('onboarding.business_info_heading') }}</h2>
        <p class="text-muted mb-4">{{ __('onboarding.business_info_sub') }}</p>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.business-info.store') }}">
            @csrf

            {{-- Business Name --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-medium">
                    {{ __('onboarding.business_name') }} <span class="text-danger">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                       placeholder="e.g. Happy Coffee Shop"
                       value="{{ old('name', $merchant?->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Business Type --}}
            <div class="mb-3">
                <label for="business_type" class="form-label fw-medium">
                    {{ __('onboarding.business_type') }} <span class="text-danger">*</span>
                </label>
                <select id="business_type"
                        name="business_type"
                        class="form-select form-select-lg @error('business_type') is-invalid @enderror"
                        required>
                    <option value="" disabled {{ old('business_type', $merchant?->business_type) ? '' : 'selected' }}>
                        {{ __('onboarding.business_type_ph') }}
                    </option>
                    @foreach ([
                        'Hair Salon', 'Nail Salon', 'Massage & Spa', 'Restaurant & Café',
                        'Hotel', 'Fashion Retail', 'Beauty & Cosmetics', 'Grocery Store',
                        'Pet Shop', 'Wholesale', 'Other',
                    ] as $type)
                        <option value="{{ $type }}"
                            {{ old('business_type', $merchant?->business_type) === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                @error('business_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Business Phone --}}
            <div class="mb-3">
                <label for="phone" class="form-label fw-medium">{{ __('onboarding.business_phone') }}</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       placeholder="e.g. +66 81 234 5678"
                       value="{{ old('phone', $merchant?->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Website --}}
            <div class="mb-4">
                <label for="website" class="form-label fw-medium">{{ __('onboarding.website') }}</label>
                <input type="url"
                       id="website"
                       name="website"
                       class="form-control @error('website') is-invalid @enderror"
                       placeholder="e.g. https://www.yourbusiness.com"
                       value="{{ old('website', $merchant?->website) }}">
                @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ __('onboarding.save_and_continue') }} <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
