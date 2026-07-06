<x-app-layout>
    <x-slot name="title">{{ __('apps.title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('apps.title') }}</x-slot>

    <div class="page-header">
        <h1>{{ __('apps.title') }}</h1>
        <p>{{ __('apps.subtitle') }}</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
    @endif

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
                                    <span class="badge bg-success">{{ __('apps.badge_installed') }}</span>
                                @elseif (! $isAvailable)
                                    <span class="badge bg-secondary">{{ __('apps.badge_coming_soon') }}</span>
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
