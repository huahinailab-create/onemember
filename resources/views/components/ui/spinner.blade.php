{{-- Design System: loading state.
     Usage: <x-ui.spinner /> or <x-ui.spinner :label="__('...')" size="sm" /> --}}
@props(['label' => null, 'size' => null])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center gap-2 text-muted']) }}>
    <div class="spinner-border {{ $size === 'sm' ? 'spinner-border-sm' : '' }} text-primary" role="status">
        <span class="visually-hidden">{{ $label ?? 'Loading…' }}</span>
    </div>
    @if ($label)
        <span style="font-size:0.875rem;">{{ $label }}</span>
    @endif
</div>
