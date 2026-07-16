@extends('layouts.corporate')

@section('title', __('corporate.faq_meta_title'))
@section('description', __('corporate.faq_meta_desc'))

@section('content')

{{-- FAQPage structured data — reuses the exact same Q&A rendered below,
     so schema and visible content can never drift apart. --}}
@php
    $faqSchemaEntities = [];
    // Key built via concat: the literal string "@context" in Blade source
    // is mis-parsed as the framework's @context directive otherwise.
    foreach (trans('corporate.faq_categories') as $cat) {
        foreach ($cat[1] as $qa) {
            $faqSchemaEntities[] = [
                ('@' . 'type') => 'Question',
                'name' => $qa[0],
                'acceptedAnswer' => [('@' . 'type') => 'Answer', 'text' => $qa[1]],
            ];
        }
    }
@endphp
<script type="application/ld+json">
{!! json_encode([
    ('@' . 'context') => 'https://schema.org',
    ('@' . 'type') => 'FAQPage',
    'mainEntity' => $faqSchemaEntities,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow text-pink">{{ __('corporate.faq_eyebrow') }}</span>
        <h1>{{ __('corporate.faq_h1') }}</h1>
        <p>{{ __('corporate.faq_sub') }} <a href="{{ route('corporate.contact') }}" class="text-pink">{{ __('corporate.faq_contact_link') }}</a></p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        {{-- Sticky category nav (§08-FAQ blueprint) --}}
        <nav class="d-flex flex-wrap gap-2 mb-5" aria-label="{{ __('corporate.faq_eyebrow') }}">
            @foreach(trans('corporate.faq_categories') as $ci => $cat)
                <a href="#faqCat{{ $ci }}" class="btn btn-sm btn-outline-navy">{{ $cat[0] }}</a>
            @endforeach
        </nav>

        @foreach(trans('corporate.faq_categories') as $ci => $cat)
        <div class="mb-5" id="faqCat{{ $ci }}" style="scroll-margin-top:90px;">
            <h2 class="section-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">{{ $cat[0] }}</h2>
            <div class="accordion corp-faq" id="faqAccordion{{ $ci }}">
                @foreach($cat[1] as $qi => $qa)
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faqQ{{ $ci }}_{{ $qi }}" aria-expanded="false" aria-controls="faqQ{{ $ci }}_{{ $qi }}">
                            {{ $qa[0] }}
                        </button>
                    </h3>
                    <div id="faqQ{{ $ci }}_{{ $qi }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion{{ $ci }}">
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
