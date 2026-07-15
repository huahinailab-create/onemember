<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $merchant->displayName() }} – {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('commerce.store_meta', ['merchant' => $merchant->displayName()]) }}">
    @vite(['resources/css/app.css'])
</head>
<body class="storefront-body">
    <main class="storefront">

        {{-- Business profile --}}
        <header class="storefront-header">
            <div class="storefront-brand">{{ $merchant->displayName() }}</div>
            @if ($merchant->business_type)
                <div class="storefront-type">{{ $merchant->business_type }}</div>
            @endif
            <div class="storefront-contact">
                @if ($merchant->phone)<span><i class="bi bi-telephone me-1"></i>{{ $merchant->phone }}</span>@endif
                @if ($merchant->city)<span><i class="bi bi-geo-alt me-1"></i>{{ $merchant->city }}</span>@endif
            </div>

            {{-- Customer language switcher (BETA-008B — merchant-configured, never browser-derived) --}}
            @php $offeredLanguages = $merchant->customerLanguages(); @endphp
            @if (count($offeredLanguages) > 1)
                <nav class="storefront-langs" aria-label="Language">
                    @foreach ($offeredLanguages as $lang)
                        <a href="{{ route('storefront.show', ['slug' => $merchant->slug, 'lang' => $lang]) }}"
                           class="{{ app()->getLocale() === $lang ? 'is-active' : '' }}"
                           lang="{{ $lang }}">{{ strtoupper($lang) }}</a>
                    @endforeach
                </nav>
            @endif

            {{-- Fulfillment badges --}}
            <div class="storefront-fulfillment">
                @if ($commerce['pickup_enabled'] ?? false)<span class="badge bg-light text-dark"><i class="bi bi-bag me-1"></i>{{ __('commerce.pickup_label') }}</span>@endif
                @if ($commerce['delivery_enabled'] ?? false)
                    <span class="badge bg-light text-dark"><i class="bi bi-bicycle me-1"></i>{{ __('commerce.delivery_label') }}
                        @if (!empty($commerce['delivery_radius_km'])) ({{ $commerce['delivery_radius_km'] }} {{ __('commerce.km') }})@endif
                    </span>
                @endif
                @if ($commerce['shipping_enabled'] ?? false)<span class="badge bg-light text-dark"><i class="bi bi-box-seam me-1"></i>{{ __('commerce.shipping_label') }}</span>@endif
            </div>
        </header>

        {{-- Loyalty summary + rewards --}}
        @if ($campaign)
            <section class="storefront-loyalty">
                <div class="storefront-section-title"><i class="bi bi-star-fill me-2"></i>{{ __('commerce.store_loyalty_title') }}</div>
                <p class="storefront-loyalty-line">{{ __('commerce.store_loyalty_line', ['merchant' => $merchant->displayName()]) }}</p>
                @if ($rewards->isNotEmpty())
                    <ul class="storefront-rewards">
                        @foreach ($rewards as $reward)
                            <li>
                                <span class="storefront-reward-name"><i class="bi bi-gift me-1"></i>{{ $reward->name }}</span>
                                @if ($reward->points_required)
                                    <span class="storefront-reward-points">{{ number_format($reward->points_required) }} {{ __('members.pts') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                <a class="storefront-join-link" href="{{ route('join.show', $merchant->slug) }}">
                    {{ __('commerce.store_join_cta') }} <i class="bi bi-arrow-right"></i>
                </a>
            </section>
        @endif

        @php
            $fulfillmentOptions = array_keys(array_filter([
                'pickup'   => (bool) ($commerce['pickup_enabled'] ?? false),
                'delivery' => (bool) ($commerce['delivery_enabled'] ?? false),
                'shipping' => (bool) ($commerce['shipping_enabled'] ?? false),
            ]));
            $orderingEnabled = $fulfillmentOptions !== [] && $products->isNotEmpty();
        @endphp

        {{-- Product listing --}}
        <section class="storefront-catalogue">
            <div class="storefront-section-title"><i class="bi bi-shop me-2"></i>{{ __('commerce.store_products_title') }}</div>

            @if ($products->isEmpty())
                <div class="storefront-empty">
                    <i class="bi bi-shop" aria-hidden="true"></i>
                    <p>{{ __('commerce.store_no_products') }}</p>
                </div>
            @else
                @foreach ($products as $categoryName => $items)
                    @if ($categoryName !== '')
                        <div class="storefront-category">{{ $categoryName }}</div>
                    @endif
                    <ul class="storefront-products">
                        @foreach ($items as $product)
                            <li class="storefront-product {{ $product->isAvailable() ? '' : 'is-unavailable' }}">
                                <div class="storefront-product-media">
                                    @if ($product->imageUrl())
                                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <i class="bi bi-image" aria-hidden="true"></i>
                                    @endif
                                </div>
                                <div class="storefront-product-body">
                                    <div class="storefront-product-name">{{ $product->name }}</div>
                                    @if ($product->description)
                                        <div class="storefront-product-desc">{{ $product->description }}</div>
                                    @endif
                                    @unless ($product->isAvailable())
                                        <span class="badge bg-secondary">{{ __('commerce.stock_out') }}</span>
                                    @else
                                        @if (! is_null($product->stock_qty) && $product->stock_qty <= 5)
                                            <span class="badge bg-warning text-dark">{{ __('commerce.stock_low', ['count' => $product->stock_qty]) }}</span>
                                        @endif
                                    @endunless
                                </div>
                                <div class="text-end">
                                    <div class="storefront-product-price">
                                        {{ number_format($product->price, 2) }}
                                        <span class="storefront-currency">{{ $merchant->currency ?? config('app.default_currency') }}</span>
                                    </div>
                                    @if ($orderingEnabled && $product->isAvailable())
                                        <input type="number" name="qty[{{ $product->id }}]" min="0" max="99" step="1"
                                               class="form-control form-control-sm storefront-qty" placeholder="0"
                                               form="storefront-order-form" inputmode="numeric"
                                               aria-label="{{ __('commerce.order_qty_label', ['product' => $product->name]) }}">
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </section>

        {{-- Order form (APP-003) — order goes to the merchant, payment direct --}}
        @if ($orderingEnabled)
            <section class="storefront-catalogue">
                <div class="storefront-section-title"><i class="bi bi-bag-check me-2"></i>{{ __('commerce.order_title') }}</div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                @endif

                <form id="storefront-order-form" method="POST" action="{{ route('storefront.order.store', $merchant->slug) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label form-label-sm" for="customer_name">{{ __('commerce.order_name') }} *</label>
                        <input type="text" id="customer_name" name="customer_name" required maxlength="150"
                               class="form-control" value="{{ old('customer_name', $customer?->name) }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm" for="customer_phone">{{ __('commerce.order_phone') }} *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required maxlength="30"
                               class="form-control" value="{{ old('customer_phone', $customer?->phone) }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm" for="fulfillment_type">{{ __('commerce.order_fulfillment') }} *</label>
                        <select id="fulfillment_type" name="fulfillment_type" class="form-select" required>
                            @foreach ($fulfillmentOptions as $option)
                                <option value="{{ $option }}" {{ old('fulfillment_type') === $option ? 'selected' : '' }}>
                                    {{ __('commerce.' . ($option === 'delivery' ? 'delivery_label' : ($option === 'shipping' ? 'shipping_label' : 'pickup_label'))) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- CUSTOMER-001B — signed-in customers pick from their
                         address book (few clicks); guests keep the free-text
                         field. Only the chosen address reaches the merchant. --}}
                    @if ($customer !== null)
                        <fieldset class="mb-2">
                            <legend class="form-label form-label-sm mb-1">{{ __('customer_address.deliver_to') }}</legend>
                            @error('address_choice')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                            @foreach ($customerAddresses as $saved)
                                <div class="form-check storefront-address-option">
                                    <input class="form-check-input" type="radio" name="address_choice" id="addr_{{ $saved->uuid }}"
                                           value="{{ $saved->uuid }}"
                                           {{ old('address_choice', $loop->first ? $saved->uuid : null) === $saved->uuid ? 'checked' : '' }}>
                                    <label class="form-check-label" for="addr_{{ $saved->uuid }}">
                                        <span class="fw-semibold">{{ $saved->label }}</span>
                                        @if ($saved->is_default)<span class="badge bg-success ms-1">{{ __('customer_address.default_badge') }}</span>@endif
                                        <span class="d-block small text-muted">{{ $saved->oneLine() }}</span>
                                    </label>
                                </div>
                            @endforeach
                            <div class="form-check storefront-address-option">
                                <input class="form-check-input" type="radio" name="address_choice" id="addr_new" value="new"
                                       {{ old('address_choice') === 'new' || $customerAddresses->isEmpty() ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="addr_new">{{ __('customer_address.checkout_add_new') }}</label>
                            </div>
                            <details class="storefront-new-address mt-2" {{ old('address_choice') === 'new' || $customerAddresses->isEmpty() || $errors->hasAny(['new_address.line1', 'new_address.postal_code']) ? 'open' : '' }}>
                                <summary class="small text-muted">{{ __('customer_address.checkout_add_new') }}</summary>
                                <div class="mt-2">
                                    <input type="hidden" name="new_address[country]" value="{{ $newAddressCountry = ($customer->country && config('customer_address.countries.'.$customer->country) ? $customer->country : config('customer_address.default_country')) }}">
                                    @include('customer.addresses._fields', ['ns' => 'new_address', 'address' => null, 'country' => $newAddressCountry])
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="save_address" name="save_address" value="1"
                                               {{ old('save_address') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="save_address">{{ __('customer_address.checkout_save') }}</label>
                                    </div>
                                </div>
                            </details>
                            <a href="{{ route('customer.addresses.index') }}" class="small d-inline-block mt-1" target="_blank" rel="noopener">{{ __('customer_address.checkout_manage') }}</a>
                        </fieldset>
                    @else
                        <div class="mb-2">
                            <label class="form-label form-label-sm" for="address">{{ __('commerce.order_address') }}</label>
                            <textarea id="address" name="address" rows="2" maxlength="500" class="form-control"
                                      placeholder="{{ __('commerce.order_address_hint') }}">{{ old('address') }}</textarea>
                            <a href="{{ route('customer.login') }}" class="small d-inline-block mt-1">{{ __('customer_address.checkout_signin') }}</a>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label form-label-sm" for="notes">{{ __('commerce.order_notes') }}</label>
                        <input type="text" id="notes" name="notes" maxlength="500" class="form-control" value="{{ old('notes') }}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-bag-check me-1"></i>{{ __('commerce.order_submit') }}
                    </button>
                    <p class="text-muted mt-2 mb-0" style="font-size:0.72rem;">{{ __('commerce.order_direct_note', ['merchant' => $merchant->displayName()]) }}</p>
                </form>
            </section>
        @endif

        <footer class="storefront-footer">
            <div>{{ __('commerce.store_seller_note', ['merchant' => $merchant->displayName()]) }}</div>
            <div class="mt-1">{{ __('launch.poster_powered') }}</div>
        </footer>
    </main>
</body>
</html>
