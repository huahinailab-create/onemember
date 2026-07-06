<x-app-layout>
    <x-slot name="title">{{ __('identity.add_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('identity.add_title') }}</x-slot>

    <div class="page-header">
        <h1>{{ __('identity.add_title') }}</h1>
        <p>
            <a href="{{ route('members') }}" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>{{ __('members.back_to_members') }}
            </a>
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-qr-code-scan me-2 text-primary"></i>{{ __('identity.scan_heading') }}
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('identity.scan_hint') }}</p>

                    <form method="POST" action="{{ route('members.identity.resolve') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="qr_payload" class="form-label">{{ __('identity.scan_label') }}</label>
                            <input type="text"
                                   id="qr_payload"
                                   name="qr_payload"
                                   class="form-control form-control-lg @error('qr_payload') is-invalid @enderror"
                                   autocomplete="off"
                                   autofocus
                                   placeholder="OM2:OM-XXXX-XXXX:…">
                            @error('qr_payload')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('identity.scan_privacy_note') }}</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>{{ __('identity.scan_submit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
