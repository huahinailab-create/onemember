<x-app-layout>
    <x-slot name="title">{{ __('commerce.fulfillment_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('commerce.fulfillment_title') }}</x-slot>

    <div class="page-header">
        <h1>{{ __('commerce.fulfillment_title') }}</h1>
        <p>{{ __('commerce.fulfillment_subtitle') }}</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('commerce.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Pickup --}}
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="pickup_enabled" name="pickup_enabled" value="1"
                                   {{ old('pickup_enabled', $commerce['pickup_enabled'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="pickup_enabled">{{ __('commerce.pickup_label') }}</label>
                        </div>

                        {{-- Merchant delivery --}}
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="delivery_enabled" name="delivery_enabled" value="1"
                                   {{ old('delivery_enabled', $commerce['delivery_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="delivery_enabled">{{ __('commerce.delivery_label') }}</label>
                        </div>
                        <div class="row g-3 mb-3 ms-4">
                            <div class="col-6">
                                <label for="delivery_radius_km" class="form-label form-label-sm">{{ __('commerce.delivery_radius') }}</label>
                                <input type="number" id="delivery_radius_km" name="delivery_radius_km" min="0" step="0.5"
                                       class="form-control form-control-sm @error('delivery_radius_km') is-invalid @enderror"
                                       value="{{ old('delivery_radius_km', $commerce['delivery_radius_km'] ?? '') }}">
                                @error('delivery_radius_km')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">{{ __('commerce.delivery_radius_hint') }}</div>
                            </div>
                            <div class="col-6">
                                <label for="delivery_fee" class="form-label form-label-sm">{{ __('commerce.delivery_fee') }}</label>
                                <input type="number" id="delivery_fee" name="delivery_fee" min="0" step="0.01"
                                       class="form-control form-control-sm @error('delivery_fee') is-invalid @enderror"
                                       value="{{ old('delivery_fee', $commerce['delivery_fee'] ?? '') }}">
                                @error('delivery_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Shipping --}}
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="shipping_enabled" name="shipping_enabled" value="1"
                                   {{ old('shipping_enabled', $commerce['shipping_enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="shipping_enabled">{{ __('commerce.shipping_label') }}</label>
                        </div>
                        <div class="row g-3 mb-4 ms-4">
                            <div class="col-6">
                                <label for="shipping_fee" class="form-label form-label-sm">{{ __('commerce.shipping_fee') }}</label>
                                <input type="number" id="shipping_fee" name="shipping_fee" min="0" step="0.01"
                                       class="form-control form-control-sm @error('shipping_fee') is-invalid @enderror"
                                       value="{{ old('shipping_fee', $commerce['shipping_fee'] ?? '') }}">
                                @error('shipping_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Payment instructions (direct customer→merchant; ADR-011) --}}
                        <div class="mb-4">
                            <label for="payment_instructions" class="form-label fw-medium">{{ __('commerce.payment_instructions') }}</label>
                            <textarea id="payment_instructions" name="payment_instructions" rows="2" maxlength="500"
                                      class="form-control @error('payment_instructions') is-invalid @enderror"
                                      placeholder="{{ __('commerce.payment_instructions_ph') }}">{{ old('payment_instructions', $commerce['payment_instructions'] ?? '') }}</textarea>
                            @error('payment_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">{{ __('commerce.payment_note') }}</div>
                        </div>

                        {{-- Merchant's own payment QR image (displayed to customers; ADR-011) --}}
                        <div class="mb-4">
                            <label for="payment_qr" class="form-label fw-medium">{{ __('commerce.payment_qr') }}</label>
                            @if (!empty($commerce['payment_qr_path']))
                                <div class="mb-2">
                                    <img src="{{ Storage::disk('public')->url($commerce['payment_qr_path']) }}"
                                         alt="{{ __('commerce.payment_qr') }}" class="commerce-payment-qr-preview">
                                </div>
                            @endif
                            <input type="file" id="payment_qr" name="payment_qr" accept="image/*"
                                   class="form-control @error('payment_qr') is-invalid @enderror">
                            @error('payment_qr')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">{{ __('commerce.payment_qr_hint') }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>{{ __('buttons.save_changes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
