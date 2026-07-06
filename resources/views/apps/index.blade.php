{{-- Reference implementation of the OneMember Design System (PLATFORM-001):
     x-ui.page-header, x-ui.flash, x-ui.status-badge. --}}
<x-app-layout>
    <x-slot name="title">{{ __('apps.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('apps.title') }}</x-slot>

    <x-ui.page-header :title="__('apps.title')" :subtitle="__('apps.subtitle')" />

    <x-ui.flash :with-errors="true" />

    <div class="row g-3">
        @foreach ($registry as $key => $app)
            @php
                $isInstalled = in_array($key, $installed, true);
                $isAvailable = $app['status'] === 'available';
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="stat-icon {{ $isInstalled ? 'stat-icon-pink' : '' }}">
                                <i class="bi {{ $app['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ __('apps.name_' . $key) }}</div>
                                @if ($isInstalled)
                                    <x-ui.status-badge status="active" :label="__('apps.badge_installed')" />
                                @elseif (! $isAvailable)
                                    <x-ui.status-badge status="coming_soon" :label="__('apps.badge_coming_soon')" />
                                @endif
                            </div>
                        </div>
                        <p class="text-muted small flex-grow-1">{{ __('apps.desc_' . $key) }}</p>

                        @if ($isInstalled)
                            <form method="POST" action="{{ route('apps.uninstall') }}"
                                  onsubmit="return confirm('{{ __('apps.uninstall_confirm') }}');">
                                @csrf
                                <input type="hidden" name="app" value="{{ $key }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    {{ __('apps.uninstall_button') }}
                                </button>
                            </form>
                        @elseif ($isAvailable)
                            <form method="POST" action="{{ route('apps.install') }}">
                                @csrf
                                <input type="hidden" name="app" value="{{ $key }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i>{{ __('apps.install_button') }}
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                {{ __('apps.badge_coming_soon') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <p class="text-muted small mt-4 mb-0">{{ __('apps.core_note') }}</p>
</x-app-layout>
