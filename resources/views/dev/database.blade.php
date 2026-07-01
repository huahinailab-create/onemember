<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Database</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-database-gear me-2 text-warning"></i>Database & Cache</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            @php
            $commands = [
                ['cmd' => 'cache:clear',    'label' => 'Cache Clear',         'icon' => 'bi-trash', 'variant' => 'outline-secondary'],
                ['cmd' => 'optimize',       'label' => 'Optimize',            'icon' => 'bi-lightning', 'variant' => 'outline-secondary'],
                ['cmd' => 'optimize:clear', 'label' => 'Optimize Clear',      'icon' => 'bi-lightning-charge', 'variant' => 'outline-secondary'],
                ['cmd' => 'config:clear',   'label' => 'Config Clear',        'icon' => 'bi-gear', 'variant' => 'outline-secondary'],
                ['cmd' => 'route:clear',    'label' => 'Route Clear',         'icon' => 'bi-signpost', 'variant' => 'outline-secondary'],
                ['cmd' => 'view:clear',     'label' => 'View Clear',          'icon' => 'bi-eye-slash', 'variant' => 'outline-secondary'],
                ['cmd' => 'queue:restart',  'label' => 'Queue Restart',       'icon' => 'bi-arrow-repeat', 'variant' => 'outline-warning'],
                ['cmd' => 'migrate',        'label' => 'Run Migrations',      'icon' => 'bi-database-up', 'variant' => 'outline-primary'],
                ['cmd' => 'migrate:rollback','label' => 'Rollback Last',      'icon' => 'bi-arrow-counterclockwise', 'variant' => 'outline-danger'],
                ['cmd' => 'db:seed',        'label' => 'Seed Database',       'icon' => 'bi-database-add', 'variant' => 'outline-secondary'],
            ];
            @endphp

            <div class="card mb-4">
                <div class="card-header fw-semibold">Artisan Commands</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($commands as $cmd)
                            <form method="POST" action="{{ route('dev.database.command') }}"
                                  @if(in_array($cmd['cmd'], ['migrate:rollback','db:seed','queue:restart']))
                                      onsubmit="return confirm('Run {{ $cmd['label'] }}?')"
                                  @endif>
                                @csrf
                                <input type="hidden" name="command" value="{{ $cmd['cmd'] }}">
                                <button class="btn btn-sm btn-{{ $cmd['variant'] }}">
                                    <i class="bi {{ $cmd['icon'] }} me-1"></i>{{ $cmd['label'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-header fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Fresh Seed (Danger)</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Drops all tables, re-runs migrations, and seeds. <strong>All data will be lost.</strong></p>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#freshSeedModal">
                        <i class="bi bi-nuclear me-1"></i>Fresh Seed
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Fresh Seed Modal --}}
<div class="modal fade" id="freshSeedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Fresh Seed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This will <strong>destroy all data</strong> and re-seed the database. This cannot be undone.</p>
                <p>Type <strong>DELETE</strong> to confirm:</p>
                <form method="POST" action="{{ route('dev.database.fresh-seed') }}" id="freshSeedForm">
                    @csrf
                    <input type="text" name="confirm" class="form-control" placeholder="Type DELETE" required pattern="DELETE">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="freshSeedForm" class="btn btn-danger">Confirm Fresh Seed</button>
            </div>
        </div>
    </div>
</div>
