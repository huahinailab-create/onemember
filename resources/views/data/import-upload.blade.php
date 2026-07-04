<x-app-layout>
    <x-slot name="title">{{ __('data.import_wizard_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('data.import_wizard_title') }}</x-slot>

    <div class="page-header">
        <div>
            <h1>{{ __('data.import_wizard_title') }}</h1>
            <p>{{ __('data.import_step1_desc') }}</p>
        </div>
        <div>
            <a href="{{ route('settings') }}?tab=data" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('data.import_back_settings') }}
            </a>
        </div>
    </div>

    {{-- Step indicator --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <span class="badge bg-primary rounded-pill px-3 py-2">1</span>
        <span class="fw-semibold">{{ __('data.import_step1_title') }}</span>
        <span class="text-muted mx-1">→</span>
        <span class="badge bg-light text-muted rounded-pill px-3 py-2">2</span>
        <span class="text-muted">{{ __('data.import_step2_title') }}</span>
        <span class="text-muted mx-1">→</span>
        <span class="badge bg-light text-muted rounded-pill px-3 py-2">3</span>
        <span class="text-muted">{{ __('data.import_step3_title') }}</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">{{ __('data.import_step1_title') }}</h5>

                    <form method="POST" action="{{ route('data.import.upload') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="csv_file" class="form-label fw-medium">
                                {{ __('data.import_upload_label') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv"
                                   class="form-control @error('csv_file') is-invalid @enderror">
                            <div class="form-text">{{ __('data.import_upload_hint') }}</div>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-upload me-1"></i>{{ __('data.import_upload_btn') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-info-subtle">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle text-info me-1"></i>{{ __('data.import_csv_format_guide') }}</h6>
                    <p class="small text-muted mb-2">{{ __('data.import_csv_columns_hint') }}</p>
                    <table class="table table-sm small">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('data.import_csv_col_column') }}</th>
                                <th>{{ __('data.import_csv_col_req_header') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>{{ __('data.import_csv_col_first_name') }}</td><td><span class="badge bg-danger">{{ __('data.import_csv_badge_required') }}</span></td></tr>
                            <tr><td>{{ __('data.import_csv_col_last_name') }}</td><td><span class="badge bg-secondary">{{ __('data.import_csv_badge_optional') }}</span></td></tr>
                            <tr><td>{{ __('data.import_csv_col_phone') }}</td><td><span class="badge bg-danger">{{ __('data.import_csv_badge_required') }}</span></td></tr>
                            <tr><td>{{ __('data.import_csv_col_email') }}</td><td><span class="badge bg-secondary">{{ __('data.import_csv_badge_optional') }}</span></td></tr>
                            <tr><td>{{ __('data.import_csv_col_dob') }}</td><td><span class="badge bg-secondary">{{ __('data.import_csv_badge_optional') }}</span></td></tr>
                            <tr><td>{{ __('data.import_csv_col_notes') }}</td><td><span class="badge bg-secondary">{{ __('data.import_csv_badge_optional') }}</span></td></tr>
                            <tr><td>{{ __('data.import_csv_col_nickname') }}</td><td><span class="badge bg-secondary">{{ __('data.import_csv_badge_optional') }}</span></td></tr>
                        </tbody>
                    </table>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                        {!! __('data.import_csv_no_overwrite') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
