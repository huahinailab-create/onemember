@extends('layouts.customer')

@section('title', __('customer_wallet.memberships_title'))
@section('shell-class', 'customer-shell-wide')
@section('wallet-nav', '1')

@section('content')
<h1 class="customer-h1 mb-1">{{ __('customer_wallet.memberships_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer_wallet.memberships_sub') }}</p>

@forelse ($memberships as $link)
    @include('customer.wallet._membership_card', ['link' => $link, 'compact' => false])
@empty
    <div class="customer-wallet-empty">
        <i class="bi bi-shop d-block mb-2" aria-hidden="true"></i>
        <p class="mb-1">{{ __('customer_wallet.empty_memberships') }}</p>
        <p class="small text-muted mb-0">{{ __('customer_wallet.empty_memberships_hint') }}</p>
    </div>
@endforelse
@endsection
