{{-- Design System: dashboard stat/KPI card.
     Usage: <x-ui.stat-card icon="bi-people" :label="__('...')" :value="$count" variant="pink" /> --}}
@props(['icon', 'label', 'value', 'variant' => 'navy', 'hint' => null])

<div {{ $attributes->merge(['class' => 'card h-100 ' . ($variant === 'pink' ? 'stat-card-pink' : 'stat-card')]) }}>
    <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon {{ $variant === 'pink' ? 'stat-icon-pink' : '' }}">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div>
            <div class="text-muted" style="font-size:0.78rem;">{{ $label }}</div>
            <div class="fs-4 fw-semibold">{{ $value }}</div>
            @if ($hint)
                <div class="text-muted" style="font-size:0.72rem;">{{ $hint }}</div>
            @endif
        </div>
    </div>
</div>
