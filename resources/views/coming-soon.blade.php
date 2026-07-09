<x-app-layout>
    <x-slot name="title">{{ $pageTitle }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ $pageTitle }}</x-slot>

    <div class="page-header">
        <h1>{{ $pageTitle }}</h1>
    </div>

    {{-- MR-002: merchant-friendly, localized placeholder. Pages can pass a
         specific body/CTA (e.g. Rewards points to Campaigns, where rewards
         actually live) so a "coming soon" item is never a dead end. --}}
    <div class="card">
        <x-ui.empty-state
            :icon="$icon ?? 'bi-clock'"
            :title="$emptyTitle ?? __('messages.coming_soon_title', ['page' => $pageTitle])"
            :body="$emptyBody ?? __('messages.coming_soon_body')"
            :help-topic="$helpTopic ?? null">
            @isset($ctaRoute)
                <a href="{{ route($ctaRoute) }}" class="btn btn-primary btn-sm">
                    <i class="bi {{ $ctaIcon ?? 'bi-arrow-right' }} me-1" aria-hidden="true"></i>{{ $ctaLabel }}
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-house me-1" aria-hidden="true"></i>{{ __('messages.back_to_dashboard') }}
                </a>
            @endisset
        </x-ui.empty-state>
    </div>

</x-app-layout>
