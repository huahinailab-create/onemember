@extends('layouts.corporate')

@section('title', __('corporate.contact_meta_title'))
@section('description', __('corporate.contact_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.contact_eyebrow') }}</span>
        <h1>{{ __('corporate.contact_h1') }}</h1>
        <p>{{ __('corporate.contact_sub') }}</p>
    </div>
</section>

@if (config('services.line.oa_url'))
<section class="corp-section pb-0">
    <div class="container">
        <a href="{{ config('services.line.oa_url') }}" target="_blank" rel="noopener"
           class="d-flex align-items-center gap-3 p-4 rounded-4 text-decoration-none"
           style="background:rgba(255,21,133,0.06);border:1px solid rgba(255,21,133,0.2);">
            <i class="bi bi-chat-dots-fill text-pink" style="font-size:1.75rem;" aria-hidden="true"></i>
            <div>
                <div class="fw-bold" style="color:#1A1A2E;">{{ __('corporate.contact_line_cta') }}</div>
                <div class="text-muted small">{{ __('corporate.contact_line_note') }}</div>
            </div>
            <i class="bi bi-arrow-right ms-auto text-pink" aria-hidden="true"></i>
        </a>
    </div>
</section>
@endif

<section class="corp-section">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Cards --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    @foreach(trans('corporate.contact_channels') as $c)
                    <div class="d-flex gap-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:{{ $c['color'] === '#FF1585' ? 'rgba(255,21,133,0.1)' : 'rgba(26,46,90,0.08)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $c['icon'] }}" style="color:{{ $c['color'] }};font-size:1.125rem;" aria-hidden="true"></i>
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
                    <div class="fw-semibold small mb-1" style="color:#1A2E5A;"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i> {{ __('corporate.contact_location_label') }}</div>
                    <p class="text-muted small mb-0">{{ __('corporate.contact_location_name') }}<br>{{ __('corporate.contact_location_city') }}</p>
                </div>
            </div>

            {{-- Contact Form — client-side mailto only; there is no backend
                 handler for this form, so it must never claim a response
                 promise it can't keep (see docs/OMOS/Website/09-Contact.md). --}}
            <div class="col-lg-7">
                <div class="corp-contact-card">
                    <h3 class="fw-bold mb-1" style="color:#1A1A2E;">{{ __('corporate.contact_form_h3') }}</h3>
                    <p class="text-muted small mb-4">{{ __('corporate.contact_form_sub') }}</p>
                    <form id="corp-contact-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact-name" class="form-label small fw-semibold">{{ __('corporate.contact_form_name') }}</label>
                                <input type="text" id="contact-name" name="name" class="form-control" placeholder="{{ __('corporate.contact_form_name_ph') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-email" class="form-label small fw-semibold">{{ __('corporate.contact_form_email') }}</label>
                                <input type="email" id="contact-email" name="email" class="form-control" placeholder="{{ __('corporate.contact_form_email_ph') }}" required>
                            </div>
                            <div class="col-12">
                                <label for="contact-biz" class="form-label small fw-semibold">{{ __('corporate.contact_form_biz') }}</label>
                                <input type="text" id="contact-biz" name="business" class="form-control" placeholder="{{ __('corporate.contact_form_biz_ph') }}">
                            </div>
                            <div class="col-12">
                                <label for="contact-type" class="form-label small fw-semibold">{{ __('corporate.contact_form_type') }}</label>
                                <select id="contact-type" name="type" class="form-select">
                                    <option value="">{{ __('corporate.contact_form_type_ph') }}</option>
                                    @foreach(trans('corporate.contact_form_types') as $type)
                                    <option>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="contact-message" class="form-label small fw-semibold">{{ __('corporate.contact_form_message') }}</label>
                                <textarea id="contact-message" name="message" class="form-control" rows="5" placeholder="{{ __('corporate.contact_form_message_ph') }}" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-pink w-100">{{ __('corporate.cta_send_message') }} <i class="bi bi-send ms-1" aria-hidden="true"></i></button>
                                <p class="text-muted small text-center mt-2 mb-0">{{ __('corporate.contact_form_reply_note') }}</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Map — deferred, no office worship, see docs/OMOS/Website/09-Contact.md --}}
        <div class="mt-5 rounded-4 d-flex align-items-center justify-content-center" style="height:240px;background:#F8F9FB;border:1px solid rgba(26,46,90,0.08);">
            <div class="text-center text-muted">
                <i class="bi bi-map" style="font-size:3rem;color:#1A2E5A;opacity:0.25;" aria-hidden="true"></i>
                <p class="mt-2 small">{{ __('corporate.contact_map_label') }}</p>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('corp-contact-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    var f = new FormData(this);
    var subject = encodeURIComponent('[OneMember] ' + (f.get('type') || 'General Enquiry') + ' — ' + (f.get('business') || f.get('name')));
    var body = encodeURIComponent(
        'Name: ' + f.get('name') + '\n' +
        'Email: ' + f.get('email') + '\n' +
        'Business: ' + (f.get('business') || '-') + '\n\n' +
        f.get('message')
    );
    window.location.href = 'mailto:hello@onemember.co?subject=' + subject + '&body=' + body;
});
</script>

@endsection
