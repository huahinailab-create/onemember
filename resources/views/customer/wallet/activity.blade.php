@extends('layouts.customer')

@section('title', __('customer_wallet.activity_title'))
@section('shell-class', 'customer-shell-wide')
@section('wallet-nav', '1')

@section('content')
<h1 class="customer-h1 mb-1">{{ __('customer_wallet.activity_title') }}</h1>
<p class="text-muted mb-4">{{ __('customer_wallet.activity_sub') }}</p>

@include('customer.wallet._activity_list', ['activity' => $activity])
@endsection
