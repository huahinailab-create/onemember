{{-- CUSTOMER-001C — uniform activity feed (joins, transactions, orders). --}}
@forelse ($activity as $item)
    @php
        [$icon, $tone] = match ($item['type']) {
            'joined'   => ['bi-stars', 'text-primary'],
            'earn'     => ['bi-plus-circle', 'text-success'],
            'redeem'   => ['bi-gift', 'text-primary'],
            'birthday' => ['bi-cake2', 'text-warning'],
            'expire'   => ['bi-hourglass-bottom', 'text-muted'],
            'adjust'   => ['bi-sliders', 'text-muted'],
            'order'    => ['bi-bag-check', 'text-primary'],
            default    => ['bi-dot', 'text-muted'],
        };
    @endphp
    <div class="customer-activity-row">
        <i class="bi {{ $icon }} {{ $tone }}" aria-hidden="true"></i>
        <div class="flex-grow-1">
            <div class="small">
                {{ __('customer_wallet.activity_'.$item['type'], ['merchant' => $item['merchant']]) }}
                @if ($item['points'] !== null)
                    <span class="fw-semibold {{ $item['points'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $item['points'] >= 0 ? '+' : '' }}{{ number_format($item['points']) }}
                    </span>
                @endif
            </div>
            @if ($item['note'])
                <div class="small text-muted">{{ $item['note'] }}</div>
            @endif
        </div>
        <time class="small text-muted text-nowrap" datetime="{{ $item['at']->toDateString() }}">{{ $item['at']->translatedFormat('j M') }}</time>
    </div>
@empty
    <div class="customer-wallet-empty">
        <i class="bi bi-clock-history d-block mb-2" aria-hidden="true"></i>
        <p class="mb-0">{{ __('customer_wallet.empty_activity') }}</p>
    </div>
@endforelse
