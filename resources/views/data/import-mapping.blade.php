<x-app-layout>
    <x-slot name="title">{{ __('data.import_wizard_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('data.import_wizard_title') }}</x-slot>

    <div class="page-header">
        <div>
            <h1>{{ __('data.import_wizard_title') }}</h1>
            <p>{{ __('data.import_step2_desc') }}</p>
        </div>
    </div>

    {{-- Step indicator --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check"></i></span>
        <span class="text-muted text-decoration-line-through">{{ __('data.import_step1_title') }}</span>
        <span class="text-muted mx-1">→</span>
        <span class="badge bg-primary rounded-pill px-3 py-2">2</span>
        <span class="fw-semibold">{{ __('data.import_step2_title') }}</span>
        <span class="text-muted mx-1">→</span>
        <span class="badge bg-light text-muted rounded-pill px-3 py-2">3</span>
        <span class="text-muted">{{ __('data.import_step3_title') }}</span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">{{ __('data.import_step2_title') }}</h5>
                <span class="badge bg-secondary">{{ __('data.import_rows_detected', ['count' => $rowsCount]) }}</span>
            </div>

            <form method="POST" action="{{ route('data.import.preview') }}">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%">{{ __('data.import_col_csv') }}</th>
                                <th>{{ __('data.import_col_field') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($headers as $header)
                                @php
                                    $key        = base64_encode($header);
                                    $detected   = $detectedMapping[$header] ?? '__skip__';
                                @endphp
                                <tr>
                                    <td>
                                        <code class="text-dark">{{ $header }}</code>
                                        @if ($detected !== '__skip__')
                                            <span class="badge bg-info-subtle text-info ms-2 small">auto-detected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="mapping[{{ $key }}]" class="form-select form-select-sm">
                                            <option value="__skip__" @selected($detected === '__skip__')>
                                                {{ __('data.import_field_skip') }}
                                            </option>
                                            @foreach ($availableFields as $field)
                                                <option value="{{ $field }}" @selected($detected === $field)>
                                                    {{ __('data.import_field_' . $field) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i>{{ __('data.import_proceed_preview') }}
                    </button>
                    <a href="{{ route('data.import.form') }}" class="btn btn-outline-secondary">
                        {{ __('data.import_cancel_btn') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
