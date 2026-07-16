@extends('layouts.customer')

@section('title', __('customer.register_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.register_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.register_sub') }}</p>

<form method="POST" action="{{ route('customer.register.store') }}" novalidate>
    @csrf

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="first_name" class="form-label fw-semibold">{{ __('customer.field_first_name') }} <span class="text-danger">*</span></label>
            <input type="text" id="first_name" name="first_name"
                   class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name') }}" autocomplete="given-name" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="last_name" class="form-label fw-semibold">{{ __('customer.field_last_name') }}</label>
            <input type="text" id="last_name" name="last_name"
                   class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name') }}" autocomplete="family-name">
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="country" class="form-label fw-semibold">{{ __('customer.field_country') }}</label>
        <select id="country" name="country" class="form-select @error('country') is-invalid @enderror">
            @foreach ($countries as $code)
                <option value="{{ $code }}" {{ old('country', 'TH') === $code ? 'selected' : '' }}>
                    {{ __('customer.country_' . strtolower($code)) }}
                </option>
            @endforeach
        </select>
        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <p class="small text-muted mb-2">{{ __('customer.identifier_choice_note') }}</p>

    <div class="mb-3">
        <label for="phone" class="form-label fw-semibold">{{ __('customer.field_phone') }}</label>
        <input type="tel" id="phone" name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone') }}" placeholder="081 234 5678" autocomplete="tel">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">{{ __('customer.field_email') }}</label>
        <input type="email" id="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">{{ __('customer.field_password_optional') }}</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">{{ __('customer.password_optional_hint') }}</div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold">{{ __('customer.field_password_confirm') }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="form-control" autocomplete="new-password">
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-accent">{{ __('customer.register_button') }}</button>
    </div>
</form>

<div class="text-center mt-4 small">
    <a href="{{ route('customer.login') }}">{{ __('customer.already_have_account') }}</a>
</div>
@endsection
