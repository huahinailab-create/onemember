@extends('layouts.customer')

@section('title', __('customer.verify_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.verify_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.verify_sub', ['destination' => $destination]) }}</p>

<form method="POST" action="{{ route('customer.otp.verify') }}" novalidate>
    @csrf

    <div class="mb-4">
        <label for="code" class="form-label fw-semibold">{{ __('customer.field_code') }}</label>
        <input type="text" id="code" name="code" inputmode="numeric" pattern="[0-9]*"
               maxlength="{{ config('customer_identity.otp.length') }}"
               class="form-control form-control-lg text-center customer-otp-input @error('code') is-invalid @enderror"
               autocomplete="one-time-code" autofocus required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-accent">{{ __('customer.verify_button') }}</button>
    </div>
</form>

<form method="POST" action="{{ route('customer.otp.resend') }}" class="text-center mt-4">
    @csrf
    <button type="submit" class="btn btn-link btn-sm" @disabled($resendIn > 0)>
        {{ $resendIn > 0 ? __('customer.resend_in', ['seconds' => $resendIn]) : __('customer.resend_code') }}
    </button>
</form>

<div class="text-center mt-2 small">
    <a href="{{ route('customer.login') }}">{{ __('customer.back_to_login') }}</a>
</div>
@endsection
