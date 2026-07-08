<x-app-layout>
    <x-slot name="title">{{ __('identity.consent_title') }} – {{ config('app.name') }}</x-slot>
    <x-slot name="pageTitle">{{ __('identity.consent_title') }}</x-slot>

    {{-- Customer-facing consent screen shown on the merchant device (ADR-010).
         Only masked identity hints appear until the customer approves. --}}
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-shield-check me-2 text-primary"></i>{{ __('identity.consent_heading', ['merchant' => $merchant->displayName()]) }}
                </div>
                <div class="card-body">

                    <div class="identity-consent-preview rounded p-3 mb-3">
                        <div class="fw-semibold">{{ $customer->onemember_id }}</div>
                        <div class="text-muted small">{{ __('identity.consent_phone_hint', ['phone' => $customer->maskedPhone()]) }}</div>
                    </div>

                    <p class="small">{{ __('identity.consent_body', ['merchant' => $merchant->displayName()]) }}</p>

                    <form method="POST" action="{{ route('members.identity.join') }}">
                        @csrf
                        <input type="hidden" name="customer_uuid" value="{{ $customer->public_uuid }}">

                        @foreach ($fields as $field)
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="fields[]"
                                       value="{{ $field }}"
                                       id="field-{{ $field }}"
                                       {{ $field === 'name' ? 'checked onclick="return false;"' : 'checked' }}>
                                <label class="form-check-label" for="field-{{ $field }}">
                                    {{ __('identity.field_' . $field) }}
                                    @if ($field === 'name')
                                        <span class="text-muted small">({{ __('identity.field_required') }})</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach

                        <p class="text-muted mt-3" style="font-size:0.75rem;">{{ __('identity.consent_footnote') }}</p>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i>{{ __('identity.consent_approve') }}
                            </button>
                            <a href="{{ route('members.identity.add') }}" class="btn btn-outline-secondary">
                                {{ __('identity.consent_decline') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
