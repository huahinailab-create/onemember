@extends('layouts.customer')

@section('title', __('customer.settings_title'))

@section('content')
<h1 class="customer-h1">{{ __('customer.settings_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer.settings_sub') }}</p>

{{-- Change password --}}
<section class="mb-5" aria-labelledby="settings-password">
    <h2 class="customer-h2" id="settings-password">{{ __('customer.change_password') }}</h2>
    <form method="POST" action="{{ route('customer.password.change') }}" novalidate>
        @csrf
        @method('PUT')

        @if ($customer->hasPassword())
            <div class="mb-3">
                <label for="current_password" class="form-label fw-semibold">{{ __('customer.field_current_password') }}</label>
                <input type="password" id="current_password" name="current_password"
                       class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @else
            <p class="small text-muted">{{ __('customer.no_password_yet') }}</p>
        @endif

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">{{ __('customer.field_new_password') }}</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">{{ __('customer.field_password_confirm') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">{{ __('customer.save_password') }}</button>
    </form>
</section>

{{-- Change email --}}
<section class="mb-5" aria-labelledby="settings-email">
    <h2 class="customer-h2" id="settings-email">{{ __('customer.change_email') }}</h2>
    <p class="small text-muted">{{ __('customer.current_value') }}: {{ $customer->email ?? '—' }}</p>
    <form method="POST" action="{{ route('customer.email.change') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="new_email" class="form-label fw-semibold">{{ __('customer.field_new_email') }}</label>
            <input type="email" id="new_email" name="new_email"
                   class="form-control @error('new_email') is-invalid @enderror" autocomplete="email">
            @error('new_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">{{ __('customer.change_verify_note') }}</div>
        </div>
        <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('customer.send_code') }}</button>
    </form>
</section>

{{-- Change phone --}}
<section aria-labelledby="settings-phone">
    <h2 class="customer-h2" id="settings-phone">{{ __('customer.change_phone') }}</h2>
    <p class="small text-muted">{{ __('customer.current_value') }}: {{ $customer->phone ?? '—' }}</p>
    <form method="POST" action="{{ route('customer.phone.change') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="new_phone" class="form-label fw-semibold">{{ __('customer.field_new_phone') }}</label>
            <input type="tel" id="new_phone" name="new_phone"
                   class="form-control @error('new_phone') is-invalid @enderror"
                   placeholder="081 234 5678" autocomplete="tel">
            @error('new_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">{{ __('customer.change_verify_note') }}</div>
        </div>
        <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('customer.send_code') }}</button>
    </form>
</section>

<div class="mt-4 small">
    <a href="{{ route('customer.profile') }}">← {{ __('customer.back_to_profile') }}</a>
</div>
@endsection
