<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Dashboard</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0"><i class="bi bi-grid-1x2 me-2 text-warning"></i>Developer Dashboard</h4>
                <span class="badge bg-warning text-dark">{{ strtoupper(app()->environment()) }}</span>
            </div>

            {{-- Stats Row --}}
            <div class="row g-3 mb-4">
                @foreach ([
                    ['label' => 'Merchants',    'value' => $stats['merchants'],    'icon' => 'bi-shop',             'color' => 'primary'],
                    ['label' => 'Members',      'value' => $stats['members'],      'icon' => 'bi-people',           'color' => 'success'],
                    ['label' => 'Transactions', 'value' => $stats['transactions'], 'icon' => 'bi-arrow-left-right', 'color' => 'info'],
                    ['label' => 'Rewards',      'value' => $stats['rewards'],      'icon' => 'bi-gift',             'color' => 'warning'],
                    ['label' => 'Failed Jobs',  'value' => $stats['failed_jobs'],  'icon' => 'bi-exclamation-circle','color' => $stats['failed_jobs'] > 0 ? 'danger' : 'secondary'],
                    ['label' => 'Pending Jobs', 'value' => $stats['pending_jobs'], 'icon' => 'bi-hourglass-split',  'color' => 'secondary'],
                ] as $card)
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card text-center h-100">
                            <div class="card-body py-3">
                                <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }} fs-4 d-block mb-1"></i>
                                <div class="h4 fw-bold mb-0">{{ number_format($card['value']) }}</div>
                                <div class="text-muted small">{{ $card['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Health Row --}}
            <h6 class="text-muted text-uppercase mb-3 small fw-semibold">System Health</h6>
            @php $statusMap = ['green'=>['bg'=>'success','icon'=>'bi-check-circle-fill'],'yellow'=>['bg'=>'warning','icon'=>'bi-exclamation-circle-fill'],'red'=>['bg'=>'danger','icon'=>'bi-x-circle-fill']]; @endphp
            <div class="row g-2 mb-4">
                @foreach ($health as $key => $check)
                    @php $style = $statusMap[$check['status']] ?? $statusMap['yellow']; @endphp
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="d-flex align-items-center gap-2 border rounded px-3 py-2">
                            <i class="bi {{ $style['icon'] }} text-{{ $style['bg'] }}"></i>
                            <div>
                                <div class="fw-semibold small">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                <div class="text-muted" style="font-size:0.7rem;">{{ $check['message'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Env quick summary --}}
            <h6 class="text-muted text-uppercase mb-3 small fw-semibold">Environment</h6>
            <div class="row g-2">
                @foreach (['app_env','php_version','laravel_version','mail_mailer','queue_connection','git_branch','git_commit'] as $key)
                    @if (isset($env[$key]))
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="border rounded px-3 py-2">
                                <div class="text-muted" style="font-size:0.7rem;">{{ str_replace('_',' ',ucwords($key,'_')) }}</div>
                                <code class="small">{{ $env[$key] }}</code>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
