@extends('layouts.customer')

@section('title', __('customer.confirm_change_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.confirm_change_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.verify_sub', ['destination' => $destination]) }}</p>

<form method="POST" action="{{ route('customer.change.apply') }}" novalidate>
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

<div class="text-center mt-4 small">
    <a href="{{ route('customer.settings') }}">← {{ __('customer.back_to_settings') }}</a>
</div>
@endsection
