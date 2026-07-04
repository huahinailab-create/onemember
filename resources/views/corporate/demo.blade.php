@extends('layouts.corporate')

@section('title', __('corporate.demo_meta_title'))
@section('description', __('corporate.demo_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.demo_eyebrow') }}</span>
        <h1>{{ __('corporate.demo_h1') }}</h1>
        <p>{{ __('corporate.demo_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <h2 class="section-heading">{{ __('corporate.demo_preview_h3') }}</h2>
                <div class="d-flex flex-column gap-4 mt-4">
                    @foreach(trans('corporate.demo_demo_previews') as $step)
                    <div class="d-flex gap-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:rgba(26,46,90,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $step[0] }}" style="color:#1A2E5A;font-size:1.125rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#1A1A2E;">{{ $step[1] }}</div>
                            <div class="text-muted small">{{ $step[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 rounded-3" style="background:rgba(255,21,133,0.06);border:1px solid rgba(255,21,133,0.15);">
                    <div class="fw-semibold small mb-1" style="color:#FF1585;">{{ __('corporate.demo_or_trial') }}</div>
                    <p class="text-muted small mb-2">{{ __('corporate.demo_prefer_trial_body') }}</p>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-pink">{{ __('corporate.cta_start_trial') }} <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="corp-contact-card">
                    <h3 class="fw-bold mb-1" style="color:#1A1A2E;">{{ __('corporate.demo_form_h3') }}</h3>
                    <p class="text-muted small mb-4">{{ __('corporate.demo_reach_out') }}</p>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_form_name') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('corporate.demo_form_name_ph') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_form_phone') }}</label>
                                <input type="tel" class="form-control" placeholder="{{ __('corporate.demo_form_phone_ph') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_form_email') }}</label>
                                <input type="email" class="form-control" placeholder="{{ __('corporate.demo_form_email_ph') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_form_biz') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('corporate.demo_form_biz_ph') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_industry_label') }}</label>
                                <select class="form-select">
                                    <option value="">{{ __('corporate.demo_industry_ph') }}</option>
                                    @foreach(trans('corporate.demo_industries') as $ind)
                                    <option>{{ $ind }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_customers_label') }}</label>
                                <select class="form-select">
                                    <option value="">{{ __('corporate.demo_customers_ph') }}</option>
                                    @foreach(trans('corporate.demo_customers_options') as $opt)
                                    <option>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('corporate.demo_specific_label') }}</label>
                                <textarea class="form-control" rows="3" placeholder="{{ __('corporate.demo_specific_ph') }}"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-pink w-100">{{ __('corporate.demo_schedule_btn') }} <i class="bi bi-calendar-check ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
