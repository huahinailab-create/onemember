<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — System Health</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-heart-pulse me-2 text-warning"></i>System Health</h4>

            @php
            $statusMap = [
                'green'  => ['bg' => 'success', 'icon' => 'bi-check-circle-fill'],
                'yellow' => ['bg' => 'warning',  'icon' => 'bi-exclamation-circle-fill'],
                'red'    => ['bg' => 'danger',   'icon' => 'bi-x-circle-fill'],
            ];
            $labels = [
                'database'    => 'Database',
                'redis'       => 'Redis / Cache',
                'mail'        => 'Mail Transport',
                'queue'       => 'Queue',
                'storage'     => 'Storage',
                'scheduler'   => 'Scheduler',
                'failed_jobs' => 'Failed Jobs',
                'disk_usage'  => 'Disk Usage',
                'memory'      => 'Memory',
            ];
            @endphp

            <div class="row g-3">
                @foreach ($health as $key => $check)
                    @php $style = $statusMap[$check['status']] ?? $statusMap['yellow']; @endphp
                    <div class="col-md-4">
                        <div class="card border-{{ $style['bg'] }}">
                            <div class="card-body d-flex align-items-center gap-3">
                                <i class="bi {{ $style['icon'] }} text-{{ $style['bg'] }} fs-4"></i>
                                <div>
                                    <div class="fw-semibold">{{ $labels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</div>
                                    <div class="text-muted small">{{ $check['message'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <a href="{{ route('dev.health') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</a>
            </div>
        </div>
    </div>
</x-app-layout>
