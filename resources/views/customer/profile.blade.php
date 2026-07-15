@extends('layouts.customer')

@section('title', __('customer.profile_title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-1">
    <h1 class="customer-h1 mb-0">{{ __('customer.profile_hello', ['name' => $customer->displayName()]) }}</h1>
</div>
<p class="text-muted mb-4">{{ __('customer.profile_sub') }}</p>

{{-- Identity summary --}}
<div class="customer-identity-box mb-4">
    <div>
        <div class="small text-muted">{{ __('customer.onemember_id') }}</div>
        <div class="customer-omid">{{ $customer->onemember_id }}</div>
    </div>
    <div class="text-end small">
        @if ($customer->phone)
            <div>
                {{ $customer->maskedPhone() }}
                @if ($customer->hasVerifiedPhone())
                    <span class="badge bg-success">{{ __('customer.verified') }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('customer.unverified') }}</span>
                @endif
            </div>
        @endif
        @if ($customer->email)
            <div class="mt-1">
                {{ $customer->maskedEmail() }}
                @if ($customer->hasVerifiedEmail())
                    <span class="badge bg-success">{{ __('customer.verified') }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('customer.unverified') }}</span>
                @endif
            </div>
        @endif
    </div>
</div>

<form method="POST" action="{{ route('customer.profile.update') }}" novalidate>
    @csrf
    @method('PUT')

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="first_name" class="form-label fw-semibold">{{ __('customer.field_first_name') }} <span class="text-danger">*</span></label>
            <input type="text" id="first_name" name="first_name"
                   class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name', $customer->first_name ?? $customer->name) }}" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="last_name" class="form-label fw-semibold">{{ __('customer.field_last_name') }}</label>
            <input type="text" id="last_name" name="last_name"
                   class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name', $customer->last_name) }}">
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="nickname" class="form-label fw-semibold">{{ __('customer.field_nickname') }}</label>
            <input type="text" id="nickname" name="nickname"
                   class="form-control @error('nickname') is-invalid @enderror"
                   value="{{ old('nickname', $customer->nickname) }}">
            @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="display_name" class="form-label fw-semibold">{{ __('customer.field_display_name') }}</label>
            <input type="text" id="display_name" name="display_name"
                   class="form-control @error('display_name') is-invalid @enderror"
                   value="{{ old('display_name', $customer->display_name) }}">
            @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <label for="birthday" class="form-label fw-semibold">{{ __('customer.field_birthday') }}</label>
            <input type="date" id="birthday" name="birthday"
                   class="form-control @error('birthday') is-invalid @enderror"
                   value="{{ old('birthday', $customer->birthday?->format('Y-m-d')) }}">
            @error('birthday')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="locale" class="form-label fw-semibold">{{ __('customer.field_language') }}</label>
            <select id="locale" name="locale" class="form-select @error('locale') is-invalid @enderror">
                <option value="th" {{ old('locale', $customer->locale) === 'th' ? 'selected' : '' }}>ไทย</option>
                <option value="en" {{ old('locale', $customer->locale) === 'en' ? 'selected' : '' }}>English</option>
            </select>
            @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">{{ __('customer.save_profile') }}</button>
        <a href="{{ route('customer.addresses.index') }}" class="btn btn-outline-primary"><i class="bi bi-geo-alt me-1"></i>{{ __('customer_address.index_title') }}</a>
        <a href="{{ route('customer.settings') }}" class="btn btn-outline-primary">{{ __('customer.account_settings') }}</a>
    </div>
</form>
@endsection
