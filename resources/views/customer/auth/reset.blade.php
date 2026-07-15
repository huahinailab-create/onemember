@extends('layouts.customer')

@section('title', __('customer.reset_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.reset_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.reset_sub') }}</p>

<form method="POST" action="{{ route('customer.password.update') }}" novalidate>
    @csrf

    <div class="mb-3">
        <label for="code" class="form-label fw-semibold">{{ __('customer.field_code') }}</label>
        <input type="text" id="code" name="code" inputmode="numeric" pattern="[0-9]*"
               maxlength="{{ config('customer_identity.otp.length') }}"
               class="form-control form-control-lg text-center customer-otp-input @error('code') is-invalid @enderror"
               autocomplete="one-time-code" autofocus required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">{{ __('customer.field_new_password') }}</label>
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold">{{ __('customer.field_password_confirm') }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="form-control" autocomplete="new-password" required>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-accent">{{ __('customer.reset_button') }}</button>
    </div>
</form>

<div class="text-center mt-4 small">
    <a href="{{ route('customer.login') }}">{{ __('customer.back_to_login') }}</a>
</div>
@endsection
