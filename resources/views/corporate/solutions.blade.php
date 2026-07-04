@extends('layouts.corporate')

@section('title', __('corporate.solutions_meta_title'))
@section('description', __('corporate.solutions_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.solutions_eyebrow') }}</span>
        <h1>{{ __('corporate.solutions_h1') }}</h1>
        <p>{{ __('corporate.solutions_sub2') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        @foreach(trans('corporate.solutions_programme_list') as $i => $sol)
        <div class="row align-items-center g-5 mb-5 {{ $i % 2 === 1 ? 'flex-row-reverse' : '' }}">
            <div class="col-lg-6">
                <div class="corp-feature-icon {{ $sol[2] ? 'corp-feature-icon-pink' : '' }}" style="width:60px;height:60px;font-size:1.625rem;">
                    <i class="bi {{ $sol[1] }}"></i>
                </div>
                <h2 class="section-heading mt-3">{{ $sol[0] }}</h2>
                <p class="section-sub mb-4">{{ $sol[3] }}</p>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    @foreach($sol[4] as $point)
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill" style="color:#FF1585;font-size:0.875rem;"></i>
                        <span style="font-size:0.9rem;color:#334155;">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="mt-4">
                    <a href="{{ route('register') }}" class="btn btn-pink">{{ __('corporate.cta_start_trial') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height:280px;border:1px solid rgba(26,46,90,0.08);">
                    <div class="text-center text-muted">
                        <i class="bi {{ $sol[1] }}" style="font-size:4rem;color:{{ $sol[2] ? '#FF1585' : '#1A2E5A' }};opacity:0.25;"></i>
                    </div>
                </div>
            </div>
        </div>
        @if($i < 3)<hr class="my-5" style="border-color:rgba(26,46,90,0.06);">@endif
        @endforeach
    </div>
</section>

<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">{{ __('corporate.solutions_cta_h2') }}</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">{{ __('corporate.solutions_cta_sub') }}</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('corporate.demo') }}" class="btn btn-pink btn-pink-lg">{{ __('corporate.solutions_cta_demo_btn') }}</a>
            <a href="{{ route('register') }}" class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.35);color:#fff;">{{ __('corporate.cta_start_trial') }}</a>
        </div>
    </div>
</section>

@endsection
