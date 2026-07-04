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
