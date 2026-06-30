<x-portal-layout :branding="$branding" :title="__('portal.disabled_title')">

    <div class="container py-5 text-center" style="max-width:400px">
        <div class="mb-4">
            @if ($branding->logo())
                <img src="{{ $branding->logo() }}"
                     alt="{{ $branding->displayName() }}"
                     class="portal-logo mb-3"
                     style="max-height:64px;max-width:180px;object-fit:contain;">
            @endif
            <div class="coming-soon-icon bg-secondary bg-opacity-10 mx-auto">
                <i class="bi bi-slash-circle text-secondary fs-2"></i>
            </div>
        </div>
        <h2 class="h5 fw-bold">{{ __('portal.disabled_title') }}</h2>
        <p class="text-muted">{{ __('portal.disabled_message') }}</p>
        <p class="text-muted small">{{ $branding->displayName() }}</p>
    </div>

</x-portal-layout>
