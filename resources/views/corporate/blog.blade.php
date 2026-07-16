@extends('layouts.corporate')

@section('title', __('corporate.blog_meta_title'))
@section('description', __('corporate.blog_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.blog_eyebrow') }}</span>
        <h1>{{ __('corporate.blog_h1') }}</h1>
        <p>{{ __('corporate.blog_sub2') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4">
            @foreach(trans('corporate.blog_articles') as $post)
            <div class="col-md-6 col-lg-4">
                <div class="corp-feature-card h-100">
                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="height:140px;">
                        <i class="bi {{ $post[3] }}" style="font-size:3rem;color:#1A2E5A;opacity:0.25;"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill" style="background:rgba(26,46,90,0.08);color:#1A2E5A;font-size:0.7rem;font-weight:600;">{{ $post[1] }}</span>
                        <span class="text-muted" style="font-size:0.75rem;">{{ $post[2] }}</span>
                    </div>
                    <h4>{{ $post[0] }}</h4>
                    <p class="text-muted small">{{ __('corporate.blog_coming_soon_card') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 p-4 rounded-3" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <h4 class="fw-bold mb-2">{{ __('corporate.blog_notify_h3') }}</h4>
            <p class="text-muted mb-3">{{ __('corporate.blog_notify_body') }}</p>
            <div class="d-flex gap-2 justify-content-center" style="max-width:400px;margin:0 auto;">
                <input type="email" class="form-control" placeholder="{{ __('corporate.blog_notify_ph') }}">
                <button class="btn btn-pink" style="white-space:nowrap;">{{ __('corporate.cta_subscribe') }}</button>
            </div>
        </div>
    </div>
</section>

@endsection
