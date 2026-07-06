{{-- Design System: standard page header (title + subtitle + action slot).
     Usage: <x-ui.page-header :title="__('...')" :subtitle="__('...')">actions</x-ui.page-header> --}}
@props(['title', 'subtitle' => null, 'backUrl' => null, 'backLabel' => null])

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>{{ $title }}</h1>
        @if ($backUrl)
            <p>
                <a href="{{ $backUrl }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i>{{ $backLabel ?? __('buttons.back') }}
                </a>
            </p>
        @elseif ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="d-flex gap-2 flex-wrap page-header-actions">{{ $slot }}</div>
    @endif
</div>
