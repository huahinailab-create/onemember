@extends('layouts.corporate')

@section('title', __('corporate.pdpa_meta_title'))
@section('description', __('corporate.pdpa_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.pdpa_eyebrow') }}</span>
        <h1>{{ __('corporate.pdpa_h1') }}</h1>
        <p>{{ __('corporate.pdpa_last_updated') }} {{ date('d F Y') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">

        <div class="alert mb-5" style="background:rgba(26,46,90,0.06);border:1px solid rgba(26,46,90,0.15);border-radius:12px;">
            <div class="d-flex gap-3 align-items-start">
                <i class="bi bi-shield-fill-check mt-1" style="color:#FF1585;font-size:1.25rem;flex-shrink:0;"></i>
                <p class="mb-0 small text-muted">{{ __('corporate.pdpa_intro') }}</p>
            </div>
        </div>

        <div class="d-flex flex-column gap-5">
            @foreach(trans('corporate.pdpa_full_sections') as $section)
            <div>
                <h2 class="fw-bold mb-3" style="color:#1A1A2E;font-size:1.25rem;">{{ $section[0] }}</h2>
                @foreach($section[1] as $para)
                <p class="text-muted">{{ $para }}</p>
                @endforeach
                @foreach($section[2] as $sub)
                @if($sub[0])<h6 class="fw-semibold mt-3 mb-2" style="color:#1A1A2E;">{{ $sub[0] }}</h6>@endif
                <ul class="text-muted ps-3">
                    @foreach($sub[1] as $point)
                    <li class="mb-1">{{ $point }}</li>
                    @endforeach
                </ul>
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 pt-4 border-top">
            <h3 class="fw-bold mb-3">{{ __('corporate.terms_questions_h2') }}</h3>
            <p class="text-muted mb-4">privacy@onemember.co</p>
            <a href="{{ route('corporate.contact') }}" class="btn btn-pink btn-pink-lg">{{ __('corporate.terms_questions_btn') }}</a>
        </div>
    </div>
</section>

@endsection
