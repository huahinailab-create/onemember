@extends('layouts.customer')

@section('title', $merchant->displayName())
@section('shell-class', 'customer-shell-wide')
@section('wallet-nav', '1')

@section('content')
<a href="{{ route('customer.wallet.memberships') }}" class="small d-inline-block mb-3">← {{ __('customer_wallet.back_to_memberships') }}</a>

{{-- Merchant profile --}}
<div class="d-flex align-items-center gap-3 mb-3">
    <div class="customer-membership-logo customer-membership-logo-lg">
        @if ($merchant->logo_path)
            <img src="{{ Storage::disk('public')->url($merchant->logo_path) }}" alt="">
        @else
            <span aria-hidden="true">{{ $merchant->initials() }}</span>
        @endif
    </div>
    <div>
        <h1 class="customer-h1 mb-0">{{ $merchant->displayName() }}</h1>
        <div class="small text-muted">
            {{ __('customer_wallet.member_since', ['date' => ($member->joined_at ?? $member->created_at)->translatedFormat('F Y')]) }}
        </div>
    </div>
</div>

{{-- Balance --}}
<div class="customer-wallet-stat customer-wallet-stat-hero mb-3">
    <div class="customer-wallet-stat-value">{{ number_format($member->total_points) }}</div>
    <div class="customer-wallet-stat-label">
        {{ $unit === 'stamps' ? __('customer_wallet.unit_stamps') : __('customer_wallet.unit_points') }}
        @if ($programme)
            · {{ $programme->name }}
        @endif
    </div>
</div>

{{-- Membership information --}}
<h2 class="customer-h2 mb-2">{{ __('customer_wallet.membership_info') }}</h2>
<div class="customer-wallet-box mb-4">
    <dl class="customer-wallet-dl mb-0">
        <dt>{{ __('customer_wallet.member_code') }}</dt>
        <dd>{{ $member->member_code }}</dd>
        <dt>{{ __('customer_wallet.status') }}</dt>
        <dd>{{ $member->status->label() }}</dd>
        @if ($member->last_activity_at)
            <dt>{{ __('customer_wallet.last_visit_label') }}</dt>
            <dd>{{ $member->last_activity_at->translatedFormat('j F Y') }}</dd>
        @endif
        @if ($programme)
            <dt>{{ __('customer_wallet.campaign') }}</dt>
            <dd>{{ $programme->name }}@if($programme->description) — {{ $programme->description }}@endif</dd>
        @endif
    </dl>
</div>

{{-- Rewards (read-only in the MVP) --}}
<h2 class="customer-h2 mb-2">{{ __('customer_wallet.nav_rewards') }}</h2>
@forelse ($rewards as $row)
    @include('customer.wallet._reward_row', ['row' => $row, 'showMerchant' => false])
@empty
    <p class="small text-muted">{{ __('customer_wallet.empty_rewards_merchant') }}</p>
@endforelse

{{-- Recent transactions --}}
<h2 class="customer-h2 mt-4 mb-2">{{ __('customer_wallet.recent_transactions') }}</h2>
@forelse ($transactions as $tx)
    <div class="customer-activity-row">
        <span class="{{ $tx->type->badgeClass() }}">{{ $tx->type->label() }}</span>
        <div class="flex-grow-1 small">
            <span class="fw-semibold {{ $tx->points >= 0 ? 'text-success' : 'text-danger' }}">{{ $tx->points >= 0 ? '+' : '' }}{{ number_format($tx->points) }}</span>
            @if ($tx->note) <span class="text-muted">— {{ $tx->note }}</span> @endif
        </div>
        <time class="small text-muted text-nowrap" datetime="{{ $tx->created_at->toDateString() }}">{{ $tx->created_at->translatedFormat('j M Y') }}</time>
    </div>
@empty
    <p class="small text-muted">{{ __('customer_wallet.empty_transactions') }}</p>
@endforelse

{{-- Merchant contact --}}
<h2 class="customer-h2 mt-4 mb-2">{{ __('customer_wallet.merchant_contact') }}</h2>
<div class="customer-wallet-box mb-3">
    <dl class="customer-wallet-dl mb-0">
        @if ($merchant->phone)
            <dt>{{ __('customer_wallet.contact_phone') }}</dt>
            <dd><a href="tel:{{ $merchant->phone }}">{{ $merchant->phone }}</a></dd>
        @endif
        @if ($merchant->email)
            <dt>{{ __('customer_wallet.contact_email') }}</dt>
            <dd><a href="mailto:{{ $merchant->email }}">{{ $merchant->email }}</a></dd>
        @endif
        @if ($merchant->address_line_1 || $merchant->address)
            <dt>{{ __('customer_wallet.contact_address') }}</dt>
            <dd>{{ $merchant->address_line_1 ?? $merchant->address }}</dd>
        @endif
        @if (! $merchant->phone && ! $merchant->email && ! ($merchant->address_line_1 ?? $merchant->address))
            <dd class="text-muted mb-0">{{ __('customer_wallet.contact_none') }}</dd>
        @endif
    </dl>
</div>

@if ($merchant->hasApp('commerce'))
    <a href="{{ route('storefront.show', $merchant->slug) }}" class="btn btn-primary w-100" target="_blank" rel="noopener">
        <i class="bi bi-shop me-1"></i>{{ __('customer_wallet.open_storefront') }}
    </a>
@endif
@endsection
