{{-- CUSTOMER-001C — wallet navigation: horizontally scrollable pills,
     mobile-first, large touch targets, keyboard/screen-reader friendly. --}}
<nav class="customer-wallet-nav" aria-label="{{ __('customer_wallet.nav_label') }}">
    @foreach ([
        'customer.wallet'             => ['bi-house-door', __('customer_wallet.nav_home')],
        'customer.wallet.memberships' => ['bi-shop', __('customer_wallet.nav_memberships')],
        'customer.wallet.rewards'     => ['bi-gift', __('customer_wallet.nav_rewards')],
        'customer.wallet.activity'    => ['bi-clock-history', __('customer_wallet.nav_activity')],
        'customer.wallet.orders'      => ['bi-bag', __('customer_wallet.nav_orders')],
    ] as $route => [$icon, $label])
        <a href="{{ route($route) }}"
           class="customer-wallet-nav-link {{ request()->routeIs($route) ? 'active' : '' }}"
           @if (url()->current() === route($route)) aria-current="page" @endif>
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
            <span>{{ $label }}</span>
        </a>
    @endforeach
</nav>
