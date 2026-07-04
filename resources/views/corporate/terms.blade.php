@extends('layouts.corporate')

@section('title', __('corporate.terms_meta_title'))
@section('description', __('corporate.terms_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.legal_eyebrow') }}</span>
        <h1>{{ __('corporate.terms_h1') }}</h1>
        <p>{{ __('corporate.terms_last_updated') }} {{ date('d F Y') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        <div class="d-flex flex-column gap-5">
            @foreach(trans('corporate.terms_full_sections') as $section)
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
            <a href="{{ route('corporate.contact') }}" class="btn btn-pink btn-pink-lg">{{ __('corporate.terms_questions_btn') }}</a>
        </div>
    </div>
</section>

@endsection
