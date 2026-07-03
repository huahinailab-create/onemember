@extends('layouts.corporate')

@section('title', 'About Us — OneMember')
@section('description', 'OneMember is on a mission to give every Thai small business the same loyalty tools that big brands use. Built in Thailand. Growing across ASEAN.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">About Us</span>
        <h1>Built in Thailand, for Thailand</h1>
        <p>We believe every Thai small business deserves access to the loyalty tools that big brands take for granted.</p>
    </div>
</section>

{{-- Mission & Vision --}}
<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="section-eyebrow">Our Mission</span>
                <h2 class="section-heading">Loyalty technology for every Thai merchant</h2>
                <p class="section-sub mb-4">OneMember was created because we saw a gap. Large retail chains in Thailand had sophisticated loyalty programmes. Small businesses — the cafés, salons, clinics, and shops that form the backbone of Thai commerce — were still using paper stamp cards.</p>
                <p class="text-muted">We set out to build a platform that is simple enough for a one-person shop to operate in minutes, yet powerful enough to scale with growing chains. PDPA compliant from day one. Thai-first in design and language.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @php
                    $aboutValues = [
                        ['bi-bullseye', 'Mission', 'Give every Thai SME the loyalty tools they deserve — simple, affordable, and PDPA-safe.'],
                        ['bi-eye', 'Vision', "Become the loyalty platform for ASEAN's 70 million small businesses."],
                        ['bi-heart', 'Values', 'Merchant-first. Simple by design. Honest pricing. Built for Thailand.'],
                        ['bi-flag', 'Thailand First', 'We designed for Thai merchants before thinking about any other market.'],
                    ];
                    @endphp
                    @foreach($aboutValues as $value)
                    <div class="col-6">
                        <div class="corp-feature-card text-center">
                            <div class="corp-feature-icon mx-auto"><i class="bi {{ $value[0] }}"></i></div>
                            <h4>{{ $value[1] }}</h4>
                            <p>{{ $value[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Story --}}
<section class="corp-section corp-section-alt">
    <div class="container" style="max-width:800px;">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Our Story</span>
            <h2 class="section-heading">Why OneMember exists</h2>
        </div>
        <div class="d-flex flex-column gap-4">
            <p class="fs-5" style="color:#334155;line-height:1.8;">Every day, millions of Thai customers visit local shops, cafés, and salons. They hand over a paper card to be stamped, then forget about it, lose it, or simply stop coming back. Meanwhile, the merchant has no idea who their best customers are or when they last visited.</p>
            <p style="color:#64748b;line-height:1.8;">OneMember started with a simple question: <em>why can't a small Thai business have the same loyalty intelligence as Starbucks or Central?</em> The answer was that existing tools were too complex, too expensive, or simply not designed for Thai businesses.</p>
            <p style="color:#64748b;line-height:1.8;">We built OneMember to change that. A platform where a café owner can set up a loyalty programme in under 15 minutes, see who their regulars are, and automatically delight them on their birthday — all without needing a developer or a big IT budget.</p>
            <p style="color:#64748b;line-height:1.8;">We launched in Thailand first because Thai SMEs are underserved, Thai consumers are mobile-savvy, and Thailand is one of the fastest-growing digital economies in ASEAN. This is our home market, and we are building it right.</p>
        </div>
    </div>
</section>

{{-- ASEAN Expansion --}}
<section class="corp-section corp-section-navy">
    <div class="container text-center">
        <span class="section-eyebrow" style="color:#FF1585;">ASEAN Expansion</span>
        <h2 class="section-heading section-heading-light">Thailand first. ASEAN next.</h2>
        <p class="section-sub section-sub-light mx-auto mb-5">ASEAN has over 70 million small businesses. Our plan is to build the loyalty infrastructure that powers them all.</p>
        <div class="row g-4 justify-content-center">
            @foreach([
                ['🇹🇭', 'Thailand', 'Launched — Pilot programme active'],
                ['🇻🇳', 'Vietnam', '2026 roadmap'],
                ['🇲🇾', 'Malaysia', '2026 roadmap'],
                ['🇵🇭', 'Philippines', '2027 roadmap'],
            ] as $c)
            <div class="col-md-3 col-6">
                <div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:1.5rem;text-align:center;">
                    <div style="font-size:2.5rem;margin-bottom:0.5rem;">{{ $c[0] }}</div>
                    <div style="font-weight:700;color:#fff;margin-bottom:0.25rem;">{{ $c[1] }}</div>
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);">{{ $c[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="corp-section">
    <div class="container text-center">
        <h2 class="section-heading">Join us in building the future of Thai loyalty</h2>
        <p class="section-sub mx-auto mb-4">Whether you're a merchant, an investor, or someone who wants to join the team — we'd love to hear from you.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-pink btn-pink-lg">Start Free Trial</a>
            <a href="{{ route('corporate.careers') }}" class="btn btn-outline-navy btn-outline-navy-lg">View Careers</a>
            <a href="{{ route('corporate.contact') }}" class="btn btn-outline-navy btn-outline-navy-lg">Contact Us</a>
        </div>
    </div>
</section>

@endsection
