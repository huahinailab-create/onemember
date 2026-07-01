<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Storage</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-hdd me-2 text-warning"></i>Storage</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="card mb-3">
                <div class="card-header fw-semibold">Laravel Log</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Size: <strong>{{ number_format($logSize / 1024, 1) }} KB</strong></p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('dev.storage.download-log') }}" class="btn btn-outline-secondary"><i class="bi bi-download me-1"></i>Download laravel.log</a>
                        <form method="POST" action="{{ route('dev.storage.clear-logs') }}" onsubmit="return confirm('Clear log file?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Clear Logs</button></form>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold">Sessions & Temp</div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('dev.storage.clear-sessions') }}" onsubmit="return confirm('Clear all sessions?')">@csrf @method('DELETE')<button class="btn btn-outline-warning"><i class="bi bi-door-open me-1"></i>Clear Sessions</button></form>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Storage Link</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dev.storage.link') }}">@csrf<button class="btn btn-outline-secondary"><i class="bi bi-link me-1"></i>Storage Link</button></form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
