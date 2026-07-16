@extends('layouts.corporate')

@section('title', __('corporate.careers_meta_title'))
@section('description', __('corporate.careers_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.careers_eyebrow') }}</span>
        <h1>{{ __('corporate.careers_h1') }}</h1>
        <p>{{ __('corporate.careers_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-center mb-5">
            <div class="col-lg-6">
                <span class="section-eyebrow">{{ __('corporate.careers_why_h2') }}</span>
                <h2 class="section-heading">{{ __('corporate.careers_why_sub') }}</h2>
                <p class="section-sub mb-4">{{ __('corporate.careers_why_body') }}</p>
                <div class="d-flex flex-column gap-3">
                    @foreach(trans('corporate.careers_why_points') as $v)
                    <div class="d-flex gap-3 align-items-start">
                        <div style="width:36px;height:36px;border-radius:8px;background:rgba(26,46,90,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $v[0] }}" style="color:#1A2E5A;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">{{ $v[1] }}</div>
                            <div class="text-muted small">{{ $v[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height:280px;border:1px solid rgba(26,46,90,0.08);">
                    <div class="text-center text-muted">
                        <i class="bi bi-people" style="font-size:4rem;color:#1A2E5A;opacity:0.2;"></i>
                        <p class="mt-2 small">{{ __('corporate.careers_photo_placeholder') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Open Roles --}}
        <h2 class="section-heading mb-4">{{ __('corporate.careers_jobs_h2') }}</h2>
        <div class="d-flex flex-column gap-3">
            @foreach(trans('corporate.careers_open_roles') as $role)
            <div class="d-flex align-items-center justify-content-between p-4 rounded-3" style="background:#ffffff;border:1px solid rgba(26,46,90,0.1);">
                <div>
                    <div class="fw-semibold" style="color:#1A1A2E;">{{ $role[0] }}</div>
                    <div class="text-muted small">{{ $role[1] }} · {{ $role[2] }} · {{ $role[3] }}</div>
                </div>
                <a href="mailto:careers@onemember.co?subject={{ __('corporate.careers_cta_email_sub') }} {{ urlencode($role[0]) }}" class="btn btn-sm btn-outline-navy">{{ __('corporate.careers_apply') }}</a>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 p-4 rounded-3" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <h4 class="fw-bold mb-2">{{ __('corporate.careers_no_role_h4') }}</h4>
            <p class="text-muted mb-3">{{ __('corporate.careers_no_role_sub') }}</p>
            <a href="mailto:careers@onemember.co" class="btn btn-pink">{{ __('corporate.careers_send_cv') }} <i class="bi bi-send ms-1"></i></a>
        </div>
    </div>
</section>

@endsection
