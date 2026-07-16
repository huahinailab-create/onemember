@extends('layouts.corporate')

@section('title', __('corporate.about_meta_title'))
@section('description', __('corporate.about_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.about_eyebrow') }}</span>
        <h1>{{ __('corporate.about_h1') }}</h1>
        <p>{{ __('corporate.about_sub') }}</p>
    </div>
</section>

{{-- Mission & Vision --}}
<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="section-eyebrow">{{ __('corporate.about_mission_eyebrow') }}</span>
                <h2 class="section-heading">{{ __('corporate.about_mission_h2') }}</h2>
                <p class="section-sub mb-4">{{ __('corporate.about_mission_p1') }}</p>
                <p class="text-muted">{{ __('corporate.about_mission_p2') }}</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @foreach(trans('corporate.about_values') as $value)
                    <div class="col-6">
                        <div class="corp-feature-card text-center">
                            <div class="corp-feature-icon mx-auto"><i class="bi {{ $value['icon'] }}"></i></div>
                            <h4>{{ $value['title'] }}</h4>
                            <p>{{ $value['body'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Story --}}
<section class="corp-section corp-section-alt">
    <div class="container" style="max-width:800px;">
        <div class="text-center mb-5">
            <span class="section-eyebrow">{{ __('corporate.about_story_eyebrow') }}</span>
            <h2 class="section-heading">{{ __('corporate.about_story_h2') }}</h2>
        </div>
        <div class="d-flex flex-column gap-4">
            <p class="fs-5" style="color:#334155;line-height:1.8;">{{ __('corporate.about_story_p1') }}</p>
            <p style="color:#64748b;line-height:1.8;">{{ __('corporate.about_story_p2') }}</p>
            <p style="color:#64748b;line-height:1.8;">{{ __('corporate.about_story_p3') }}</p>
            <p style="color:#64748b;line-height:1.8;">{{ __('corporate.about_story_p4') }}</p>
        </div>
    </div>
</section>

{{-- ASEAN Expansion --}}
<section class="corp-section corp-section-navy">
    <div class="container text-center">
        <span class="section-eyebrow text-pink">{{ __('corporate.about_asean_eyebrow') }}</span>
        <h2 class="section-heading section-heading-light">{{ __('corporate.about_asean_h2') }}</h2>
        <p class="section-sub section-sub-light mx-auto mb-5">{{ __('corporate.about_asean_sub') }}</p>
        <div class="row g-4 justify-content-center">
            @foreach(trans('corporate.about_countries') as $c)
            <div class="col-md-3 col-6">
                <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:1.5rem;text-align:center;">
                    <div style="font-size:2.5rem;margin-bottom:0.5rem;">{{ $c['flag'] }}</div>
                    <div style="font-weight:700;color:#fff;margin-bottom:0.25rem;">{{ $c['name'] }}</div>
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);">{{ $c['status'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="corp-section">
    <div class="container text-center">
        <h2 class="section-heading">{{ __('corporate.about_cta_h2') }}</h2>
        <p class="section-sub mx-auto mb-4">{{ __('corporate.about_cta_sub') }}</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ $appUrl }}/register" class="btn btn-pink btn-pink-lg">{{ __('corporate.cta_start_trial') }}</a>
            <a href="{{ route('corporate.careers') }}" class="btn btn-outline-navy btn-outline-navy-lg">{{ __('corporate.cta_view_careers') }}</a>
            <a href="{{ route('corporate.contact') }}" class="btn btn-outline-navy btn-outline-navy-lg">{{ __('corporate.cta_contact_us') }}</a>
        </div>
    </div>
</section>

@endsection
