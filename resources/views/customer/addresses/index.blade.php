@extends('layouts.customer')

@section('title', __('customer_address.index_title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-1">
    <h1 class="customer-h1 mb-0">{{ __('customer_address.index_title') }}</h1>
</div>
<p class="text-muted mb-3">{{ __('customer_address.index_sub') }}</p>

<div class="d-grid mb-3">
    <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>{{ __('customer_address.add_address') }}
    </a>
</div>

@if ($addresses->isNotEmpty() || $search !== '')
    <form method="GET" action="{{ route('customer.addresses.index') }}" class="mb-3" role="search">
        <div class="input-group">
            <input type="search" name="q" class="form-control" value="{{ $search }}"
                   placeholder="{{ __('customer_address.search_hint') }}" aria-label="{{ __('customer_address.search_hint') }}">
            <button type="submit" class="btn btn-outline-primary" aria-label="{{ __('customer_address.search') }}">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
@endif

@forelse ($addresses as $address)
    <div class="customer-address-card {{ $address->is_active ? '' : 'customer-address-archived' }}">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
                <span class="fw-semibold">{{ $address->label }}</span>
                @if ($address->is_default)
                    <span class="badge bg-success ms-1">{{ __('customer_address.default_badge') }}</span>
                @endif
                @unless ($address->is_active)
                    <span class="badge bg-secondary ms-1">{{ __('customer_address.archived_badge') }}</span>
                @endunless
                <div class="small text-muted mt-1">{{ $address->recipient_name }}@if($address->phone) · {{ $address->phone }}@endif</div>
                <div class="small mt-1">
                    @foreach ($address->displayLines() as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <a href="{{ route('customer.addresses.edit', $address) }}" class="btn btn-sm btn-outline-primary">{{ __('customer_address.edit') }}</a>
            @if ($address->is_active)
                @unless ($address->is_default)
                    <form method="POST" action="{{ route('customer.addresses.default', $address) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('customer_address.make_default') }}</button>
                    </form>
                @endunless
                <form method="POST" action="{{ route('customer.addresses.duplicate', $address) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('customer_address.duplicate') }}</button>
                </form>
                <form method="POST" action="{{ route('customer.addresses.archive', $address) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('customer_address.archive') }}</button>
                </form>
            @else
                <form method="POST" action="{{ route('customer.addresses.restore', $address) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('customer_address.restore') }}</button>
                </form>
            @endif
            <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}"
                  onsubmit="return confirm('{{ __('customer_address.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('customer_address.delete') }}</button>
            </form>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-4">
        <i class="bi bi-geo-alt d-block mb-2" style="font-size:1.8rem;" aria-hidden="true"></i>
        <p class="mb-0">{{ $search !== '' ? __('customer_address.empty_search') : __('customer_address.empty_book') }}</p>
    </div>
@endforelse

<div class="mt-4">
    <a href="{{ route('customer.profile') }}">← {{ __('customer.back_to_profile') }}</a>
</div>
@endsection
