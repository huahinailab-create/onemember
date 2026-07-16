@extends('layouts.corporate')

@section('title', __('corporate.security_meta_title'))
@section('description', __('corporate.security_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.security_eyebrow') }}</span>
        <h1>{{ __('corporate.security_h1') }}</h1>
        <p>{{ __('corporate.security_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4 mb-5">
            @foreach(trans('corporate.security_features') as $s)
            <div class="col-md-6 col-lg-4">
                <div class="corp-feature-card">
                    <div class="corp-feature-icon {{ $s[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $s[0] }}"></i>
                    </div>
                    <h4>{{ $s[2] }}</h4>
                    <p>{{ $s[3] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- PDPA Detail --}}
        <div class="corp-contact-card mb-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3 style="font-weight:700;color:#1A1A2E;">{{ __('corporate.security_pdpa_act') }}</h3>
                    <p class="text-muted mb-3">{{ __('corporate.security_pdpa_act_desc') }}</p>
                    <ul class="text-muted" style="line-height:2;">
                        @foreach(trans('corporate.security_pdpa_list') as $point)
                        <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-4 text-center">
                    <div style="width:80px;height:80px;background:rgba(26,46,90,0.08);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="bi bi-shield-check" style="font-size:2rem;color:#1A2E5A;"></i>
                    </div>
                    <p class="small text-muted">{{ __('corporate.security_pdpa_since') }}</p>
                </div>
            </div>
        </div>

        {{-- Responsible Disclosure --}}
        <div class="corp-contact-card">
            <h3 style="font-weight:700;color:#1A1A2E;">{{ __('corporate.security_disclosure_h2') }}</h3>
            <p class="text-muted mb-3">{{ __('corporate.security_disclosure_intro') }}</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="fw-semibold">{{ __('corporate.security_what_label') }}</h6>
                    <ul class="text-muted small">
                        @foreach(trans('corporate.security_what_list') as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold">{{ __('corporate.security_how_label') }}</h6>
                    <p class="text-muted small mb-2">{{ __('corporate.security_how_email') }} <a href="mailto:security@onemember.co" style="color:#1A2E5A;font-weight:600;">security@onemember.co</a></p>
                    <p class="text-muted small">{{ __('corporate.security_how_ack') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <h2 class="section-heading">{{ __('corporate.security_cta_h2') }}</h2>
        <p class="section-sub mx-auto mb-4">{{ __('corporate.security_cta_sub') }}</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="mailto:security@onemember.co" class="btn btn-pink btn-pink-lg">{{ __('corporate.security_cta_btn1') }}</a>
            <a href="mailto:privacy@onemember.co" class="btn btn-outline-navy btn-outline-navy-lg">{{ __('corporate.security_cta_btn2') }}</a>
        </div>
    </div>
</section>

@endsection
