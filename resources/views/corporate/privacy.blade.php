@extends('layouts.corporate')

@section('title', __('corporate.privacy_meta_title'))
@section('description', __('corporate.privacy_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.legal_eyebrow') }}</span>
        <h1>{{ __('corporate.privacy_h1') }}</h1>
        <p>{{ __('corporate.terms_last_updated') }} {{ date('d F Y') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        <div class="d-flex flex-column gap-5">
            @foreach(trans('corporate.privacy_full_sections') as $section)
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
    </div>
</section>

@endsection
