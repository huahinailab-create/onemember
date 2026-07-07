@extends('layouts.wizard')

@section('title', __('onboarding.business_settings_title') . ' – ' . config('app.name'))

@section('header-action')
    <a href="{{ route('onboarding.skip') }}" class="btn btn-sm btn-outline-secondary">
        {{ __('onboarding.skip_for_now') }}
    </a>
@endsection

@section('content')
@php
    // BETA-008B — single documented source for allowed values
    $timezones  = config('localization.timezones');
    $currencies = config('localization.currencies');
@endphp

<div class="card shadow-sm">

    {{-- Progress --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-medium">{{ __('onboarding.step_3_of_6') }}</span>
            <span class="text-muted small">50%</span>
        </div>
        <div class="progress mb-1" style="height:6px;">
            <div class="progress-bar bg-primary" style="width:50%;" role="progressbar"
                 aria-label="{{ __('onboarding.step_3_of_6') }}" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
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
            {{-- Country (CORE-001 — drives extensions + defaults) --}}
            <div class="mb-3">
                <label for="country" class="form-label fw-medium">
                    {{ __('onboarding.country') }} <span class="text-danger">*</span>
                </label>
                <select id="country"
                        name="country"
                        class="form-select @error('country') is-invalid @enderror"
                        required>
                    @foreach (config('countries.list') as $code => $label)
                        <option value="{{ $code }}"
                            {{ old('country', $merchant?->country ?? config('countries.default')) === $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('onboarding.country_hint') }}</div>
            </div>

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
                            {{ old('currency', $merchant?->currency ?? config('app.default_currency')) === $code ? 'selected' : '' }}>
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
            <div class="mb-3">
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

            {{-- Interface Language --}}
            <div class="mb-4">
                <label for="locale" class="form-label fw-medium">
                    {{ __('settings.language') }} <span class="text-danger">*</span>
                </label>
                @php $currentLocale = old('locale', $merchant?->settings['locale'] ?? app()->getLocale() ?? 'th'); @endphp
                <select id="locale"
                        name="locale"
                        class="form-select @error('locale') is-invalid @enderror"
                        required>
                    <option value="th" {{ $currentLocale === 'th' ? 'selected' : '' }}>ภาษาไทย</option>
                    <option value="en" {{ $currentLocale === 'en' ? 'selected' : '' }}>English</option>
                </select>
                @error('locale')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                {{-- Merchant Agreement summary (BILLING-001) --}}
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-3">
                        @include('legal.merchant-agreement')
                    </div>
                </div>

                {{-- Terms acceptance (CORE-001 — wording draft pending legal review, DR-33) --}}
                <div class="form-check mb-3 text-start">
                    <input class="form-check-input @error('terms') is-invalid @enderror"
                           type="checkbox" id="terms" name="terms" value="1" required
                           {{ old('terms') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="terms">
                        {{ __('onboarding.terms_label') }}
                        <a href="{{ 'https://' . config('domains.corporate') . '/terms' }}" target="_blank" rel="noopener">{{ __('onboarding.terms_link') }}</a>
                        ·
                        <a href="{{ 'https://' . config('domains.corporate') . '/privacy' }}" target="_blank" rel="noopener">{{ __('onboarding.privacy_link') }}</a>
                    </label>
                    <div class="form-text">{{ __('onboarding.terms_scope') }} <em>({{ __('onboarding.terms_draft_note') }})</em></div>
                    @error('terms')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    {{ __('onboarding.save_and_continue') }} <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
