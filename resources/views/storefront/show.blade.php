<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $merchant->name }} – {{ config('app.name') }}</title>
    <meta name="description" content="{{ __('commerce.store_meta', ['merchant' => $merchant->name]) }}">
    @vite(['resources/css/app.css'])
</head>
<body class="storefront-body">
    <main class="storefront">

        {{-- Business profile --}}
        <header class="storefront-header">
            <div class="storefront-brand">{{ $merchant->name }}</div>
            @if ($merchant->business_type)
                <div class="storefront-type">{{ $merchant->business_type }}</div>
            @endif
            <div class="storefront-contact">
                @if ($merchant->phone)<span><i class="bi bi-telephone me-1"></i>{{ $merchant->phone }}</span>@endif
                @if ($merchant->city)<span><i class="bi bi-geo-alt me-1"></i>{{ $merchant->city }}</span>@endif
            </div>

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
                <p class="storefront-loyalty-line">{{ __('commerce.store_loyalty_line', ['merchant' => $merchant->name]) }}</p>
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

        {{-- Product listing --}}
        <section class="storefront-catalogue">
            <div class="storefront-section-title"><i class="bi bi-shop me-2"></i>{{ __('commerce.store_products_title') }}</div>

            @if ($products->isEmpty())
                <p class="text-muted">{{ __('commerce.store_no_products') }}</p>
            @else
                @foreach ($products as $categoryName => $items)
                    @if ($categoryName !== '')
                        <div class="storefront-category">{{ $categoryName }}</div>
                    @endif
                    <ul class="storefront-products">
                        @foreach ($items as $product)
                            <li class="storefront-product {{ $product->isAvailable() ? '' : 'is-unavailable' }}">
                                <div>
                                    <div class="storefront-product-name">{{ $product->name }}</div>
                                    @if ($product->description)
                                        <div class="storefront-product-desc">{{ $product->description }}</div>
                                    @endif
                                    @unless ($product->isAvailable())
                                        <span class="badge bg-secondary">{{ __('commerce.stock_out') }}</span>
                                    @endunless
                                </div>
                                <div class="storefront-product-price">
                                    {{ number_format($product->price, 2) }}
                                    <span class="storefront-currency">{{ $merchant->currency ?? config('app.default_currency') }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </section>

        <footer class="storefront-footer">
            <div>{{ __('commerce.store_seller_note', ['merchant' => $merchant->name]) }}</div>
            <div class="mt-1">{{ __('launch.poster_powered') }}</div>
        </footer>
    </main>
</body>
</html>
