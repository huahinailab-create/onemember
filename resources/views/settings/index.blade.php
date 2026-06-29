<x-app-layout>
    <x-slot name="title">{{ __('settings.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('settings.title') }}</x-slot>

    <div class="page-header">
        <h1>{{ __('settings.title') }}</h1>
        <p>{{ __('settings.subtitle') }}</p>
    </div>

    {{-- Trial Lifecycle Banner --}}
    <x-trial-banner :merchant="$merchant" />

    {{-- Flash messages --}}
    @if (session('success') || session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') ?? __('settings.password_updated') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Nav Tabs --}}
    <ul class="nav nav-tabs mb-0" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-profile" data-bs-toggle="tab"
                    data-bs-target="#pane-profile" type="button" role="tab">
                <i class="bi bi-building me-1"></i>{{ __('settings.tab_profile') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-preferences" data-bs-toggle="tab"
                    data-bs-target="#pane-preferences" type="button" role="tab">
                <i class="bi bi-sliders me-1"></i>{{ __('settings.tab_preferences') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-account" data-bs-toggle="tab"
                    data-bs-target="#pane-account" type="button" role="tab">
                <i class="bi bi-person-circle me-1"></i>{{ __('settings.tab_account') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-security" data-bs-toggle="tab"
                    data-bs-target="#pane-security" type="button" role="tab">
                <i class="bi bi-shield-lock me-1"></i>{{ __('settings.tab_security') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ── TAB 1: Business Profile ──────────────────────────── --}}
        <div class="tab-pane fade" id="pane-profile" role="tabpanel">
            <div class="card border-top-0 rounded-top-0">
                <div class="card-body p-4">

                    @if ($errors->any() && request('tab', 'profile') === 'profile')
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium">
                                    {{ __('settings.business_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $merchant?->name) }}"
                                       placeholder="{{ __('settings.business_name_ph') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="business_type" class="form-label fw-medium">
                                    {{ __('settings.business_type') }} <span class="text-danger">*</span>
                                </label>
                                <select id="business_type" name="business_type"
                                        class="form-select @error('business_type') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('business_type', $merchant?->business_type) ? '' : 'selected' }}>
                                        {{ __('buttons.select') }}
                                    </option>
                                    @foreach ([
                                        'Hair Salon', 'Nail Salon', 'Massage & Spa', 'Restaurant & Café',
                                        'Hotel', 'Fashion Retail', 'Beauty & Cosmetics', 'Grocery Store',
                                        'Pet Shop', 'Wholesale', 'Other',
                                    ] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('business_type', $merchant?->business_type) === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('business_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium">{{ __('settings.business_phone') }}</label>
                                <input type="text" id="phone" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $merchant?->phone) }}"
                                       placeholder="e.g. +66 81 234 5678">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="website" class="form-label fw-medium">{{ __('settings.website') }}</label>
                                <input type="url" id="website" name="website"
                                       class="form-control @error('website') is-invalid @enderror"
                                       value="{{ old('website', $merchant?->website) }}"
                                       placeholder="https://www.yourbusiness.com">
                                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">
                                    {{ __('settings.business_email') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $merchant?->email) }}"
                                       placeholder="hello@yourbusiness.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-12">
                                <label for="address_line_1" class="form-label fw-medium">{{ __('settings.address_line_1') }}</label>
                                <input type="text" id="address_line_1" name="address_line_1"
                                       class="form-control @error('address_line_1') is-invalid @enderror"
                                       value="{{ old('address_line_1', $merchant?->address_line_1) }}"
                                       placeholder="{{ __('settings.address_line_1_ph') }}">
                                @error('address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address_line_2" class="form-label fw-medium">{{ __('settings.address_line_2') }}</label>
                                <input type="text" id="address_line_2" name="address_line_2"
                                       class="form-control @error('address_line_2') is-invalid @enderror"
                                       value="{{ old('address_line_2', $merchant?->address_line_2) }}"
                                       placeholder="{{ __('settings.address_line_2_ph') }}">
                                @error('address_line_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label fw-medium">{{ __('settings.city') }}</label>
                                <input type="text" id="city" name="city"
                                       class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $merchant?->city) }}"
                                       placeholder="e.g. Bangkok">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label fw-medium">{{ __('settings.state') }}</label>
                                <input type="text" id="state" name="state"
                                       class="form-control @error('state') is-invalid @enderror"
                                       value="{{ old('state', $merchant?->state) }}"
                                       placeholder="e.g. Bangkok">
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="postal_code" class="form-label fw-medium">{{ __('settings.postal_code') }}</label>
                                <input type="text" id="postal_code" name="postal_code"
                                       class="form-control @error('postal_code') is-invalid @enderror"
                                       value="{{ old('postal_code', $merchant?->postal_code) }}"
                                       placeholder="e.g. 10110">
                                @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label fw-medium">{{ __('settings.country') }}</label>
                                <select id="country" name="country"
                                        class="form-select @error('country') is-invalid @enderror">
                                    <option value="">— {{ __('settings.country') }} —</option>
                                    @php
                                        $countries = [
                                            'Thailand','Singapore','Malaysia','Indonesia','Philippines',
                                            'Vietnam','Myanmar','Cambodia','Laos','Japan','South Korea',
                                            'China','India','Australia','New Zealand','United Kingdom',
                                            'United States','Canada','Germany','France','Netherlands',
                                            'UAE','Saudi Arabia','Other',
                                        ];
                                    @endphp
                                    @foreach ($countries as $c)
                                        <option value="{{ $c }}"
                                            {{ old('country', $merchant?->country) === $c ? 'selected' : '' }}>
                                            {{ $c }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label fw-medium">{{ __('settings.notes') }}</label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Internal notes about your business (optional)">{{ old('notes', $merchant?->notes) }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>

                        <div class="mt-4 pt-2 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('buttons.save_changes') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ── TAB 2: Business Preferences ──────────────────────── --}}
        <div class="tab-pane fade" id="pane-preferences" role="tabpanel"
             x-data="{
                 expType: '{{ old('default_expiration_type', $merchant?->settings['default_expiration_type'] ?? 'never') }}'
             }">
            <div class="card border-top-0 rounded-top-0">
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('settings.preferences.update') }}">
                        @csrf
                        @method('PUT')

                        @php
                            $currencies = [
                                'THB'=>'THB – Thai Baht','USD'=>'USD – US Dollar','EUR'=>'EUR – Euro',
                                'GBP'=>'GBP – British Pound','JPY'=>'JPY – Japanese Yen',
                                'SGD'=>'SGD – Singapore Dollar','MYR'=>'MYR – Malaysian Ringgit',
                                'IDR'=>'IDR – Indonesian Rupiah','PHP'=>'PHP – Philippine Peso',
                                'VND'=>'VND – Vietnamese Dong','AUD'=>'AUD – Australian Dollar',
                                'CAD'=>'CAD – Canadian Dollar',
                            ];
                            $timezones = [
                                'Asia/Bangkok'=>'Bangkok (UTC+7)','Asia/Singapore'=>'Singapore (UTC+8)',
                                'Asia/Kuala_Lumpur'=>'Kuala Lumpur (UTC+8)','Asia/Jakarta'=>'Jakarta (UTC+7)',
                                'Asia/Manila'=>'Manila (UTC+8)','Asia/Ho_Chi_Minh'=>'Ho Chi Minh (UTC+7)',
                                'Asia/Tokyo'=>'Tokyo (UTC+9)','Asia/Seoul'=>'Seoul (UTC+9)',
                                'Asia/Kolkata'=>'Kolkata (UTC+5:30)','Asia/Dubai'=>'Dubai (UTC+4)',
                                'Europe/London'=>'London (UTC+0/+1)','Europe/Paris'=>'Paris (UTC+1/+2)',
                                'America/New_York'=>'New York (UTC-5/-4)','America/Los_Angeles'=>'Los Angeles (UTC-8/-7)',
                                'Australia/Sydney'=>'Sydney (UTC+10/+11)','UTC'=>'UTC',
                            ];
                        @endphp

                        <div class="row g-4">

                            {{-- Currency --}}
                            <div class="col-md-4">
                                <label for="currency" class="form-label fw-medium">
                                    {{ __('settings.currency') }} <span class="text-danger">*</span>
                                </label>
                                <select id="currency" name="currency"
                                        class="form-select @error('currency') is-invalid @enderror" required>
                                    @foreach ($currencies as $code => $label)
                                        <option value="{{ $code }}"
                                            {{ old('currency', $merchant?->currency ?? 'THB') === $code ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Timezone --}}
                            <div class="col-md-5">
                                <label for="timezone" class="form-label fw-medium">
                                    {{ __('settings.timezone') }} <span class="text-danger">*</span>
                                </label>
                                <select id="timezone" name="timezone"
                                        class="form-select @error('timezone') is-invalid @enderror" required>
                                    @foreach ($timezones as $tz => $label)
                                        <option value="{{ $tz }}"
                                            {{ old('timezone', $merchant?->timezone ?? 'Asia/Bangkok') === $tz ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Date Format --}}
                            <div class="col-md-3">
                                <label for="date_format" class="form-label fw-medium">
                                    {{ __('settings.date_format') }} <span class="text-danger">*</span>
                                </label>
                                @php $df = old('date_format', $merchant?->settings['date_format'] ?? 'DD/MM/YYYY'); @endphp
                                <select id="date_format" name="date_format"
                                        class="form-select @error('date_format') is-invalid @enderror" required>
                                    @foreach (['DD/MM/YYYY'=>'DD/MM/YYYY','MM/DD/YYYY'=>'MM/DD/YYYY','YYYY-MM-DD'=>'YYYY-MM-DD'] as $v=>$l)
                                        <option value="{{ $v }}" {{ $df === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('date_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Language --}}
                            <div class="col-md-3">
                                <label for="locale" class="form-label fw-medium">
                                    {{ __('settings.language') }} <span class="text-danger">*</span>
                                </label>
                                <select id="locale" name="locale"
                                        class="form-select @error('locale') is-invalid @enderror" required>
                                    <option value="en" {{ old('locale', $merchant?->settings['locale'] ?? 'en') === 'en' ? 'selected' : '' }}>
                                        English
                                    </option>
                                    <option value="th" {{ old('locale', $merchant?->settings['locale'] ?? 'en') === 'th' ? 'selected' : '' }}>
                                        ภาษาไทย
                                    </option>
                                </select>
                                <div class="form-text">{{ __('settings.language_hint') }}</div>
                                @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12"><hr class="my-0"></div>

                            {{-- Points Expiration Default --}}
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-medium">{{ __('settings.points_expiration') }}</label>
                                <div class="text-muted small mb-2">
                                    {{ __('settings.points_expiration_hint') }}
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="default_expiration_type"
                                               id="exp-never" value="never"
                                               x-model="expType"
                                               {{ old('default_expiration_type', $merchant?->settings['default_expiration_type'] ?? 'never') === 'never' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exp-never">
                                            {{ __('settings.exp_never') }} <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:.7rem;">{{ __('settings.exp_recommended') }}</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="default_expiration_type"
                                               id="exp-months" value="months"
                                               x-model="expType"
                                               {{ old('default_expiration_type', $merchant?->settings['default_expiration_type'] ?? '') === 'months' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exp-months">{{ __('settings.exp_months') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="default_expiration_type"
                                               id="exp-years" value="years"
                                               x-model="expType"
                                               {{ old('default_expiration_type', $merchant?->settings['default_expiration_type'] ?? '') === 'years' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exp-years">{{ __('settings.exp_years') }}</label>
                                    </div>
                                </div>
                                <div class="mt-2" x-show="expType === 'months' || expType === 'years'" x-cloak>
                                    <label for="default_expiration_duration" class="form-label small fw-medium">
                                        Duration (<span x-text="expType"></span>)
                                    </label>
                                    <input type="number" id="default_expiration_duration"
                                           name="default_expiration_duration"
                                           class="form-control @error('default_expiration_duration') is-invalid @enderror"
                                           style="max-width:120px;"
                                           min="1" max="120"
                                           value="{{ old('default_expiration_duration', $merchant?->settings['default_expiration_duration']) }}"
                                           placeholder="e.g. 12">
                                    @error('default_expiration_duration')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                @error('default_expiration_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Birthday Reward Default --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('settings.birthday_default') }}</label>
                                <div class="text-muted small mb-2">
                                    {{ __('settings.birthday_default_hint') }}
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="default_birthday_enabled" name="default_birthday_enabled"
                                           value="1"
                                           {{ old('default_birthday_enabled', $merchant?->settings['default_birthday_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_birthday_enabled">
                                        {{ __('settings.birthday_enabled_label') }}
                                    </label>
                                </div>
                            </div>

                        </div>

                        {{-- Email Notifications --}}
                        @php
                            $emailPrefs = $merchant?->settings['email_notifications'] ?? [];
                        @endphp
                        <div class="col-12 mt-4 pt-3 border-top">
                            <label class="form-label fw-medium">{{ __('settings.email_notifications') }}</label>
                            <div class="text-muted small mb-3">{{ __('settings.email_notifications_hint') }}</div>

                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="email_product_updates" name="email_product_updates" value="1"
                                               {{ old('email_product_updates', $emailPrefs['product_updates'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_product_updates">
                                            {{ __('settings.email_product_updates') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="email_tips" name="email_tips" value="1"
                                               {{ old('email_tips', $emailPrefs['tips'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_tips">
                                            {{ __('settings.email_tips') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="email_feature_announcements" name="email_feature_announcements" value="1"
                                               {{ old('email_feature_announcements', $emailPrefs['feature_announcements'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_feature_announcements">
                                            {{ __('settings.email_feature_announcements') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-1">
                                    <small class="text-muted">
                                        <i class="bi bi-lock-fill me-1"></i>{{ __('settings.email_always_on_note') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('buttons.save_changes') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ── TAB 3: Account ───────────────────────────────────── --}}
        <div class="tab-pane fade" id="pane-account" role="tabpanel">
            <div class="card border-top-0 rounded-top-0">
                <div class="card-body p-4">

                    <div class="row g-0">
                        <div class="col-12">
                            <dl class="row mb-0">

                                <dt class="col-sm-4 text-muted fw-normal">{{ __('settings.merchant_name') }}</dt>
                                <dd class="col-sm-8 fw-medium">
                                    {{ $merchant?->name ?? '—' }}
                                </dd>

                                <dt class="col-sm-4 text-muted fw-normal">{{ __('settings.email_address') }}</dt>
                                <dd class="col-sm-8">{{ $user->email }}</dd>

                                <dt class="col-sm-4 text-muted fw-normal">{{ __('settings.account_created') }}</dt>
                                <dd class="col-sm-8">{{ $user->created_at->format('d M Y') }}</dd>

                                <dt class="col-sm-4 text-muted fw-normal">{{ __('settings.current_plan') }}</dt>
                                <dd class="col-sm-8">
                                    @if ($merchant)
                                        <span class="badge bg-primary">{{ $merchant->currentPlan()->label() }}</span>
                                        <span class="badge {{ $merchant->subscriptionStatus()->badgeClass() }} ms-1">
                                            {{ $merchant->subscriptionStatus()->label() }}
                                        </span>
                                        @if ($merchant->isOnTrial())
                                            <span class="text-muted small ms-1">(Professional features active)</span>
                                        @elseif ($merchant->isTrialExpired())
                                            <span class="text-muted small ms-1">(Free plan limits apply)</span>
                                        @endif
                                    @else
                                        <span class="badge bg-primary">Professional Trial</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4 text-muted fw-normal">{{ __('settings.subscription') }}</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-credit-card me-1"></i>{{ __('settings.manage_subscription') }}
                                    </a>
                                </dd>

                            </dl>

                            <div class="alert alert-info mt-4 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ __('settings.account_info') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── TAB 4: Security ──────────────────────────────────── --}}
        <div class="tab-pane fade" id="pane-security" role="tabpanel">
            <div class="card border-top-0 rounded-top-0">
                <div class="card-body p-4">

                    <h6 class="fw-semibold mb-3">{{ __('settings.change_password') }}</h6>

                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">{{ __('settings.last_password_change') }}</div>
                        <div class="col-sm-8">
                            @if ($user->password_changed_at)
                                {{ $user->password_changed_at->format('d M Y, H:i') }}
                            @else
                                <span class="text-muted">{{ __('settings.never_changed') }}</span>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}"
                          style="max-width:480px;">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-medium">{{ __('settings.current_password') }}</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">{{ __('settings.new_password') }}</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-medium">{{ __('settings.confirm_password') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-lock me-1"></i>{{ __('buttons.save_password') }}
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>{{-- /tab-content --}}

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var tab = new URLSearchParams(window.location.search).get('tab') || 'profile';
        var el  = document.getElementById('tab-' + tab);
        if (el) bootstrap.Tab.getOrCreateInstance(el).show();
    });
    </script>

</x-app-layout>
