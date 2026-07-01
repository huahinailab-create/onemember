<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Performance</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-speedometer me-2 text-warning"></i>Performance Tools</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            @php
            $commands = [
                ['cmd' => 'optimize',       'label' => 'Optimize',          'icon' => 'bi-lightning',        'variant' => 'outline-primary',   'desc' => 'Cache config, routes, views, events'],
                ['cmd' => 'optimize:clear', 'label' => 'Optimize Clear',    'icon' => 'bi-lightning-charge', 'variant' => 'outline-warning',   'desc' => 'Clear all cached optimizations'],
                ['cmd' => 'cache:clear',    'label' => 'Cache Clear',       'icon' => 'bi-trash',            'variant' => 'outline-secondary', 'desc' => 'Flush application cache'],
                ['cmd' => 'config:clear',   'label' => 'Config Clear',      'icon' => 'bi-gear',             'variant' => 'outline-secondary', 'desc' => 'Clear configuration cache'],
                ['cmd' => 'route:clear',    'label' => 'Route Clear',       'icon' => 'bi-signpost',         'variant' => 'outline-secondary', 'desc' => 'Clear route cache'],
                ['cmd' => 'view:clear',     'label' => 'View Clear',        'icon' => 'bi-eye-slash',        'variant' => 'outline-secondary', 'desc' => 'Clear compiled Blade views'],
                ['cmd' => 'event:clear',    'label' => 'Event Clear',       'icon' => 'bi-broadcast',        'variant' => 'outline-secondary', 'desc' => 'Clear cached event listeners'],
                ['cmd' => 'queue:restart',  'label' => 'Restart Queue',     'icon' => 'bi-arrow-repeat',     'variant' => 'outline-warning',   'desc' => 'Signal queue workers to restart'],
            ];
            @endphp

            <div class="row g-3">
                @foreach ($commands as $cmd)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title d-flex align-items-center gap-2">
                                    <i class="bi {{ $cmd['icon'] }}"></i>{{ $cmd['label'] }}
                                </h6>
                                <p class="text-muted small mb-3">{{ $cmd['desc'] }}</p>
                            </div>
                            <div class="card-footer">
                                <form method="POST" action="{{ route('dev.performance.run') }}">@csrf
                                    <input type="hidden" name="command" value="{{ $cmd['cmd'] }}">
                                    <button class="btn btn-{{ $cmd['variant'] }} w-100"><i class="bi bi-play me-1"></i>Run</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
