<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Queue Inspector</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-stack-overflow me-2 text-warning"></i>Queue Inspector</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="text-muted small">Driver</div><code>{{ $queueInfo['driver'] }}</code></div></div></div>
                <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="display-6 fw-bold">{{ $stats['pending'] }}</div><div class="text-muted small">Pending</div></div></div></div>
                <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="display-6 fw-bold {{ $stats['failed'] > 0 ? 'text-danger' : '' }}">{{ $stats['failed'] }}</div><div class="text-muted small">Failed</div></div></div></div>
                <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="text-muted small">Horizon</div><span class="badge {{ $queueInfo['horizon_installed'] ? 'bg-success' : 'bg-secondary' }}">{{ $queueInfo['horizon_installed'] ? 'Installed' : 'Not installed' }}</span></div></div></div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Actions</div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('dev.queue-inspector.restart') }}">@csrf<button class="btn btn-outline-warning"><i class="bi bi-arrow-repeat me-1"></i>Restart Queue{{ $queueInfo['horizon_installed'] ? ' + Horizon' : '' }}</button></form>
                    <form method="POST" action="{{ route('dev.queue-inspector.retry-failed') }}">@csrf<button class="btn btn-outline-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Retry All Failed</button></form>
                    <form method="POST" action="{{ route('dev.queue-inspector.delete-failed') }}" onsubmit="return confirm('Delete all failed jobs?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete Failed Jobs</button></form>
                </div>
            </div>

            @if ($failedJobs->count())
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Failed Jobs (last 50)</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>ID</th><th>Queue</th><th>Failed At</th><th>Exception</th></tr></thead>
                            <tbody>
                                @foreach ($failedJobs as $job)
                                    <tr>
                                        <td><code class="small">{{ substr($job->uuid ?? $job->id, 0, 8) }}…</code></td>
                                        <td><span class="badge bg-secondary">{{ $job->queue }}</span></td>
                                        <td class="small text-muted">{{ $job->failed_at }}</td>
                                        <td class="text-danger small">{{ Str::limit($job->exception, 120) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($pendingJobs->count())
                <div class="card">
                    <div class="card-header fw-semibold">Pending Jobs (next 20)</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Queue</th><th>Attempts</th><th>Available At</th><th>Payload</th></tr></thead>
                            <tbody>
                                @foreach ($pendingJobs as $job)
                                    @php $payload = json_decode($job->payload, true); @endphp
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $job->queue }}</span></td>
                                        <td>{{ $job->attempts }}</td>
                                        <td class="small text-muted">{{ \Carbon\Carbon::createFromTimestamp($job->available_at)->diffForHumans() }}</td>
                                        <td class="small">{{ $payload['displayName'] ?? Str::limit($job->payload, 60) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
