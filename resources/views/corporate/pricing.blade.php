@extends('layouts.corporate')

@section('title', __('corporate.pricing_meta_title'))
@section('description', __('corporate.pricing_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.pricing_eyebrow') }}</span>
        <h1>{{ __('corporate.pricing_h1') }}</h1>
        <p>{{ __('corporate.pricing_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center gap-2 bg-light rounded-pill px-3 py-2">
                <i class="bi bi-gift-fill text-pink"></i>
                <span class="small fw-semibold">{!! __('corporate.pricing_trial_note') !!}</span>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Free --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card">
                    <div class="corp-pricing-plan">{{ __('corporate.pricing_free_name') }}</div>
                    <div class="corp-pricing-price">{{ __('corporate.pricing_free_price') }} <span>{{ __('corporate.pricing_per_month') }}</span></div>
                    <p class="corp-pricing-desc">{{ __('corporate.pricing_free_desc') }}</p>
                    <ul class="corp-pricing-features">
                        @foreach(trans('corporate.pricing_free_features') as $f)
                        <li><i class="bi bi-check-circle-fill"></i> {{ $f }}</li>
                        @endforeach
                        @foreach(trans('corporate.pricing_free_missing') as $f)
                        <li class="na"><i class="bi bi-dash"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $appUrl }}/register" class="btn btn-outline-navy w-100">{{ __('corporate.pricing_get_started') }}</a>
                </div>
            </div>

            {{-- Starter --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card">
                    <div class="corp-pricing-plan">{{ __('corporate.pricing_starter_name') }}</div>
                    <div class="corp-pricing-price">{{ __('corporate.pricing_starter_price') }} <span style="font-size:0.875rem;">{{ __('corporate.pricing_per_month') }}</span></div>
                    <p class="corp-pricing-desc">{{ __('corporate.pricing_starter_desc') }}</p>
                    <ul class="corp-pricing-features">
                        @foreach(trans('corporate.pricing_starter_features') as $f)
                        <li><i class="bi bi-check-circle-fill"></i> {{ $f }}</li>
                        @endforeach
                        @foreach(trans('corporate.pricing_starter_missing') as $f)
                        <li class="na"><i class="bi bi-dash"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('corporate.demo') }}" class="btn btn-outline-navy w-100">{{ __('corporate.pricing_contact_sales') }}</a>
                </div>
            </div>

            {{-- Professional --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card featured">
                    <div class="corp-pricing-badge">{{ __('corporate.pricing_pro_badge') }}</div>
                    <div class="corp-pricing-plan">{{ __('corporate.pricing_pro_name') }}</div>
                    <div class="corp-pricing-price">{{ __('corporate.pricing_pro_price') }} <span style="font-size:0.875rem;">{{ __('corporate.pricing_per_month') }}</span></div>
                    <p class="corp-pricing-desc">{{ __('corporate.pricing_pro_desc') }}</p>
                    <ul class="corp-pricing-features">
                        @foreach(trans('corporate.pricing_pro_features') as $f)
                        <li><i class="bi bi-check-circle-fill"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $appUrl }}/register" class="btn btn-pink w-100">{{ __('corporate.cta_start_trial') }}</a>
                </div>
            </div>

            {{-- Enterprise --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card">
                    <div class="corp-pricing-plan">{{ __('corporate.pricing_enterprise_name') }}</div>
                    <div class="corp-pricing-price" style="font-size:1.75rem;">{{ __('corporate.pricing_enterprise_price') }}</div>
                    <p class="corp-pricing-desc">{{ __('corporate.pricing_enterprise_desc') }}</p>
                    <ul class="corp-pricing-features">
                        @foreach(trans('corporate.pricing_enterprise_features') as $f)
                        <li><i class="bi bi-check-circle-fill"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('corporate.contact') }}" class="btn btn-outline-navy w-100">{{ __('corporate.pricing_contact_sales') }}</a>
                </div>
            </div>
        </div>

        <p class="text-center text-muted small mt-4">{{ __('corporate.pricing_note') }} <a href="{{ route('corporate.contact') }}">{{ __('corporate.pricing_note_contact') }}</a> {{ __('corporate.pricing_note_suffix') }}</p>
    </div>
</section>

{{-- FAQ --}}
<section class="corp-section corp-section-alt">
    <div class="container" style="max-width:720px;">
        <div class="text-center mb-5">
            <span class="section-eyebrow">{{ __('corporate.pricing_faq_heading') }}</span>
            <h2 class="section-heading">{{ __('corporate.pricing_faq_sub') }}</h2>
        </div>
        <div class="accordion corp-faq" id="pricingFaq">
            @foreach(trans('corporate.pricing_faq_items') as $i => $qa)
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse"
                            data-bs-target="#pFaq{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="pFaq{{ $i }}">
                        {{ $qa[0] }}
                    </button>
                </h3>
                <div id="pFaq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#pricingFaq">
                    <div class="accordion-body">{{ $qa[1] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">{{ __('corporate.pricing_trial_cta_h2') }}</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">{{ __('corporate.pricing_trial_cta_sub') }}</p>
        <a href="{{ $appUrl }}/register" class="btn btn-pink btn-pink-lg">{{ __('corporate.pricing_trial_cta_btn') }} <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</section>

@endsection
