@extends('layouts.wizard')

@section('title', __('onboarding.business_settings_title') . ' – ' . config('app.name'))

@section('header-action')
    <a href="{{ route('onboarding.skip') }}" class="btn btn-sm btn-outline-secondary">
        {{ __('onboarding.skip_for_now') }}
    </a>
@endsection

@section('content')
@php
    $timezones = [
        'Asia/Bangkok'     => 'Bangkok (UTC+7)',
        'Asia/Singapore'   => 'Singapore (UTC+8)',
        'Asia/Kuala_Lumpur'=> 'Kuala Lumpur (UTC+8)',
        'Asia/Jakarta'     => 'Jakarta (UTC+7)',
        'Asia/Manila'      => 'Manila (UTC+8)',
        'Asia/Ho_Chi_Minh' => 'Ho Chi Minh City (UTC+7)',
        'Asia/Tokyo'       => 'Tokyo (UTC+9)',
        'Asia/Seoul'       => 'Seoul (UTC+9)',
        'Asia/Kolkata'     => 'Kolkata (UTC+5:30)',
        'Asia/Dubai'       => 'Dubai (UTC+4)',
        'Europe/London'    => 'London (UTC+0/+1)',
        'Europe/Paris'     => 'Paris (UTC+1/+2)',
        'America/New_York' => 'New York (UTC-5/-4)',
        'America/Los_Angeles' => 'Los Angeles (UTC-8/-7)',
        'Australia/Sydney' => 'Sydney (UTC+10/+11)',
        'UTC'              => 'UTC',
    ];
    $currencies = [
        'THB' => 'THB – Thai Baht',
        'USD' => 'USD – US Dollar',
        'EUR' => 'EUR – Euro',
        'GBP' => 'GBP – British Pound',
        'JPY' => 'JPY – Japanese Yen',
        'SGD' => 'SGD – Singapore Dollar',
        'MYR' => 'MYR – Malaysian Ringgit',
        'IDR' => 'IDR – Indonesian Rupiah',
        'PHP' => 'PHP – Philippine Peso',
        'VND' => 'VND – Vietnamese Dong',
        'AUD' => 'AUD – Australian Dollar',
        'CAD' => 'CAD – Canadian Dollar',
    ];
@endphp

<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">{{ __('onboarding.step_3_of_6') }}</span>
            <span class="text-muted small">50%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-primary" style="width:50%;" role="progressbar"></div>
        </div>
    </div>

    <div class="card-body p-4">
        <h2 class="fw-bold fs-4 mb-1">{{ __('onboarding.business_settings_heading') }}</h2>
        <p class="text-muted mb-4">{{ __('onboarding.business_settings_sub') }}</p>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.business-settings.store') }}">
            @csrf

            {{-- Currency --}}
            <div class="mb-3">
                <label for="currency" class="form-label fw-medium">
                    {{ __('settings.currency') }} <span class="text-danger">*</span>
                </label>
                <select id="currency"
                        name="currency"
                        class="form-select @error('currency') is-invalid @enderror"
                        required>
                    @foreach ($currencies as $code => $label)
                        <option value="{{ $code }}"
                            {{ old('currency', $merchant?->currency ?? 'THB') === $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Timezone --}}
            <div class="mb-3">
                <label for="timezone" class="form-label fw-medium">
                    {{ __('settings.timezone') }} <span class="text-danger">*</span>
                </label>
                <select id="timezone"
                        name="timezone"
                        class="form-select @error('timezone') is-invalid @enderror"
                        required>
                    @foreach ($timezones as $tz => $label)
                        <option value="{{ $tz }}"
                            {{ old('timezone', $merchant?->timezone ?? 'Asia/Bangkok') === $tz ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('timezone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Date Format --}}
            <div class="mb-4">
                <label for="date_format" class="form-label fw-medium">
                    {{ __('settings.date_format') }} <span class="text-danger">*</span>
                </label>
                @php
                    $currentFormat = old('date_format', $merchant?->settings['date_format'] ?? 'DD/MM/YYYY');
                @endphp
                <select id="date_format"
                        name="date_format"
                        class="form-select @error('date_format') is-invalid @enderror"
                        required>
                    @foreach (['DD/MM/YYYY' => 'DD/MM/YYYY (e.g. 28/06/2026)', 'MM/DD/YYYY' => 'MM/DD/YYYY (e.g. 06/28/2026)', 'YYYY-MM-DD' => 'YYYY-MM-DD (e.g. 2026-06-28)'] as $value => $label)
                        <option value="{{ $value }}" {{ $currentFormat === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('date_format')
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
