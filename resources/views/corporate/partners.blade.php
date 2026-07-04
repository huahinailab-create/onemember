@extends('layouts.corporate')

@section('title', __('corporate.partners_meta_title'))
@section('description', __('corporate.partners_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.partners_eyebrow') }}</span>
        <h1>{{ __('corporate.partners_h1') }}</h1>
        <p>{{ __('corporate.partners_intro_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4 mb-5">
            @foreach(trans('corporate.partners_types_page') as $p)
            <div class="col-md-6">
                <div class="corp-feature-card h-100">
                    <div class="corp-feature-icon {{ $p[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $p[0] }}"></i>
                    </div>
                    <h4>{{ $p[2] }}</h4>
                    <p>{{ $p[3] }}</p>
                    <a href="mailto:partners@onemember.co" class="btn btn-sm btn-outline-navy mt-2">{{ __('corporate.partners_learn_more') }}</a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Partner Benefits --}}
        <div class="corp-contact-card mb-5">
            <h3 class="fw-bold mb-4" style="color:#1A1A2E;">{{ __('corporate.partners_benefits_h2') }}</h3>
            <div class="row g-4">
                @foreach(trans('corporate.partners_benefit_items') as $b)
                <div class="col-md-6">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-check-circle-fill mt-1" style="color:#FF1585;font-size:0.875rem;"></i>
                        <div>
                            <div class="fw-semibold small" style="color:#1A1A2E;">{{ $b[0] }}</div>
                            <div class="text-muted small">{{ $b[1] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center">
            <h3 class="fw-bold mb-3">{{ __('corporate.partners_cta_h2') }}</h3>
            <p class="text-muted mb-4">{{ __('corporate.partners_cta_sub') }}</p>
            <a href="mailto:partners@onemember.co?subject={{ __('corporate.partners_cta_email_sub') }}" class="btn btn-pink btn-pink-lg">{{ __('corporate.partners_cta_btn') }} <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

@endsection
