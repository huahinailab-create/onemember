@extends('layouts.customer')

@section('title', __('customer.login_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.login_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.login_sub') }}</p>

<form method="POST" action="{{ route('customer.login.password') }}" novalidate>
    @csrf

    <div class="mb-3">
        <label for="identifier" class="form-label fw-semibold">{{ __('customer.field_identifier') }}</label>
        <input type="text" id="identifier" name="identifier"
               class="form-control @error('identifier') is-invalid @enderror"
               value="{{ old('identifier') }}"
               placeholder="{{ __('customer.field_identifier_ph') }}"
               autocomplete="username" autofocus required>
        @error('identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-2">
        <label for="password" class="form-label fw-semibold">{{ __('customer.field_password') }}</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               autocomplete="current-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">{{ __('customer.password_only_hint') }}</div>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
        <label class="form-check-label" for="remember">{{ __('customer.remember_me') }}</label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">{{ __('customer.continue_password') }}</button>
        {{-- Same form, OTP branch — password field is ignored server-side --}}
        <button type="submit" class="btn btn-accent"
                formaction="{{ route('customer.login.otp') }}" formnovalidate>
            {{ __('customer.continue_otp') }}
        </button>
    </div>
</form>

<div class="d-flex justify-content-between mt-4 small">
    <a href="{{ route('customer.password.request') }}">{{ __('customer.forgot_password') }}</a>
    <a href="{{ route('customer.register') }}">{{ __('customer.create_account') }}</a>
</div>
@endsection
