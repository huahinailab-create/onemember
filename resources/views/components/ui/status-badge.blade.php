{{-- Design System: canonical status→colour mapping. One place decides colours.
     Usage: <x-ui.status-badge :status="$order->status" :label="__('...')" /> --}}
@props(['status', 'label' => null])

@php
    $class = match ($status) {
        'active', 'completed', 'paid', 'ok'                  => 'bg-success',
        'trial', 'placed', 'accepted', 'ready', 'info'       => 'bg-primary',
        'paused', 'unpaid', 'warn', 'pending', 'coming_soon' => 'bg-warning text-dark',
        'cancelled', 'suspended', 'expired', 'archived', 'error' => 'bg-danger',
        default                                              => 'bg-secondary',
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $class]) }}>{{ $label ?? $status }}</span>
