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
                    <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle text-info me-1"></i>CSV Format Guide</h6>
                    <p class="small text-muted mb-2">Your CSV should include these columns:</p>
                    <table class="table table-sm small">
                        <thead class="table-light">
                            <tr>
                                <th>Column</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>First Name</td><td><span class="badge bg-danger">Required</span></td></tr>
                            <tr><td>Last Name</td><td><span class="badge bg-secondary">Optional</span></td></tr>
                            <tr><td>Phone</td><td><span class="badge bg-danger">Required</span></td></tr>
                            <tr><td>Email</td><td><span class="badge bg-secondary">Optional</span></td></tr>
                            <tr><td>Date of Birth</td><td><span class="badge bg-secondary">Optional</span></td></tr>
                            <tr><td>Notes</td><td><span class="badge bg-secondary">Optional</span></td></tr>
                            <tr><td>Nickname</td><td><span class="badge bg-secondary">Optional</span></td></tr>
                        </tbody>
                    </table>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                        Existing members (matched by phone or email) will <strong>not</strong> be overwritten.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
