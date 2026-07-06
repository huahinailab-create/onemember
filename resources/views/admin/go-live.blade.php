<x-admin-layout title="Go-Live Readiness">

    <div class="stat-card card p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-600 mb-1" style="color:#1A2E5A;">Private Beta / Go-Live Readiness</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem;">Config and local-state checks only — no external services. Also runnable as <code>php artisan onemember:go-live-check</code>.</p>
            </div>
            <div class="text-end">
                <div class="fw-700" style="font-size:2rem;color:{{ $summary['ready'] ? '#059669' : '#DC2626' }};">
                    {{ $summary['passed'] }}/{{ $summary['total'] }}
                </div>
                <span class="badge {{ $summary['ready'] ? 'bg-success' : 'bg-danger' }}">
                    {{ $summary['ready'] ? 'No critical blockers' : count($summary['critical_failed']) . ' critical blocker(s)' }}
                </span>
            </div>
        </div>
    </div>

    <div class="stat-card card">
        <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr style="font-size:0.72rem;color:#6B7280;text-transform:uppercase;letter-spacing:0.04em;">
                    <th class="ps-3">Check</th>
                    <th>Status</th>
                    <th class="pe-3">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summary['checks'] as $check)
                    <tr>
                        <td class="ps-3 fw-medium">{{ str_replace('_', ' ', ucfirst($check['key'])) }}</td>
                        <td>
                            @if ($check['pass'])
                                <span class="badge bg-success">PASS</span>
                            @elseif ($check['critical'])
                                <span class="badge bg-danger">FAIL</span>
                            @else
                                <span class="badge bg-warning text-dark">WARN</span>
                            @endif
                        </td>
                        <td class="pe-3 text-muted" style="font-size:0.85rem;">{{ $check['detail'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

</x-admin-layout>
