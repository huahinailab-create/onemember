@extends('layouts.corporate')

@section('title', __('corporate.features_meta_title'))
@section('description', __('corporate.features_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.features_eyebrow') }}</span>
        <h1>{{ __('corporate.features_h1') }}</h1>
        <p>{{ __('corporate.features_sub2') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4">
            @foreach(trans('corporate.features_page_list') as $feat)
            <div class="col-md-6 col-lg-4">
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
    </div>
</section>

<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">{{ __('corporate.features_all_cta') }}</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">{{ __('corporate.features_cta_sub2') }}</p>
        <a href="{{ route('register') }}" class="btn btn-pink btn-pink-lg">{{ __('corporate.cta_start_trial') }} <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</section>

@endsection
