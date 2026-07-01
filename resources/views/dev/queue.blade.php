<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Queue</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-stack me-2 text-warning"></i>Queue</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 fw-bold text-warning">{{ $stats['pending'] }}</div>
                            <div class="text-muted small">Pending Jobs</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 fw-bold text-danger">{{ $stats['failed'] }}</div>
                            <div class="text-muted small">Failed Jobs</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 fw-bold">{{ $stats['total'] }}</div>
                            <div class="text-muted small">Total</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Actions</div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('dev.queue.retry-failed') }}">@csrf<button class="btn btn-outline-success"><i class="bi bi-arrow-repeat me-1"></i>Retry All Failed</button></form>
                    <form method="POST" action="{{ route('dev.queue.delete-failed') }}" onsubmit="return confirm('Delete all failed jobs?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete Failed Jobs</button></form>
                    <form method="POST" action="{{ route('dev.queue.restart') }}">@csrf<button class="btn btn-outline-warning"><i class="bi bi-power me-1"></i>Restart Queue</button></form>
                </div>
            </div>

            @if ($failedJobs->count())
                <div class="card">
                    <div class="card-header fw-semibold">Recent Failed Jobs</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>ID</th><th>Queue</th><th>Failed At</th><th>Exception</th></tr></thead>
                            <tbody>
                                @foreach ($failedJobs as $job)
                                    <tr>
                                        <td><code>{{ $job->id }}</code></td>
                                        <td>{{ $job->queue }}</td>
                                        <td>{{ $job->failed_at }}</td>
                                        <td class="text-danger small">{{ Str::limit($job->exception, 100) }}</td>
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
