@extends('layouts.corporate')

@section('title', 'Pricing — OneMember')
@section('description', 'Simple, transparent pricing for Thai SMEs. Start free, upgrade when ready. 30-day Professional trial included — no credit card required.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Pricing</span>
        <h1>Simple, honest pricing</h1>
        <p>Start free. Upgrade when your business is ready. No hidden fees, no surprises.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center gap-2 bg-light rounded-pill px-3 py-2">
                <i class="bi bi-gift-fill text-pink" style="color:#FF1585;"></i>
                <span class="small fw-semibold">All new merchants get a <strong>30-day Professional trial</strong> — no credit card required</span>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Free --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card">
                    <div class="corp-pricing-plan">Free</div>
                    <div class="corp-pricing-price">฿0 <span>/ month</span></div>
                    <p class="corp-pricing-desc">Perfect for testing the platform or running a very small shop.</p>
                    <ul class="corp-pricing-features">
                        <li><i class="bi bi-check-circle-fill"></i> Up to 100 members</li>
                        <li><i class="bi bi-check-circle-fill"></i> 1 active campaign</li>
                        <li><i class="bi bi-check-circle-fill"></i> Points or stamps</li>
                        <li><i class="bi bi-check-circle-fill"></i> Member QR cards</li>
                        <li><i class="bi bi-check-circle-fill"></i> Data export</li>
                        <li class="na"><i class="bi bi-dash"></i> Birthday rewards</li>
                        <li class="na"><i class="bi bi-dash"></i> Analytics & reports</li>
                        <li class="na"><i class="bi bi-dash"></i> Staff accounts</li>
                        <li class="na"><i class="bi bi-dash"></i> Priority support</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-navy w-100">Get Started Free</a>
                </div>
            </div>

            {{-- Starter --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card">
                    <div class="corp-pricing-plan">Starter</div>
                    <div class="corp-pricing-price">TBA <span style="font-size:0.875rem;"> / month</span></div>
                    <p class="corp-pricing-desc">For growing shops ready to automate their loyalty programme.</p>
                    <ul class="corp-pricing-features">
                        <li><i class="bi bi-check-circle-fill"></i> Up to 500 members</li>
                        <li><i class="bi bi-check-circle-fill"></i> 3 active campaigns</li>
                        <li><i class="bi bi-check-circle-fill"></i> Points or stamps</li>
                        <li><i class="bi bi-check-circle-fill"></i> Birthday rewards</li>
                        <li><i class="bi bi-check-circle-fill"></i> Data export</li>
                        <li><i class="bi bi-check-circle-fill"></i> 2 staff accounts</li>
                        <li class="na"><i class="bi bi-dash"></i> Analytics & reports</li>
                        <li class="na"><i class="bi bi-dash"></i> Custom branding</li>
                        <li class="na"><i class="bi bi-dash"></i> Priority support</li>
                    </ul>
                    <a href="{{ route('corporate.demo') }}" class="btn btn-outline-navy w-100">Contact for Pricing</a>
                </div>
            </div>

            {{-- Professional --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card featured">
                    <div class="corp-pricing-badge">Most Popular</div>
                    <div class="corp-pricing-plan">Professional</div>
                    <div class="corp-pricing-price">TBA <span style="font-size:0.875rem;"> / month</span></div>
                    <p class="corp-pricing-desc">The full platform for established businesses. Includes 30-day free trial.</p>
                    <ul class="corp-pricing-features">
                        <li><i class="bi bi-check-circle-fill"></i> Unlimited members</li>
                        <li><i class="bi bi-check-circle-fill"></i> Unlimited campaigns</li>
                        <li><i class="bi bi-check-circle-fill"></i> Points & stamps</li>
                        <li><i class="bi bi-check-circle-fill"></i> Birthday rewards</li>
                        <li><i class="bi bi-check-circle-fill"></i> Full analytics</li>
                        <li><i class="bi bi-check-circle-fill"></i> Unlimited staff</li>
                        <li><i class="bi bi-check-circle-fill"></i> Custom branding</li>
                        <li><i class="bi bi-check-circle-fill"></i> Priority support</li>
                        <li><i class="bi bi-check-circle-fill"></i> Data export</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-pink w-100">Start 30-Day Trial</a>
                </div>
            </div>

            {{-- Enterprise --}}
            <div class="col-lg-3 col-md-6">
                <div class="corp-pricing-card">
                    <div class="corp-pricing-plan">Enterprise</div>
                    <div class="corp-pricing-price" style="font-size:1.75rem;">Custom</div>
                    <p class="corp-pricing-desc">For multi-location businesses and chains with custom integration needs.</p>
                    <ul class="corp-pricing-features">
                        <li><i class="bi bi-check-circle-fill"></i> Everything in Professional</li>
                        <li><i class="bi bi-check-circle-fill"></i> Multi-location</li>
                        <li><i class="bi bi-check-circle-fill"></i> API access</li>
                        <li><i class="bi bi-check-circle-fill"></i> Custom integration</li>
                        <li><i class="bi bi-check-circle-fill"></i> Dedicated account manager</li>
                        <li><i class="bi bi-check-circle-fill"></i> SLA guarantee</li>
                        <li><i class="bi bi-check-circle-fill"></i> PDPA DPA available</li>
                        <li><i class="bi bi-check-circle-fill"></i> Custom reporting</li>
                    </ul>
                    <a href="{{ route('corporate.contact') }}" class="btn btn-outline-navy w-100">Contact Sales</a>
                </div>
            </div>
        </div>

        <p class="text-center text-muted small mt-4">Prices in Thai Baht (฿). Final pricing will be announced before public launch. <a href="{{ route('corporate.contact') }}">Contact us</a> with questions.</p>
    </div>
</section>

{{-- FAQ --}}
<section class="corp-section corp-section-alt">
    <div class="container" style="max-width:720px;">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Pricing FAQ</span>
            <h2 class="section-heading">Your questions answered</h2>
        </div>
        <div class="accordion corp-faq" id="pricingFaq">
            @foreach([
                ['Do I need a credit card to start?', 'No. Your 30-day Professional trial starts the moment you create your account. No credit card required until the trial ends.'],
                ['What happens when my trial ends?', 'At the end of your trial, you choose a plan. If you choose not to upgrade, your account moves to the Free plan (up to 100 members, 1 campaign). Your data is never deleted.'],
                ['Can I change plans later?', 'Yes. You can upgrade or downgrade at any time from your subscription settings.'],
                ['Is there a contract or minimum term?', 'No long-term contracts. All paid plans are month-to-month. Cancel any time.'],
                ['Do you offer discounts for annual billing?', 'Annual billing discounts will be available at public launch. Contact us to discuss early-adopter pricing.'],
            ] as $i => $qa)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#pFaq{{ $i }}">
                        {{ $qa[0] }}
                    </button>
                </h2>
                <div id="pFaq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#pricingFaq">
                    <div class="accordion-body">{{ $qa[1] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">Start your free 30-day trial today</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">No credit card. No commitment. Full Professional features for 30 days.</p>
        <a href="{{ route('register') }}" class="btn btn-pink btn-pink-lg">Create Free Account <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</section>

@endsection
