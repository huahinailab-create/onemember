@php
    /** CUSTOMER-001C — one reward line; redeem is honestly disabled (MVP). */
    $reward = $row['reward'];
    $available = $row['status'] === 'available';
@endphp
<div class="customer-reward-row">
    <i class="bi bi-gift {{ $available ? 'text-primary' : 'text-muted' }}" aria-hidden="true"></i>
    <div class="flex-grow-1">
        <div class="fw-semibold small">{{ $reward->name }}</div>
        <div class="small text-muted">
            @if ($showMerchant ?? false)
                {{ $row['merchant']->displayName() }} ·
            @endif
            {{ trans_choice('customer_wallet.points_required', $reward->points_required, ['count' => number_format($reward->points_required)]) }}
        </div>
    </div>
    @if ($available)
        <span class="badge bg-success">{{ __('customer_wallet.reward_available') }}</span>
    @else
        <span class="badge bg-secondary">{{ __('customer_wallet.reward_coming_soon') }}</span>
    @endif
    <button type="button" class="btn btn-sm btn-outline-primary" disabled
            title="{{ __('customer_wallet.redeem_not_yet') }}" aria-disabled="true">
        {{ __('customer_wallet.redeem') }}
    </button>
</div>
