@extends('layouts.corporate')

@section('title', __('corporate.industries_meta_title'))
@section('description', __('corporate.industries_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.industries_eyebrow') }}</span>
        <h1>{{ __('corporate.industries_h1') }}</h1>
        <p>{{ __('corporate.industries_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4">
            @foreach(trans('corporate.industries_page_list') as $ind)
            <div class="col-md-6 col-lg-3">
                <div class="corp-industry-card h-100">
                    <div class="corp-industry-icon"><i class="bi {{ $ind[0] }}"></i></div>
                    <h4>{{ $ind[1] }}</h4>
                    <p class="mb-3">{{ $ind[2] }}</p>
                    <ul class="list-unstyled" style="position:relative;z-index:1;">
                        @foreach($ind[3] as $point)
                        <li class="d-flex align-items-center gap-2 mb-1" style="font-size:0.8rem;color:rgba(255,255,255,0.8);">
                            <i class="bi bi-check2 text-pink"></i> {{ $point }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <h2 class="section-heading">{{ __('corporate.industries_other_h2') }}</h2>
        <p class="section-sub mx-auto mb-4">{{ __('corporate.industries_any_business') }}</p>
        <a href="{{ route('corporate.contact') }}" class="btn btn-pink btn-pink-lg">{{ __('corporate.industries_other_btn') }} <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</section>

@endsection
