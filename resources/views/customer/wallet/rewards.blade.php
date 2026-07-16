@extends('layouts.customer')

@section('title', __('customer_wallet.rewards_title'))
@section('shell-class', 'customer-shell-wide')
@section('wallet-nav', '1')

@section('content')
<h1 class="customer-h1 mb-1">{{ __('customer_wallet.rewards_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer_wallet.rewards_sub') }}</p>

@forelse ($rewardsByMerchant as $merchantName => $rows)
    <h2 class="customer-h2 mb-2">{{ $merchantName }}</h2>
    <div class="mb-4">
        @foreach ($rows as $row)
            @include('customer.wallet._reward_row', ['row' => $row, 'showMerchant' => false])
        @endforeach
    </div>
@empty
    <div class="customer-wallet-empty">
        <i class="bi bi-gift d-block mb-2" aria-hidden="true"></i>
        <p class="mb-1">{{ __('customer_wallet.empty_rewards') }}</p>
        <p class="small text-muted mb-0">{{ __('customer_wallet.empty_rewards_hint') }}</p>
    </div>
@endforelse
@endsection
