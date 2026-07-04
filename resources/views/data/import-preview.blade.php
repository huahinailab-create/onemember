<x-app-layout>
    <x-slot name="title">{{ __('data.import_wizard_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('data.import_wizard_title') }}</x-slot>

    <div class="page-header">
        <div>
            <h1>{{ __('data.import_wizard_title') }}</h1>
            <p>{{ __('data.import_step3_desc') }}</p>
        </div>
    </div>

    {{-- Step indicator --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check"></i></span>
        <span class="text-muted text-decoration-line-through">{{ __('data.import_step1_title') }}</span>
        <span class="text-muted mx-1">→</span>
        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check"></i></span>
        <span class="text-muted text-decoration-line-through">{{ __('data.import_step2_title') }}</span>
        <span class="text-muted mx-1">→</span>
        <span class="badge bg-primary rounded-pill px-3 py-2">3</span>
        <span class="fw-semibold">{{ __('data.import_step3_title') }}</span>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                    <div class="h3 fw-bold mb-0">{{ $validation['total'] }}</div>
                    <div class="small text-muted">{{ __('data.import_summary_total') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-success-subtle">
                <div class="card-body py-3">
                    <div class="h3 fw-bold text-success mb-0">{{ $validation['valid'] }}</div>
                    <div class="small text-muted">{{ __('data.import_summary_valid') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-danger-subtle">
                <div class="card-body py-3">
                    <div class="h3 fw-bold text-danger mb-0">{{ $validation['invalid'] }}</div>
                    <div class="small text-muted">{{ __('data.import_summary_invalid') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-warning-subtle">
                <div class="card-body py-3">
                    <div class="h3 fw-bold text-warning mb-0">{{ $validation['duplicates'] }}</div>
                    <div class="small text-muted">{{ __('data.import_summary_duplicates') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if ($shouldQueue)
        <div class="alert alert-info mb-4">
            <i class="bi bi-clock me-2"></i>{{ __('data.import_queue_note') }}
        </div>
    @endif

    @if ($validation['valid'] === 0)
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ __('data.import_no_valid') }}
        </div>
    @endif

    {{-- Preview of valid rows --}}
    @if (! empty($previewRows))
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">{{ __('data.import_preview_title') }}</span>
                <span class="badge bg-secondary">{{ count($previewRows) }} / {{ $validation['valid'] }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('data.import_preview_col_hash') }}</th>
                            <th>{{ __('data.import_preview_col_name') }}</th>
                            <th>{{ __('data.import_preview_col_phone') }}</th>
                            <th>{{ __('data.import_preview_col_email') }}</th>
                            <th>{{ __('data.import_preview_col_birthday') }}</th>
                            <th>{{ __('data.import_preview_col_notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($previewRows as $row)
                            <tr>
                                <td class="text-muted small">{{ $row['row'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['phone'] }}</td>
                                <td>{{ $row['email'] ?? '—' }}</td>
                                <td>{{ $row['birthday'] ?? '—' }}</td>
                                <td class="text-truncate" style="max-width:150px">{{ $row['notes'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Rows with errors --}}
    @if (! empty($validation['errors']))
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-danger">
                    <i class="bi bi-x-circle me-1"></i>{{ __('data.import_errors_title') }}
                </span>
                <span class="badge bg-danger">{{ count($validation['errors']) }}</span>
            </div>
            <div class="table-responsive" style="max-height:250px;overflow-y:auto">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width:60px">{{ __('data.import_errors_col_row') }}</th>
                            <th>{{ __('data.import_errors_col_issue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($validation['errors'] as $err)
                            <tr class="{{ ($err['duplicate'] ?? false) ? 'table-warning' : 'table-danger' }}">
                                <td>{{ $err['row'] }}</td>
                                <td>
                                    @foreach ($err['messages'] as $msg)
                                        <span class="badge {{ ($err['duplicate'] ?? false) ? 'bg-warning text-dark' : 'bg-danger' }} me-1">{{ $msg }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Warnings --}}
    @if (! empty($validation['warnings']))
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <span class="fw-semibold text-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ __('data.import_warnings_title') }}
                </span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($validation['warnings'] as $warn)
                    <li class="list-group-item small text-muted">{{ $warn['message'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Action buttons --}}
    @if ($validation['valid'] > 0)
        <form method="POST" action="{{ route('data.import.execute') }}">
            @csrf
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-1"></i>
                    @if ($shouldQueue)
                        {{ __('data.import_confirm_queued_btn', ['count' => $validation['valid']]) }}
                    @else
                        {{ __('data.import_confirm_btn', ['count' => $validation['valid']]) }}
                    @endif
                </button>
                <a href="{{ route('data.import.form') }}" class="btn btn-outline-secondary">
                    {{ __('data.import_cancel_btn') }}
                </a>
            </div>
        </form>
    @else
        <a href="{{ route('data.import.form') }}" class="btn btn-outline-secondary">
            {{ __('data.import_cancel_btn') }}
        </a>
    @endif
</x-app-layout>
