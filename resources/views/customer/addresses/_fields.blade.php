{{-- CUSTOMER-001B — country-aware address fields, shared by the address book
     form and the checkout "add new address" form. $ns prefixes field names
     (e.g. 'new_address') so checkout can nest them; '' for the plain form.
     $country selects the schema from config/customer_address.php. --}}
@php
    $ns      = $ns ?? '';
    $name    = fn (string $f) => $ns === '' ? $f : "{$ns}[{$f}]";
    $err     = fn (string $f) => $ns === '' ? $f : "{$ns}.{$f}";
    $old     = fn (string $f, $default = null) => old($err($f), $address?->{$f} ?? $default);
    $schema  = config("customer_address.countries.{$country}");
    $req     = fn (string $f) => in_array($f, $schema['required'], true);
    $star    = fn (string $f) => $req($f) ? ' <span class="text-danger">*</span>' : '';
    $idp     = $ns === '' ? 'addr' : $ns;
@endphp

<div class="row g-3 mb-3">
    <div class="col-12">
        <label for="{{ $idp }}_recipient_name" class="form-label fw-semibold">{{ __('customer_address.field_recipient') }} <span class="text-danger">*</span></label>
        <input type="text" id="{{ $idp }}_recipient_name" name="{{ $name('recipient_name') }}" maxlength="150" required
               class="form-control @error($err('recipient_name')) is-invalid @enderror" value="{{ $old('recipient_name') }}">
        @error($err('recipient_name'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="{{ $idp }}_phone" class="form-label fw-semibold">{{ __('customer_address.field_phone') }}</label>
        <input type="tel" id="{{ $idp }}_phone" name="{{ $name('phone') }}" maxlength="30" inputmode="tel"
               class="form-control @error($err('phone')) is-invalid @enderror" value="{{ $old('phone') }}">
        @error($err('phone'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="{{ $idp }}_line1" class="form-label fw-semibold">{{ __('customer_address.field_line1') }}{!! $star('line1') !!}</label>
        <input type="text" id="{{ $idp }}_line1" name="{{ $name('line1') }}" maxlength="255" @if($req('line1')) required @endif
               class="form-control @error($err('line1')) is-invalid @enderror" value="{{ $old('line1') }}"
               placeholder="{{ __('customer_address.field_line1_hint') }}">
        @error($err('line1'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="{{ $idp }}_line2" class="form-label fw-semibold">{{ __('customer_address.field_line2') }}</label>
        <input type="text" id="{{ $idp }}_line2" name="{{ $name('line2') }}" maxlength="255"
               class="form-control @error($err('line2')) is-invalid @enderror" value="{{ $old('line2') }}">
        @error($err('line2'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-4">
        <label for="{{ $idp }}_building" class="form-label fw-semibold">{{ __('customer_address.field_building') }}</label>
        <input type="text" id="{{ $idp }}_building" name="{{ $name('building') }}" maxlength="120"
               class="form-control @error($err('building')) is-invalid @enderror" value="{{ $old('building') }}">
        @error($err('building'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-4">
        <label for="{{ $idp }}_floor" class="form-label fw-semibold">{{ __('customer_address.field_floor') }}</label>
        <input type="text" id="{{ $idp }}_floor" name="{{ $name('floor') }}" maxlength="20"
               class="form-control @error($err('floor')) is-invalid @enderror" value="{{ $old('floor') }}">
        @error($err('floor'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-4">
        <label for="{{ $idp }}_unit" class="form-label fw-semibold">{{ __('customer_address.field_unit') }}</label>
        <input type="text" id="{{ $idp }}_unit" name="{{ $name('unit') }}" maxlength="50"
               class="form-control @error($err('unit')) is-invalid @enderror" value="{{ $old('unit') }}">
        @error($err('unit'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Administrative areas — smallest first, per the country's config --}}
    @foreach (array_reverse(array_keys($schema['levels']), true) as $area)
        @continue(! isset($schema['levels'][$area]))
        <div class="col-6">
            <label for="{{ $idp }}_{{ $area }}" class="form-label fw-semibold">{{ __('customer_address.level_'.$schema['levels'][$area]) }}{!! $star($area) !!}</label>
            <input type="text" id="{{ $idp }}_{{ $area }}" name="{{ $name($area) }}" maxlength="120" @if($req($area)) required @endif
                   class="form-control @error($err($area)) is-invalid @enderror" value="{{ $old($area) }}">
            @error($err($area))<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach

    <div class="col-6">
        <label for="{{ $idp }}_postal_code" class="form-label fw-semibold">{{ __('customer_address.field_postal_code') }}{!! $star('postal_code') !!}</label>
        <input type="text" id="{{ $idp }}_postal_code" name="{{ $name('postal_code') }}" maxlength="16" inputmode="numeric" @if($req('postal_code')) required @endif
               class="form-control @error($err('postal_code')) is-invalid @enderror" value="{{ $old('postal_code') }}">
        @error($err('postal_code'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="{{ $idp }}_landmark" class="form-label fw-semibold">{{ __('customer_address.field_landmark') }}</label>
        <input type="text" id="{{ $idp }}_landmark" name="{{ $name('landmark') }}" maxlength="255"
               class="form-control @error($err('landmark')) is-invalid @enderror" value="{{ $old('landmark') }}"
               placeholder="{{ __('customer_address.field_landmark_hint') }}">
        @error($err('landmark'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="{{ $idp }}_delivery_instructions" class="form-label fw-semibold">{{ __('customer_address.field_instructions') }}</label>
        <textarea id="{{ $idp }}_delivery_instructions" name="{{ $name('delivery_instructions') }}" maxlength="500" rows="2"
                  class="form-control @error($err('delivery_instructions')) is-invalid @enderror">{{ $old('delivery_instructions') }}</textarea>
        @error($err('delivery_instructions'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
