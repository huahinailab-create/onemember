@extends('layouts.corporate')

@section('title', 'Book a Demo — OneMember')
@section('description', 'Book a personalised OneMember demo. See how the loyalty platform works for your specific business. 30 minutes, no commitment.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Book a Demo</span>
        <h1>See OneMember in action</h1>
        <p>30 minutes. Personalised to your business type. No pressure, no commitment.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <h2 class="section-heading">What you'll see in your demo</h2>
                <div class="d-flex flex-column gap-4 mt-4">
                    @foreach([
                        ['bi-lightning', 'Live platform walkthrough', 'We\'ll walk you through the full OneMember dashboard — members, campaigns, reports, and member QR cards.'],
                        ['bi-building', 'Configured for your business', 'Tell us your industry and we\'ll show you exactly how OneMember works for a business like yours.'],
                        ['bi-chat-dots', 'Your questions answered', 'Bring any questions about pricing, PDPA compliance, setup time, or specific features. We\'ll answer everything.'],
                        ['bi-play-circle', 'Next steps at your pace', 'Start your trial after the demo — or take your time. No pressure.'],
                    ] as $step)
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
                    <div class="fw-semibold small mb-1" style="color:#FF1585;">Prefer to explore yourself?</div>
                    <p class="text-muted small mb-2">Start your 30-day free trial right now — no demo required.</p>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-pink">Start Free Trial <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="corp-contact-card">
                    <h3 class="fw-bold mb-1" style="color:#1A1A2E;">Request your demo</h3>
                    <p class="text-muted small mb-4">We'll reach out within 1 business day to schedule a time that works for you.</p>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Your Name</label>
                                <input type="text" class="form-control" placeholder="Khun Somchai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Phone Number</label>
                                <input type="tel" class="form-control" placeholder="08X-XXX-XXXX">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Email Address</label>
                                <input type="email" class="form-control" placeholder="you@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Business Name</label>
                                <input type="text" class="form-control" placeholder="Your shop name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Industry</label>
                                <select class="form-select">
                                    <option value="">Select your industry...</option>
                                    <option>Café / Coffee Shop</option>
                                    <option>Restaurant / Food</option>
                                    <option>Beauty / Hair Salon</option>
                                    <option>Clinic / Wellness</option>
                                    <option>Retail Shop</option>
                                    <option>Car Wash / Auto</option>
                                    <option>Tutoring / Education</option>
                                    <option>Hotel / Guesthouse</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Number of current customers (approximate)</label>
                                <select class="form-select">
                                    <option value="">Select...</option>
                                    <option>Under 100</option>
                                    <option>100 – 500</option>
                                    <option>500 – 2,000</option>
                                    <option>2,000 – 10,000</option>
                                    <option>10,000+</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Anything specific you'd like to see?</label>
                                <textarea class="form-control" rows="3" placeholder="e.g. How birthday rewards work, how to set up a stamp card, PDPA questions..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-pink w-100">Request Demo <i class="bi bi-calendar-check ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
