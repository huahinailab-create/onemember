@extends('layouts.customer')

@section('title', __('customer_wallet.home_title'))
@section('shell-class', 'customer-shell-wide')
@section('wallet-nav', '1')

@section('content')
<h1 class="customer-h1 mb-1">{{ __('customer_wallet.home_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer_wallet.home_welcome', ['name' => $customer->displayName()]) }}</p>

{{-- Summary --}}
<div class="row g-3 mb-4" role="group" aria-label="{{ __('customer_wallet.summary_label') }}">
    <div class="col-6">
        <div class="customer-wallet-stat">
            <div class="customer-wallet-stat-value">{{ $summary['merchants'] }}</div>
            <div class="customer-wallet-stat-label">{{ __('customer_wallet.stat_merchants') }}</div>
        </div>
    </div>
    <div class="col-6">
        <div class="customer-wallet-stat">
            <div class="customer-wallet-stat-value">{{ $summary['rewards_available'] }}</div>
            <div class="customer-wallet-stat-label">{{ __('customer_wallet.stat_rewards') }}</div>
        </div>
    </div>
</div>

{{-- Memberships preview --}}
<div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="customer-h2 mb-0">{{ __('customer_wallet.nav_memberships') }}</h2>
    <a href="{{ route('customer.wallet.memberships') }}" class="small">{{ __('customer_wallet.see_all') }}</a>
</div>
@forelse ($memberships as $link)
    @include('customer.wallet._membership_card', ['link' => $link, 'compact' => true])
@empty
    <div class="customer-wallet-empty">
        <i class="bi bi-shop d-block mb-2" aria-hidden="true"></i>
        <p class="mb-1">{{ __('customer_wallet.empty_memberships') }}</p>
        <p class="small text-muted mb-0">{{ __('customer_wallet.empty_memberships_hint') }}</p>
    </div>
@endforelse

{{-- Recent activity --}}
<div class="d-flex align-items-center justify-content-between mt-4 mb-2">
    <h2 class="customer-h2 mb-0">{{ __('customer_wallet.nav_activity') }}</h2>
    <a href="{{ route('customer.wallet.activity') }}" class="small">{{ __('customer_wallet.see_all') }}</a>
</div>
@include('customer.wallet._activity_list', ['activity' => $activity])

{{-- Quick links --}}
<h2 class="customer-h2 mt-4 mb-2">{{ __('customer_wallet.quick_links') }}</h2>
<div class="row g-2 mb-3">
    @foreach ([
        ['route' => route('customer.addresses.index'), 'icon' => 'bi-geo-alt',  'label' => __('customer_address.index_title')],
        ['route' => route('customer.profile'),          'icon' => 'bi-person',   'label' => __('customer.profile_title')],
        ['route' => route('customer.settings'),         'icon' => 'bi-gear',     'label' => __('customer.settings_title')],
        ['route' => route('customer.wallet.orders'),    'icon' => 'bi-bag',      'label' => __('customer_wallet.nav_orders')],
    ] as $quick)
        <div class="col-6 col-md-3">
            <a href="{{ $quick['route'] }}" class="customer-wallet-tile">
                <i class="bi {{ $quick['icon'] }}" aria-hidden="true"></i>
                <span>{{ $quick['label'] }}</span>
            </a>
        </div>
    @endforeach
</div>

{{-- Future wallet surfaces — reserved, honestly labelled --}}
<h2 class="customer-h2 mt-4 mb-2">{{ __('customer_wallet.coming_soon_title') }}</h2>
<div class="row g-2">
    @foreach ([
        ['icon' => 'bi-credit-card-2-front', 'label' => __('customer_wallet.soon_membership_cards')],
        ['icon' => 'bi-gift',                'label' => __('customer_wallet.soon_gift_cards')],
        ['icon' => 'bi-arrow-repeat',        'label' => __('customer_wallet.soon_subscriptions')],
        ['icon' => 'bi-calendar-check',      'label' => __('customer_wallet.soon_appointments')],
        ['icon' => 'bi-calendar2-week',      'label' => __('customer_wallet.soon_bookings')],
        ['icon' => 'bi-wallet2',             'label' => __('customer_wallet.soon_digital_wallet')],
    ] as $soon)
        <div class="col-6 col-md-4">
            <div class="customer-wallet-tile customer-wallet-tile-soon" aria-disabled="true">
                <i class="bi {{ $soon['icon'] }}" aria-hidden="true"></i>
                <span>{{ $soon['label'] }}</span>
                <span class="badge text-bg-light">{{ __('customer_wallet.coming_soon') }}</span>
            </div>
        </div>
    @endforeach
</div>
@endsection
