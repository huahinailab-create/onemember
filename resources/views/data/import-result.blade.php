<x-app-layout>
    <x-slot name="title">{{ __('data.import_wizard_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('data.import_wizard_title') }}</x-slot>

    <div class="text-center py-5">
        @if ($queued)
            <div class="mb-3">
                <span class="display-1 text-primary"><i class="bi bi-clock-history"></i></span>
            </div>
            <h2 class="fw-bold mb-2">{{ __('data.import_result_queued_title') }}</h2>
            <p class="text-muted mb-4">{{ __('data.import_result_queued_msg', ['count' => $rowsCount]) }}</p>
        @else
            <div class="mb-3">
                <span class="display-1 text-success"><i class="bi bi-check-circle-fill"></i></span>
            </div>
            <h2 class="fw-bold mb-2">{{ __('data.import_result_title') }}</h2>
            <p class="text-muted mb-4">{{ __('data.import_result_done') }}</p>

            <div class="row justify-content-center g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-success-subtle text-center py-3">
                        <div class="h3 fw-bold text-success mb-0">{{ $result['imported'] }}</div>
                        <div class="small text-muted">{{ __('data.import_result_imported') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-warning-subtle text-center py-3">
                        <div class="h3 fw-bold text-warning mb-0">{{ $validation['duplicates'] }}</div>
                        <div class="small text-muted">{{ __('data.import_result_duplicates') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-danger-subtle text-center py-3">
                        <div class="h3 fw-bold text-danger mb-0">{{ $result['failed'] }}</div>
                        <div class="small text-muted">{{ __('data.import_result_failed') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 bg-light text-center py-3">
                        <div class="h3 fw-bold text-muted mb-0">{{ $result['time_ms'] }}ms</div>
                        <div class="small text-muted">{{ __('data.import_result_time') }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('members') }}" class="btn btn-primary">
                <i class="bi bi-people me-1"></i>{{ __('data.import_result_view_members') }}
            </a>
            <a href="{{ route('data.import.form') }}" class="btn btn-outline-primary">
                <i class="bi bi-cloud-upload me-1"></i>{{ __('data.import_result_import_more') }}
            </a>
            <a href="{{ route('settings') }}?tab=data" class="btn btn-outline-secondary">
                {{ __('data.import_result_back_settings') }}
            </a>
        </div>
    </div>
</x-app-layout>
