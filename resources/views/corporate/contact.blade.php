@extends('layouts.corporate')

@section('title', 'Contact Us — OneMember')
@section('description', 'Get in touch with the OneMember team. Sales, support, partnerships, and general enquiries.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Contact</span>
        <h1>We'd love to hear from you</h1>
        <p>Whether you're ready to start, have questions, or want to explore a partnership — reach out.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Cards --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    @foreach([
                        ['bi-bag', '#1A2E5A', 'Sales', 'Interested in OneMember for your business or want to discuss enterprise pricing?', 'sales@onemember.co'],
                        ['bi-headset', '#FF1585', 'Support', 'Already a merchant and need help? Our support team is here for you.', 'support@onemember.co'],
                        ['bi-handshake', '#1A2E5A', 'Partnerships', 'Agency, reseller, or integration partner? Let\'s talk.', 'partners@onemember.co'],
                        ['bi-envelope', '#FF1585', 'General', 'All other enquiries — say hello.', 'hello@onemember.co'],
                    ] as $c)
                    <div class="d-flex gap-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:{{ $c[1] === '#FF1585' ? 'rgba(255,21,133,0.1)' : 'rgba(26,46,90,0.08)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $c[0] }}" style="color:{{ $c[1] }};font-size:1.125rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#1A1A2E;">{{ $c[2] }}</div>
                            <div class="text-muted small mb-1">{{ $c[3] }}</div>
                            <a href="mailto:{{ $c[4] }}" style="color:#1A2E5A;font-weight:600;font-size:0.875rem;">{{ $c[4] }}</a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 rounded-3" style="background:rgba(26,46,90,0.05);border:1px solid rgba(26,46,90,0.08);">
                    <div class="fw-semibold small mb-1" style="color:#1A2E5A;"><i class="bi bi-geo-alt me-1"></i> Location</div>
                    <p class="text-muted small mb-0">OneMember Co., Ltd.<br>Bangkok, Thailand 🇹🇭</p>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-7">
                <div class="corp-contact-card">
                    <h3 class="fw-bold mb-1" style="color:#1A1A2E;">Send us a message</h3>
                    <p class="text-muted small mb-4">We reply to all enquiries within 1 business day.</p>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Your Name</label>
                                <input type="text" class="form-control" placeholder="Khun Somchai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address</label>
                                <input type="email" class="form-control" placeholder="you@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Business Name (optional)</label>
                                <input type="text" class="form-control" placeholder="Your shop or company name">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Enquiry Type</label>
                                <select class="form-select">
                                    <option value="">Select...</option>
                                    <option>Sales & Pricing</option>
                                    <option>Technical Support</option>
                                    <option>Partnership</option>
                                    <option>Security / Privacy</option>
                                    <option>General Enquiry</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Message</label>
                                <textarea class="form-control" rows="5" placeholder="Tell us about your business and how we can help..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-pink w-100">Send Message <i class="bi bi-send ms-1"></i></button>
                                <p class="text-muted small text-center mt-2 mb-0">We'll respond within 1 business day.</p>
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
                <p class="mt-2 small">Bangkok, Thailand</p>
            </div>
        </div>
    </div>
</section>

@endsection
