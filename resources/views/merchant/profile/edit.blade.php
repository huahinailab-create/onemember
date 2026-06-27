<x-app-layout>
    <x-slot name="title">Merchant Profile – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">Merchant Profile</x-slot>

    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">Merchant Profile</h1>
        <p class="text-muted mb-0">Manage your business information.</p>
    </div>

    <form method="POST" action="{{ route('merchant.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- Business Information --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-building me-2 text-primary"></i>Business Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Business Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $merchant?->name) }}"
                                       placeholder="e.g. Brew & Bloom Coffee" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person"
                                       class="form-control @error('contact_person') is-invalid @enderror"
                                       value="{{ old('contact_person', $merchant?->contact_person) }}"
                                       placeholder="e.g. John Smith">
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Business Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $merchant?->email) }}"
                                       placeholder="e.g. hello@yourbusiness.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Mobile Number</label>
                                <input type="text" id="phone" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $merchant?->phone) }}"
                                       placeholder="e.g. +66 81 234 5678">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Business Address</label>
                                <textarea id="address" name="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Street, City, Country">{{ old('address', $merchant?->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Business Logo --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-image me-2 text-primary"></i>Business Logo
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-4">
                            <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px; flex-shrink: 0;">
                                @if ($merchant?->logo_path)
                                    <img src="{{ asset('storage/' . $merchant->logo_path) }}"
                                         alt="Logo" class="img-fluid rounded-3"
                                         style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                @else
                                    <i class="bi bi-shop text-secondary fs-2"></i>
                                @endif
                            </div>
                            <div>
                                <input type="file" id="logo" name="logo"
                                       class="form-control @error('logo') is-invalid @enderror"
                                       accept="image/*" disabled>
                                <div class="form-text">Logo upload will be available in a future sprint.</div>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold py-3">
                        <i class="bi bi-gear me-2 text-primary"></i>Regional Settings
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label for="currency" class="form-label">
                                    Currency <span class="text-danger">*</span>
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
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="timezone" class="form-label">
                                    Time Zone <span class="text-danger">*</span>
                                </label>
                                <select id="timezone" name="timezone"
                                        class="form-select @error('timezone') is-invalid @enderror" required>
                                    @php
                                        $timezones = [
                                            'Asia/Bangkok'    => 'Asia/Bangkok (UTC+7) – Thailand, Vietnam, Laos',
                                            'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur (UTC+8) – Malaysia',
                                            'Asia/Singapore'  => 'Asia/Singapore (UTC+8) – Singapore',
                                            'Asia/Jakarta'    => 'Asia/Jakarta (UTC+7) – Indonesia (West)',
                                            'Asia/Makassar'   => 'Asia/Makassar (UTC+8) – Indonesia (Central)',
                                            'Asia/Jayapura'   => 'Asia/Jayapura (UTC+9) – Indonesia (East)',
                                            'Asia/Manila'     => 'Asia/Manila (UTC+8) – Philippines',
                                            'Asia/Ho_Chi_Minh'=> 'Asia/Ho_Chi_Minh (UTC+7) – Vietnam',
                                            'Asia/Rangoon'    => 'Asia/Rangoon (UTC+6:30) – Myanmar',
                                            'Asia/Phnom_Penh' => 'Asia/Phnom_Penh (UTC+7) – Cambodia',
                                            'UTC'             => 'UTC',
                                        ];
                                        $selected = old('timezone', $merchant?->timezone ?? 'Asia/Bangkok');
                                    @endphp
                                    @foreach ($timezones as $value => $label)
                                        <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Save Profile
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>

        </div>
    </form>

</x-app-layout>
