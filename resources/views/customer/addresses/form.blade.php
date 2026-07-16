@extends('layouts.customer')

@section('title', $address ? __('customer_address.edit_title') : __('customer_address.new_title'))

@section('content')
<h1 class="customer-h1">{{ $address ? __('customer_address.edit_title') : __('customer_address.new_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer_address.form_sub') }}</p>

<form method="POST"
      action="{{ $address ? route('customer.addresses.update', $address) : route('customer.addresses.store') }}"
      novalidate>
    @csrf
    @if ($address) @method('PUT') @endif

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="addr_label" class="form-label fw-semibold">{{ __('customer_address.field_label') }} <span class="text-danger">*</span></label>
            <input type="text" id="addr_label" name="label" maxlength="50" required list="label-suggestions"
                   class="form-control @error('label') is-invalid @enderror"
                   value="{{ old('label', $address?->label) }}" placeholder="{{ __('customer_address.field_label_hint') }}">
            <datalist id="label-suggestions">
                @foreach (config('customer_address.suggested_labels') as $suggestion)
                    <option value="{{ __('customer_address.label_'.$suggestion) }}"></option>
                @endforeach
            </datalist>
            @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="addr_country" class="form-label fw-semibold">{{ __('customer_address.field_country') }} <span class="text-danger">*</span></label>
            {{-- Changing country re-renders the form with that country's fields
                 (GET round-trip — works without JavaScript via the link below) --}}
            <select id="addr_country" name="country" class="form-select"
                    onchange="window.location = '{{ $address ? route('customer.addresses.edit', $address) : route('customer.addresses.create') }}?country=' + this.value">
                @foreach (array_keys(config('customer_address.countries')) as $code)
                    <option value="{{ $code }}" {{ $country === $code ? 'selected' : '' }}>{{ __('customer_address.country_'.$code) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @include('customer.addresses._fields', ['ns' => '', 'address' => $address, 'country' => $country])

    <div class="form-check mb-4">
        <input type="checkbox" class="form-check-input" id="addr_is_default" name="is_default" value="1"
               {{ old('is_default', $address?->is_default) ? 'checked' : '' }}>
        <label class="form-check-label" for="addr_is_default">{{ __('customer_address.set_as_default') }}</label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">{{ __('customer_address.save') }}</button>
        <a href="{{ route('customer.addresses.index') }}" class="btn btn-outline-primary">{{ __('customer_address.cancel') }}</a>
    </div>
</form>
@endsection
