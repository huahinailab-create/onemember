{{-- BILLING-001 — the versioned merchant agreement summary shown at the point
     of acceptance (onboarding) and on the public terms page. All wording is
     DRAFT PENDING LEGAL REVIEW (DR-33). Version comes from config so it always
     matches what terms_acceptances records. --}}
<div class="legal-agreement">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h6 class="fw-semibold mb-0">{{ __('legal.agreement_title') }}</h6>
        <span class="badge bg-warning text-dark">{{ __('legal.draft_badge') }}</span>
    </div>
    <p class="text-muted small mb-2">{{ __('legal.version_label') }}: {{ config('countries.terms_version') }}</p>
    <ul class="small text-muted mb-0 ps-3">
        <li class="mb-1">{{ __('legal.clause_subscription') }}</li>
        <li class="mb-1">{{ __('legal.clause_trial') }}</li>
        <li class="mb-1">{{ __('legal.clause_free_plan') }}</li>
        <li class="mb-1">{{ __('legal.clause_upgrade') }}</li>
        <li class="mb-1">{{ __('legal.clause_responsibility') }}</li>
        <li class="mb-1">{{ __('legal.clause_not_merchant_of_record') }}</li>
        <li class="mb-1">{{ __('legal.clause_no_funds') }}</li>
        <li class="mb-1">{{ __('legal.clause_acceptable_use') }}</li>
        <li>{{ __('legal.clause_privacy') }}</li>
    </ul>
    <p class="text-muted mt-2 mb-0" style="font-size:0.72rem;"><em>{{ __('legal.draft_note') }}</em></p>
</div>
