@extends('layouts.corporate')

@section('title', __('corporate.home_meta_title'))
@section('description', __('corporate.home_meta_desc'))
@section('og_title', __('corporate.home_meta_title'))
@section('og_description', __('corporate.home_meta_desc'))

@section('content')

{{-- Hero --}}
<section class="corp-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="corp-hero-eyebrow">
                    <i class="bi bi-star-fill"></i> {{ __('corporate.home_eyebrow') }}
                </div>
                <h1>{{ __('corporate.home_hero_h1') }} <span class="hero-accent">{{ __('corporate.home_hero_accent') }}</span></h1>
                <p class="corp-hero-sub">{{ __('corporate.home_hero_sub') }}</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ $appUrl }}/register" class="btn btn-pink btn-pink-lg">{{ __('corporate.cta_start_trial') }} <i class="bi bi-arrow-right ms-1"></i></a>
                    @if (config('services.line.oa_url'))
                        <a href="{{ config('services.line.oa_url') }}" target="_blank" rel="noopener"
                           class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.4);color:#fff;">
                            <i class="bi bi-chat-dots-fill me-1" aria-hidden="true"></i>{{ __('corporate.contact_line_cta') }}
                        </a>
                    @else
                        <a href="{{ route('corporate.demo') }}" class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.4);color:#fff;">{{ __('corporate.nav_book_demo') }}</a>
                    @endif
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-number">{{ __('corporate.home_stat_trial') }}</div>
                    </div>
                    <div>
                        <div class="hero-stat-number">{{ __('corporate.home_stat_setup_value') }}</div>
                        <div class="hero-stat-label">{{ __('corporate.home_stat_setup_label') }}</div>
                    </div>
                    <div>
                        <div class="hero-stat-number">{{ __('corporate.home_stat_pdpa_value') }}</div>
                        <div class="hero-stat-label">{{ __('corporate.home_stat_pdpa_label') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                {{-- Decorative product-UI illustration. aria-hidden: the sample
                     numbers are a small shop's day view, not claims — screen
                     readers should not announce them as content. Trust rule
                     (§8): modest small-shop figures, no percentage that could
                     read as a marketing statistic. --}}
                <div class="hero-mockup" aria-hidden="true">
                    <div class="hero-mockup-screen">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:8px;height:8px;border-radius:50%;background:#FF1585;"></div>
                            <div style="font-size:0.75rem;color:#64748b;font-weight:600;">{{ __('corporate.home_mockup_title') }}</div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div style="background:#f8f9ff;border-radius:8px;padding:0.875rem;border-left:3px solid #1A2E5A;">
                                    <div style="font-size:1.5rem;font-weight:800;color:#1A1A2E;">128</div>
                                    <div style="font-size:0.75rem;color:#64748b;">{{ __('corporate.home_mockup_active') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:#fff0f7;border-radius:8px;padding:0.875rem;border-left:3px solid #FF1585;">
                                    <div style="font-size:1.5rem;font-weight:800;color:#1A1A2E;">12</div>
                                    <div style="font-size:0.75rem;color:#64748b;">{{ __('corporate.home_mockup_retention') }}</div>
                                </div>
                            </div>
                        </div>
                        <div style="background:#f8f9fb;border-radius:8px;padding:0.875rem;">
                            <div style="font-size:0.75rem;font-weight:600;color:#1A2E5A;margin-bottom:0.5rem;">{{ __('corporate.home_mockup_activity') }}</div>
                            @foreach([['Chelsea P.', '+50 pts', '2m'], ['Mia K.', '🎂', '15m'], ['Alex T.', '+30 pts', '1h']] as $row)
                            <div class="d-flex justify-content-between align-items-center py-1" style="font-size:0.8rem;border-bottom:1px solid rgba(26,46,90,0.05);">
                                <span style="color:#334155;">{{ $row[0] }}</span>
                                <span class="text-pink" style="font-weight:600;">{{ $row[1] }}</span>
                                <span style="color:#94a3b8;">{{ $row[2] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Problem Section --}}
<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <span class="section-eyebrow">{{ __('corporate.home_problem_eyebrow') }}</span>
        <h2 class="section-heading mx-auto" style="max-width:600px;">{{ __('corporate.home_problem_h2') }}</h2>
        <p class="section-sub mx-auto mb-5">{{ __('corporate.home_problem_sub') }}</p>
        <div class="row g-4">
            @foreach(trans('corporate.home_problems') as $item)
            <div class="col-md-4">
                <div class="corp-feature-card text-center">
                    <div class="corp-feature-icon corp-feature-icon-pink mx-auto">
                        <i class="bi {{ $item['icon'] }}"></i>
                    </div>
                    <h4>{{ $item['title'] }}</h4>
                    <p>{{ $item['body'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="corp-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="section-eyebrow">{{ __('corporate.home_how_eyebrow') }}</span>
                <h2 class="section-heading">{{ __('corporate.home_how_h2') }}</h2>
                <p class="section-sub mb-4">{{ __('corporate.home_how_sub') }}</p>
                <div class="d-flex flex-column gap-4">
                    @foreach(trans('corporate.home_steps') as $step)
                    <div class="corp-step">
                        <div class="corp-step-number">{{ $step['num'] }}</div>
                        <div>
                            <h5>{{ $step['title'] }}</h5>
                            <p>{{ $step['body'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="bg-light rounded-4 p-4" style="min-height:380px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(26,46,90,0.08);">
                    <div class="text-center text-muted">
                        <i class="bi bi-phone" style="font-size:4rem;color:#1A2E5A;opacity:0.3;"></i>
                        <p class="mt-2 small">{{ __('corporate.home_platform_screenshot') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Industries --}}
<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <span class="section-eyebrow">{{ __('corporate.home_industries_eyebrow') }}</span>
        <h2 class="section-heading">{{ __('corporate.home_industries_h2') }}</h2>
        <p class="section-sub mx-auto mb-5">{{ __('corporate.home_industries_sub') }}</p>
        <div class="row g-3">
            @foreach(trans('corporate.home_industries') as $ind)
            <div class="col-md-4 col-6">
                <div class="corp-industry-card">
                    <div class="corp-industry-icon"><i class="bi {{ $ind[0] }}"></i></div>
                    <h4>{{ $ind[1] }}</h4>
                    <p>{{ $ind[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('corporate.industries') }}" class="btn btn-outline-navy">{{ __('corporate.home_see_all_industries') }} <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="corp-section">
    <div class="container text-center">
        <span class="section-eyebrow">{{ __('corporate.home_features_eyebrow') }}</span>
        <h2 class="section-heading">{{ __('corporate.home_features_h2') }}</h2>
        <p class="section-sub mx-auto mb-5">{{ __('corporate.home_features_sub') }}</p>
        <div class="row g-4 text-start">
            @foreach(trans('corporate.home_feature_cards') as $feat)
            <div class="col-md-4 col-6">
                <div class="corp-feature-card">
                    <div class="corp-feature-icon {{ $feat[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $feat[0] }}"></i>
                    </div>
                    <h4>{{ $feat[2] }}</h4>
                    <p>{{ $feat[3] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('corporate.features') }}" class="btn btn-outline-navy">{{ __('corporate.home_see_all_features') }} <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- Testimonials — ships hidden until >=1 real Founding Merchant quote
     exists in corporate.home_testimonials (docs/OMOS/Website/03-Home-Page.md
     §8: "no fake quotes, ever"). --}}
{{-- is_array(), not empty(): Laravel's translator falls back to returning
     the key string itself when a translation value is an empty array, so
     !empty() on that string would incorrectly evaluate truthy. --}}
@if (is_array(trans('corporate.home_testimonials')) && count(trans('corporate.home_testimonials')))
<section class="corp-section corp-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">{{ __('corporate.home_testimonials_eyebrow') }}</span>
            <h2 class="section-heading">{{ __('corporate.home_testimonials_h2') }}</h2>
        </div>
        <div class="row g-4">
            @foreach(trans('corporate.home_testimonials') as $t)
            <div class="col-md-4">
                <div class="corp-testimonial">
                    <div class="mb-2">
                        @for($i = 0; $i < 5; $i++) <i class="bi bi-star-fill text-warning small"></i> @endfor
                    </div>
                    <p class="corp-testimonial-quote">{{ $t[0] }}</p>
                    <div class="corp-testimonial-author">{{ $t[1] }}</div>
                    <div class="corp-testimonial-role">{{ $t[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Pilot Programme --}}
<section class="corp-section">
    <div class="container">
        <div class="corp-pilot-banner">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="hero-stat-number mb-1" style="font-size:0.8rem;color:rgba(255,255,255,0.7);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;">{{ __('corporate.home_pilot_label') }}</div>
                    <h2 style="font-size:2rem;font-weight:800;margin-bottom:0.75rem;">{{ __('corporate.home_pilot_h2') }}</h2>
                    <p style="color:rgba(255,255,255,0.82);margin:0;">{{ __('corporate.home_pilot_sub') }}</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ $appUrl }}/register" class="btn btn-lg" style="background:#ffffff;color:#FF1585;font-weight:700;border-radius:10px;padding:0.875rem 2rem;">
                        {{ __('corporate.home_pilot_join_btn') }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Pricing Preview --}}
<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <span class="section-eyebrow">{{ __('corporate.home_pricing_eyebrow') }}</span>
        <h2 class="section-heading">{{ __('corporate.home_pricing_h2') }}</h2>
        <p class="section-sub mx-auto mb-2">{{ __('corporate.home_pricing_sub') }}</p>
        <p class="small text-muted mb-5">{{ __('corporate.home_pricing_sub2') }} <a href="{{ route('corporate.pricing') }}">{{ __('corporate.home_pricing_view_full') }}</a>.</p>
        <div class="row justify-content-center g-4">
            @foreach(trans('corporate.home_pricing_plans') as $p)
            <div class="col-md-6 col-lg-4">
                <div class="corp-pricing-card {{ $p[5] ? 'featured' : '' }}">
                    @if($p[5]) <div class="corp-pricing-badge">{{ __('corporate.pricing_pro_badge') }}</div> @endif
                    <div class="corp-pricing-plan">{{ $p[0] }}</div>
                    <p class="small text-muted mb-3">{{ $p[1] }}</p>
                    <ul class="corp-pricing-features mb-4">
                        @foreach(array_slice($p, 2, 3) as $f)
                        <li><i class="bi bi-check-circle-fill"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $appUrl }}/register" class="btn w-100 {{ $p[5] ? 'btn-pink' : 'btn-outline-navy' }}">
                        {{ $p[5] ? __('corporate.cta_start_trial') : __('corporate.cta_get_started') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('corporate.pricing') }}" class="btn btn-outline-navy">{{ __('corporate.home_see_full_pricing') }} <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- FAQ Preview --}}
<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4">
                <span class="section-eyebrow">{{ __('corporate.home_faq_eyebrow') }}</span>
                <h2 class="section-heading">{{ __('corporate.home_faq_h2') }}</h2>
                <a href="{{ route('corporate.faq') }}" class="btn btn-outline-navy mt-3">{{ __('corporate.home_see_all_faqs') }}</a>
            </div>
            <div class="col-lg-8">
                <div class="accordion corp-faq" id="homeFaq">
                    @foreach(trans('corporate.home_faq_items') as $i => $qa)
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#homeFaq{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="homeFaq{{ $i }}">
                                {{ $qa[0] }}
                            </button>
                        </h3>
                        <div id="homeFaq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#homeFaq">
                            <div class="accordion-body">{{ $qa[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">{{ __('corporate.home_cta_h2') }}</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">{{ __('corporate.home_cta_sub') }}</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ $appUrl }}/register" class="btn btn-pink btn-pink-lg">{{ __('corporate.cta_start_trial') }} <i class="bi bi-arrow-right ms-1"></i></a>
            <a href="{{ config('services.line.oa_url') ?: route('corporate.contact') }}"
               @if (config('services.line.oa_url')) target="_blank" rel="noopener" @endif
               class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.35);color:#fff;">{{ __('corporate.home_cta_contact') }}</a>
        </div>
    </div>
</section>

@endsection
