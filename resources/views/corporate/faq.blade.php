@extends('layouts.corporate')

@section('title', __('corporate.faq_meta_title'))
@section('description', __('corporate.faq_meta_desc'))

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">{{ __('corporate.faq_eyebrow') }}</span>
        <h1>{{ __('corporate.faq_h1') }}</h1>
        <p>{{ __('corporate.faq_sub') }} <a href="{{ route('corporate.contact') }}" style="color:#FF1585;">{{ __('corporate.faq_contact_link') }}</a></p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        @foreach(trans('corporate.faq_categories') as $ci => $cat)
        <div class="mb-5">
            <h2 class="section-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">{{ $cat[0] }}</h2>
            <div class="accordion corp-faq" id="faqCat{{ $ci }}">
                @foreach($cat[1] as $qi => $qa)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqQ{{ $ci }}_{{ $qi }}">
                            {{ $qa[0] }}
                        </button>
                    </h2>
                    <div id="faqQ{{ $ci }}_{{ $qi }}" class="accordion-collapse collapse" data-bs-parent="#faqCat{{ $ci }}">
                        <div class="accordion-body">{{ $qa[1] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="text-center mt-4 p-4 rounded-3" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <h4 class="fw-bold mb-2">{{ __('corporate.faq_still_h4') }}</h4>
            <p class="text-muted mb-3">{{ __('corporate.faq_still_sub') }}</p>
            <a href="{{ route('corporate.contact') }}" class="btn btn-pink">{{ __('corporate.cta_contact_us') }}</a>
        </div>
    </div>
</section>

@endsection
