<x-app-layout>
    <x-slot name="title">{{ $pageTitle }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ $pageTitle }}</x-slot>

    <div class="page-header">
        <h1>{{ $pageTitle }}</h1>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <div class="coming-soon-icon mx-auto">
                <i class="bi {{ $icon ?? 'bi-clock' }} text-primary"></i>
            </div>
            <h5 class="fw-semibold mb-2">{{ $pageTitle }} — Coming Soon</h5>
            <p class="text-muted mb-0" style="max-width: 380px; margin: 0 auto;">
                This feature is under development and will be available in a future sprint.
            </p>
        </div>
    </div>

</x-app-layout>
