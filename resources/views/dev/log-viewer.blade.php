<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Log Viewer</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-journal-text me-2 text-warning"></i>Log Viewer</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('dev.logs') }}" class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search logs…" value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <select name="level" class="form-select">
                                <option value="">All levels</option>
                                @foreach (['emergency','alert','critical','error','warning','notice','info','debug'] as $lvl)
                                    <option value="{{ $lvl }}" @selected($level === $lvl)>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100"><i class="bi bi-search me-1"></i>Filter</button>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <a href="{{ route('dev.logs.download') }}" class="btn btn-outline-secondary flex-fill" title="Download"><i class="bi bi-download"></i></a>
                            <form method="POST" action="{{ route('dev.logs.clear') }}" onsubmit="return confirm('Clear log file?')">@csrf @method('DELETE')
                                <button class="btn btn-outline-danger" title="Clear"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted small">
                    Log size: {{ number_format($size / 1024, 1) }} KB · Showing last {{ count($lines) }} lines (newest first)
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="log-viewer" style="max-height:65vh;overflow-y:auto;font-family:monospace;font-size:0.75rem;">
                        @forelse ($lines as $line)
                            @php
                                $cls = '';
                                if (str_contains($line, '.ERROR') || str_contains($line, '[error]') || str_contains($line, '.CRITICAL') || str_contains($line, '.EMERGENCY')) $cls = 'text-danger';
                                elseif (str_contains($line, '.WARNING') || str_contains($line, '[warning]')) $cls = 'text-warning';
                                elseif (str_contains($line, '.INFO') || str_contains($line, '[info]')) $cls = 'text-info';
                            @endphp
                            <div class="px-3 py-1 border-bottom {{ $cls }}" style="white-space:pre-wrap;word-break:break-all;">{{ $line }}</div>
                        @empty
                            <div class="p-4 text-muted text-center">
                                @if ($search || $level)
                                    No log lines match your filter.
                                @else
                                    Log file is empty.
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
