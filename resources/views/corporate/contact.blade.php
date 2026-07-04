@extends('layouts.corporate')

@section('title', __('corporate.contact_meta_title'))
@section('description', __('corporate.contact_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.contact_eyebrow') }}</span>
        <h1>{{ __('corporate.contact_h1') }}</h1>
        <p>{{ __('corporate.contact_sub') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Cards --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    @foreach(trans('corporate.contact_channels') as $c)
                    <div class="d-flex gap-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:{{ $c['color'] === '#FF1585' ? 'rgba(255,21,133,0.1)' : 'rgba(26,46,90,0.08)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $c['icon'] }}" style="color:{{ $c['color'] }};font-size:1.125rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#1A1A2E;">{{ $c['title'] }}</div>
                            <div class="text-muted small mb-1">{{ $c['desc'] }}</div>
                            <a href="mailto:{{ $c['email'] }}" style="color:#1A2E5A;font-weight:600;font-size:0.875rem;">{{ $c['email'] }}</a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 rounded-3" style="background:rgba(26,46,90,0.05);border:1px solid rgba(26,46,90,0.08);">
                    <div class="fw-semibold small mb-1" style="color:#1A2E5A;"><i class="bi bi-geo-alt me-1"></i> {{ __('corporate.contact_location_label') }}</div>
                    <p class="text-muted small mb-0">{{ __('corporate.contact_location_name') }}<br>{{ __('corporate.contact_location_city') }}</p>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-7">
                <div class="corp-contact-card">
                    <h3 class="fw-bold mb-1" style="color:#1A1A2E;">{{ __('corporate.contact_form_h3') }}</h3>
                    <p class="text-muted small mb-4">{{ __('corporate.contact_form_sub') }}</p>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('corporate.contact_form_name') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('corporate.contact_form_name_ph') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('corporate.contact_form_email') }}</label>
                                <input type="email" class="form-control" placeholder="{{ __('corporate.contact_form_email_ph') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('corporate.contact_form_biz') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('corporate.contact_form_biz_ph') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('corporate.contact_form_type') }}</label>
                                <select class="form-select">
                                    <option value="">{{ __('corporate.contact_form_type_ph') }}</option>
                                    @foreach(trans('corporate.contact_form_types') as $type)
                                    <option>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('corporate.contact_form_message') }}</label>
                                <textarea class="form-control" rows="5" placeholder="{{ __('corporate.contact_form_message_ph') }}"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-pink w-100">{{ __('corporate.cta_send_message') }} <i class="bi bi-send ms-1"></i></button>
                                <p class="text-muted small text-center mt-2 mb-0">{{ __('corporate.contact_form_reply_note') }}</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Map Placeholder --}}
        <div class="mt-5 rounded-4 d-flex align-items-center justify-content-center" style="height:240px;background:#F8F9FB;border:1px solid rgba(26,46,90,0.08);">
            <div class="text-center text-muted">
                <i class="bi bi-map" style="font-size:3rem;color:#1A2E5A;opacity:0.25;"></i>
                <p class="mt-2 small">{{ __('corporate.contact_map_label') }}</p>
            </div>
        </div>
    </div>
</section>

@endsection
