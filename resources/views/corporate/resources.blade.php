@extends('layouts.corporate')

@section('title', __('corporate.resources_meta_title'))
@section('description', __('corporate.resources_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.resources_eyebrow') }}</span>
        <h1>{{ __('corporate.resources_h1') }}</h1>
        <p>{{ __('corporate.resources_sub2') }}</p>
    </div>
</section>

{{-- Public Knowledge Center entry point (site map §02: /resources/help) —
     the full merchant manual is in-app (App\Models\KnowledgeArticle); this
     is a teaser that sends visitors to sign in/start free rather than
     exposing article content publicly this sprint. --}}
<section class="corp-section pb-0">
    <div class="container">
        <div class="d-flex align-items-center gap-3 p-4 rounded-4" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <i class="bi bi-question-circle-fill" style="color:#1A2E5A;font-size:1.75rem;" aria-hidden="true"></i>
            <div class="flex-grow-1">
                <div class="fw-bold" style="color:#1A1A2E;">{{ __('corporate.resources_kc_title') }}</div>
                <div class="text-muted small">{{ __('corporate.resources_kc_body') }}</div>
            </div>
            <a href="{{ $appUrl }}/register" class="btn btn-sm btn-pink flex-shrink-0">{{ __('corporate.cta_start_trial') }}</a>
        </div>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4">
            @foreach(trans('corporate.resources_items') as $r)
            <div class="col-md-6 col-lg-4">
                <div class="corp-feature-card h-100">
                    <div class="corp-feature-icon {{ $r[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $r[0] }}"></i>
                    </div>
                    <h4>{{ $r[2] }}</h4>
                    <p class="mb-3">{{ $r[3] }}</p>
                    <a href="{{ route('corporate.contact') }}" class="btn btn-sm btn-outline-navy">{{ $r[4] }} <i class="bi bi-download ms-1"></i></a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <p class="text-muted">{{ __('corporate.resources_locked') }} <a href="{{ $appUrl }}/register" style="color:#1A2E5A;font-weight:600;">{{ __('corporate.resources_locked_link') }}</a> {{ __('corporate.resources_locked_suffix') }}</p>
        </div>
    </div>
</section>

@endsection
