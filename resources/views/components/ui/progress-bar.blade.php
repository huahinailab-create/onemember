{{-- Design System: CSS bar used in charts/widgets (funnel, analytics shares,
     postal distribution). No JS chart libraries (ADR-005 minimalism).
     Usage: <x-ui.progress-bar :percent="72" color="pink" /> --}}
@props(['percent', 'color' => 'pink', 'height' => '6px'])

@php
    $percent = max(0, min(100, (float) $percent));
    $fill    = $color === 'navy' ? '#1A2E5A' : '#FF1585';
@endphp

<div {{ $attributes->merge(['class' => 'om-progress']) }} style="height:{{ $height }};"
     role="progressbar" aria-valuenow="{{ round($percent) }}" aria-valuemin="0" aria-valuemax="100">
    <div class="om-progress-fill" style="width:{{ $percent }}%;background:{{ $fill }};"></div>
</div>
