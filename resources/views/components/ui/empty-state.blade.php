{{-- Design System: standard empty state for lists/tables.
     Usage: <x-ui.empty-state icon="bi-inbox" :title="__('...')" :body="__('...')"><a .../></x-ui.empty-state> --}}
@props(['icon' => 'bi-inbox', 'title', 'body' => null])

<div {{ $attributes->merge(['class' => 'text-center text-muted py-5']) }}>
    <i class="bi {{ $icon }} fs-1 d-block mb-2"></i>
    <div class="fw-semibold">{{ $title }}</div>
    @if ($body)
        <p class="mb-0 mt-1" style="font-size:0.875rem;">{{ $body }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
