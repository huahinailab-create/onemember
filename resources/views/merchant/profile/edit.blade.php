<x-app-layout>
    <x-slot name="title">{{ __('settings.tab_profile') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('settings.tab_profile') }}</x-slot>

    @php
        
        $branding = new \App\Services\MerchantBrandingService($merchant);
    @endphp

    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">{{ __('settings.tab_profile') }}</h1>
        <p class="text-muted mb-0">{{ __('settings.subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('merchant.profile.update') }}" enctype="multipart/form-data"
          x-data="{
              logoPreview: '{{ $branding->logo() ?? '' }}',
              removeLogo: false,
              handleLogo(e) {
                  const file = e.target.files[0];
                  if (!file) return;
                  const reader = new FileReader();
                  reader.onload = ev => { this.logoPreview = ev.target.result; this.removeLogo = false; };
                  reader.readAsDataURL(file);
              }
          }">
        @csrf
        @method('PUT')
        <input type="hidden" name="remove_logo" :value="removeLogo ? '1' : '0'">

        <div class="row g-4">

            {{-- Business Information --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-building me-2 text-primary"></i>{{ __('settings.tab_profile') }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    {{ __('settings.business_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $merchant?->name) }}"
                                       placeholder="{{ __('settings.business_name_ph') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">{{ __('settings.contact_person') }}</label>
                                <input type="text" id="contact_person" name="contact_person"
                                       class="form-control @error('contact_person') is-invalid @enderror"
                                       value="{{ old('contact_person', $merchant?->contact_person) }}"
                                       placeholder="{{ __('settings.contact_person_ph') }}">
                                @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    {{ __('settings.business_email') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $merchant?->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('settings.mobile_number') }}</label>
                                <input type="text" id="phone" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $merchant?->phone) }}"
                                       placeholder="{{ __('settings.mobile_number') }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">{{ __('settings.address_line_1') }}</label>
                                <textarea id="address" name="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $merchant?->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Branding --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-palette me-2 text-primary"></i>{{ __('settings.branding_title') }}
                        <span class="badge bg-primary-subtle text-primary ms-2 fw-normal small">{{ __('settings.branding_badge') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">

                            {{-- Logo Upload --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('settings.logo_label') }}</label>
                                <div class="d-flex align-items-start gap-4 flex-wrap">

                                    {{-- Preview --}}
                                    <div class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:96px;height:96px;background:#f8f9fa;overflow:hidden;">
                                        <template x-if="logoPreview">
                                            <img :src="logoPreview" alt="{{ __('settings.logo_preview_alt') }}"
                                                 style="max-width:96px;max-height:96px;object-fit:contain;">
                                        </template>
                                        <template x-if="!logoPreview">
                                            <i class="bi bi-shop text-secondary" style="font-size:2rem;"></i>
                                        </template>
                                    </div>

                                    <div class="flex-grow-1">
                                        <input type="file" id="logo" name="logo"
                                               class="form-control @error('logo') is-invalid @enderror"
                                               accept=".jpg,.jpeg,.png,.webp"
                                               @change="handleLogo($event)">
                                        <div class="form-text">{{ __('settings.logo_hint') }}</div>
                                        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                        @if ($merchant?->logo_path)
                                            <div class="mt-2">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        @click="removeLogo = true; logoPreview = ''">
                                                    <i class="bi bi-trash me-1"></i>{{ __('settings.logo_remove') }}
                                                </button>
                                                <span x-show="removeLogo" class="text-danger small ms-2">
                                                    {{ __('settings.logo_remove_note') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 p-3 rounded-3 border border-dashed"
                                     style="border-style:dashed!important;background:#f8f9fa;">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>{{ __('settings.branding_powered_by_note') }}
                                    </small>
                                </div>
                            </div>

                            {{-- Brand Colors --}}
                            <div class="col-md-6">
                                <label for="brand_color" class="form-label fw-medium">{{ __('settings.brand_color') }}</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" id="brand_color_picker"
                                           value="{{ old('brand_color', $merchant?->brand_color ?? '#2563EB') }}"
                                           class="form-control form-control-color"
                                           oninput="document.getElementById('brand_color').value=this.value">
                                    <input type="text" id="brand_color" name="brand_color"
                                           class="form-control @error('brand_color') is-invalid @enderror"
                                           value="{{ old('brand_color', $merchant?->brand_color ?? '#2563EB') }}"
                                           placeholder="#2563EB" maxlength="7"
                                           oninput="this.previousElementSibling.previousElementSibling.value=this.value">
                                </div>
                                <div class="form-text">{{ __('settings.brand_color_hint') }}</div>
                                @error('brand_color')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="secondary_color" class="form-label fw-medium">{{ __('settings.secondary_color') }}</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" id="secondary_color_picker"
                                           value="{{ old('secondary_color', $merchant?->secondary_color ?? '#1E293B') }}"
                                           class="form-control form-control-color"
                                           oninput="document.getElementById('secondary_color').value=this.value">
                                    <input type="text" id="secondary_color" name="secondary_color"
                                           class="form-control @error('secondary_color') is-invalid @enderror"
                                           value="{{ old('secondary_color', $merchant?->secondary_color ?? '#1E293B') }}"
                                           placeholder="#1E293B" maxlength="7"
                                           oninput="this.previousElementSibling.previousElementSibling.value=this.value">
                                </div>
                                <div class="form-text">{{ __('settings.secondary_color_hint') }}</div>
                                @error('secondary_color')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tagline --}}
                            <div class="col-12">
                                <label for="business_tagline" class="form-label fw-medium">{{ __('settings.business_tagline') }}</label>
                                <input type="text" id="business_tagline" name="business_tagline"
                                       class="form-control @error('business_tagline') is-invalid @enderror"
                                       value="{{ old('business_tagline', $merchant?->business_tagline) }}"
                                       placeholder="{{ __('settings.business_tagline_ph') }}"
                                       maxlength="100">
                                <div class="form-text">{{ __('settings.business_tagline_hint') }}</div>
                                @error('business_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Receipt Footer --}}
                            <div class="col-12">
                                <label for="receipt_footer" class="form-label fw-medium">{{ __('settings.receipt_footer') }}</label>
                                <textarea id="receipt_footer" name="receipt_footer" rows="3"
                                          class="form-control @error('receipt_footer') is-invalid @enderror"
                                          placeholder="{{ __('settings.receipt_footer_ph') }}"
                                          maxlength="500">{{ old('receipt_footer', $merchant?->receipt_footer) }}</textarea>
                                <div class="form-text">{{ __('settings.receipt_footer_hint') }}</div>
                                @error('receipt_footer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Online Presence --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-globe me-2 text-primary"></i>{{ __('settings.online_presence') }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="website" class="form-label">{{ __('settings.website') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe2"></i></span>
                                    <input type="url" id="website" name="website"
                                           class="form-control @error('website') is-invalid @enderror"
                                           value="{{ old('website', $merchant?->website) }}"
                                           placeholder="https://yourbusiness.com">
                                </div>
                                @error('website')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="facebook_url" class="form-label">{{ __('settings.facebook_url') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                    <input type="url" id="facebook_url" name="facebook_url"
                                           class="form-control @error('facebook_url') is-invalid @enderror"
                                           value="{{ old('facebook_url', $merchant?->facebook_url) }}"
                                           placeholder="https://facebook.com/yourbusiness">
                                </div>
                                @error('facebook_url')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="instagram_url" class="form-label">{{ __('settings.instagram_url') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                    <input type="url" id="instagram_url" name="instagram_url"
                                           class="form-control @error('instagram_url') is-invalid @enderror"
                                           value="{{ old('instagram_url', $merchant?->instagram_url) }}"
                                           placeholder="https://instagram.com/yourbusiness">
                                </div>
                                @error('instagram_url')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="line_url" class="form-label">{{ __('settings.line_url') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-chat-fill"></i></span>
                                    <input type="url" id="line_url" name="line_url"
                                           class="form-control @error('line_url') is-invalid @enderror"
                                           value="{{ old('line_url', $merchant?->line_url) }}"
                                           placeholder="https://line.me/ti/p/yourline">
                                </div>
                                @error('line_url')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Regional Settings --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-gear me-2 text-primary"></i>{{ __('settings.tab_preferences') }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label for="currency" class="form-label">
                                    {{ __('settings.currency') }} <span class="text-danger">*</span>
                                </label>
                                <select id="currency" name="currency"
                                        class="form-select @error('currency') is-invalid @enderror" required>
                                    <option value="THB" {{ old('currency', $merchant?->currency ?? 'THB') === 'THB' ? 'selected' : '' }}>THB – Thai Baht</option>
                                    <option value="USD" {{ old('currency', $merchant?->currency) === 'USD' ? 'selected' : '' }}>USD – US Dollar</option>
                                    <option value="SGD" {{ old('currency', $merchant?->currency) === 'SGD' ? 'selected' : '' }}>SGD – Singapore Dollar</option>
                                    <option value="MYR" {{ old('currency', $merchant?->currency) === 'MYR' ? 'selected' : '' }}>MYR – Malaysian Ringgit</option>
                                    <option value="IDR" {{ old('currency', $merchant?->currency) === 'IDR' ? 'selected' : '' }}>IDR – Indonesian Rupiah</option>
                                    <option value="PHP" {{ old('currency', $merchant?->currency) === 'PHP' ? 'selected' : '' }}>PHP – Philippine Peso</option>
                                    <option value="VND" {{ old('currency', $merchant?->currency) === 'VND' ? 'selected' : '' }}>VND – Vietnamese Dong</option>
                                </select>
                                @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-8">
                                <label for="timezone" class="form-label">
                                    {{ __('settings.timezone') }} <span class="text-danger">*</span>
                                </label>
                                <select id="timezone" name="timezone"
                                        class="form-select @error('timezone') is-invalid @enderror" required>
                                    @php
                                        $timezones = [
                                            'Asia/Bangkok'      => 'Asia/Bangkok (UTC+7) – Thailand, Vietnam, Laos',
                                            'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur (UTC+8) – Malaysia',
                                            'Asia/Singapore'    => 'Asia/Singapore (UTC+8) – Singapore',
                                            'Asia/Jakarta'      => 'Asia/Jakarta (UTC+7) – Indonesia (West)',
                                            'Asia/Makassar'     => 'Asia/Makassar (UTC+8) – Indonesia (Central)',
                                            'Asia/Jayapura'     => 'Asia/Jayapura (UTC+9) – Indonesia (East)',
                                            'Asia/Manila'       => 'Asia/Manila (UTC+8) – Philippines',
                                            'Asia/Ho_Chi_Minh'  => 'Asia/Ho_Chi_Minh (UTC+7) – Vietnam',
                                            'Asia/Rangoon'      => 'Asia/Rangoon (UTC+6:30) – Myanmar',
                                            'Asia/Phnom_Penh'   => 'Asia/Phnom_Penh (UTC+7) – Cambodia',
                                            'UTC'               => 'UTC',
                                        ];
                                        $selected = old('timezone', $merchant?->timezone ?? 'Asia/Bangkok');
                                    @endphp
                                    @foreach ($timezones as $value => $label)
                                        <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('settings.save_profile') }}
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">{{ __('settings.cancel') }}</a>
                </div>
            </div>

        </div>
    </form>

</x-app-layout>
