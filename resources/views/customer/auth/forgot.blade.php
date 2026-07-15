@extends('layouts.customer')

@section('title', __('customer.forgot_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.forgot_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.forgot_sub') }}</p>

<form method="POST" action="{{ route('customer.password.email') }}" novalidate>
    @csrf

    <div class="mb-4">
        <label for="identifier" class="form-label fw-semibold">{{ __('customer.field_identifier') }}</label>
        <input type="text" id="identifier" name="identifier"
               class="form-control @error('identifier') is-invalid @enderror"
               value="{{ old('identifier') }}"
               placeholder="{{ __('customer.field_identifier_ph') }}" autofocus required>
        @error('identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-accent">{{ __('customer.forgot_button') }}</button>
    </div>
</form>

<div class="text-center mt-4 small">
    <a href="{{ route('customer.login') }}">{{ __('customer.back_to_login') }}</a>
</div>
@endsection
