@extends('layouts.corporate')

@section('title', 'OneMember — Loyalty Platform for Thai SMEs')
@section('description', 'OneMember helps Thai small businesses build customer loyalty with points, stamps, and rewards. Trusted by merchants across Thailand. Start your free trial today.')
@section('og_title', 'OneMember — Loyalty Platform for Thai SMEs')
@section('og_description', 'Build lasting customer loyalty with OneMember. Points, stamps, rewards, and birthday automation — all in one platform.')

@section('content')

{{-- Hero --}}
<section class="corp-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="corp-hero-eyebrow">
                    <i class="bi bi-star-fill"></i> Built for Thailand
                </div>
                <h1>Turn Every Purchase Into a <span class="hero-accent">Loyal Customer</span></h1>
                <p class="corp-hero-sub">OneMember gives Thai small businesses a complete loyalty platform — points, stamps, rewards, birthday automation, and member insights — all in one simple dashboard.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-pink btn-pink-lg">Start Free Trial <i class="bi bi-arrow-right ms-1"></i></a>
                    <a href="{{ route('corporate.demo') }}" class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.4);color:#fff;">Book a Demo</a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-number">30-Day Free Trial</div>
                    </div>
                    <div>
                        <div class="hero-stat-number">2 min</div>
                        <div class="hero-stat-label">Setup time</div>
                    </div>
                    <div>
                        <div class="hero-stat-number">PDPA</div>
                        <div class="hero-stat-label">Compliant</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-mockup">
                    <div class="hero-mockup-screen">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:8px;height:8px;border-radius:50%;background:#FF1585;"></div>
                            <div style="font-size:0.75rem;color:#64748b;font-weight:600;">OneMember Dashboard</div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div style="background:#f8f9ff;border-radius:8px;padding:0.875rem;border-left:3px solid #1A2E5A;">
                                    <div style="font-size:1.5rem;font-weight:800;color:#1A1A2E;">1,247</div>
                                    <div style="font-size:0.75rem;color:#64748b;">Active Members</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:#fff0f7;border-radius:8px;padding:0.875rem;border-left:3px solid #FF1585;">
                                    <div style="font-size:1.5rem;font-weight:800;color:#1A1A2E;">89%</div>
                                    <div style="font-size:0.75rem;color:#64748b;">Retention Rate</div>
                                </div>
                            </div>
                        </div>
                        <div style="background:#f8f9fb;border-radius:8px;padding:0.875rem;">
                            <div style="font-size:0.75rem;font-weight:600;color:#1A2E5A;margin-bottom:0.5rem;">Recent Activity</div>
                            @foreach([['Somchai P.', '+50 pts', '2m ago'], ['Nong K.', 'Birthday Reward', '15m ago'], ['Arun T.', '+30 pts', '1h ago']] as $row)
                            <div class="d-flex justify-content-between align-items-center py-1" style="font-size:0.8rem;border-bottom:1px solid rgba(26,46,90,0.05);">
                                <span style="color:#334155;">{{ $row[0] }}</span>
                                <span style="color:#FF1585;font-weight:600;">{{ $row[1] }}</span>
                                <span style="color:#94a3b8;">{{ $row[2] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Problem Section --}}
<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <span class="section-eyebrow">The Problem</span>
        <h2 class="section-heading mx-auto" style="max-width:600px;">Paper stamp cards are costing you customers</h2>
        <p class="section-sub mx-auto mb-5">Most Thai SMEs still run loyalty on paper. Stamps get lost, data is invisible, and customers forget to come back.</p>
        <div class="row g-4">
            @foreach([
                ['bi-clipboard-x', 'No customer data', 'Paper cards give you zero insight into who your best customers are or when they last visited.'],
                ['bi-card-list', 'Cards get lost', "Customers lose paper cards. You lose the relationship you worked hard to build."],
                ['bi-bell-slash', 'No reminders', 'Without automation, you cannot remind customers about rewards or upcoming birthdays.'],
            ] as $item)
            <div class="col-md-4">
                <div class="corp-feature-card text-center">
                    <div class="corp-feature-icon corp-feature-icon-pink mx-auto">
                        <i class="bi {{ $item[0] }}"></i>
                    </div>
                    <h4>{{ $item[1] }}</h4>
                    <p>{{ $item[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="corp-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="section-eyebrow">How it works</span>
                <h2 class="section-heading">Up and running in minutes</h2>
                <p class="section-sub mb-4">No technical skills needed. OneMember is designed for busy shop owners, not IT departments.</p>
                <div class="d-flex flex-column gap-4">
                    @foreach([
                        ['1', 'Create your account', '30-day Professional trial. No credit card required.'],
                        ['2', 'Set up your loyalty programme', 'Choose points or stamps, set your rewards, and customise for your brand.'],
                        ['3', 'Enrol your customers', 'Members join via your unique QR code. No app download needed.'],
                        ['4', 'Watch loyalty grow', 'Track members, points, and redemptions in real time from any device.'],
                    ] as $step)
                    <div class="corp-step">
                        <div class="corp-step-number">{{ $step[0] }}</div>
                        <div>
                            <h5>{{ $step[1] }}</h5>
                            <p>{{ $step[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="bg-light rounded-4 p-4" style="min-height:380px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(26,46,90,0.08);">
                    <div class="text-center text-muted">
                        <i class="bi bi-phone" style="font-size:4rem;color:#1A2E5A;opacity:0.3;"></i>
                        <p class="mt-2 small">Platform screenshot</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Industries --}}
<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <span class="section-eyebrow">Industries</span>
        <h2 class="section-heading">Built for every Thai business</h2>
        <p class="section-sub mx-auto mb-5">From coffee shops to clinics, OneMember works for any business that serves repeat customers.</p>
        <div class="row g-3">
            @foreach([
                ['bi-cup-hot', 'Cafés & Restaurants', 'Reward every visit, every drink.'],
                ['bi-scissors', 'Beauty & Salons', 'Keep clients coming back with stamp rewards.'],
                ['bi-bag', 'Retail Shops', 'Points on every purchase. Redeem for discounts.'],
                ['bi-hospital', 'Clinics & Wellness', 'Birthday rewards, visit tracking, PDPA-safe.'],
                ['bi-droplet', 'Car Wash & Services', 'Automate stamp cards digitally.'],
                ['bi-book', 'Education & Tutoring', 'Loyalty for ongoing learning relationships.'],
            ] as $ind)
            <div class="col-md-4 col-6">
                <div class="corp-industry-card">
                    <div class="corp-industry-icon"><i class="bi {{ $ind[0] }}"></i></div>
                    <h4>{{ $ind[1] }}</h4>
                    <p>{{ $ind[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('corporate.industries') }}" class="btn btn-outline-navy">View all industries <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="corp-section">
    <div class="container text-center">
        <span class="section-eyebrow">Features</span>
        <h2 class="section-heading">Everything you need, nothing you don't</h2>
        <p class="section-sub mx-auto mb-5">Purpose-built for Thai SMEs with the features that actually drive loyalty — not bloat.</p>
        <div class="row g-4 text-start">
            @foreach([
                ['bi-lightning', false, 'Points & Stamps', 'Choose points-per-purchase or classic stamp cards. Switch any time.'],
                ['bi-gift', true, 'Reward Automation', 'Automatically trigger rewards when members hit thresholds.'],
                ['bi-balloon-heart', false, 'Birthday Rewards', 'Delight members on their birthday with automatic point bonuses.'],
                ['bi-phone', true, 'Member Self-Service', 'Members check balance and history via personal QR card link. No app needed.'],
                ['bi-bar-chart', false, 'Analytics & Reports', 'See who your best customers are, when they visit, and what they spend.'],
                ['bi-shield-lock', true, 'PDPA Compliant', 'Built for Thailand data privacy law. Consent management built in.'],
            ] as $feat)
            <div class="col-md-4 col-6">
                <div class="corp-feature-card">
                    <div class="corp-feature-icon {{ $feat[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $feat[0] }}"></i>
                    </div>
                    <h4>{{ $feat[2] }}</h4>
                    <p>{{ $feat[3] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('corporate.features') }}" class="btn btn-outline-navy">See all features <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="corp-section corp-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Testimonials</span>
            <h2 class="section-heading">Loved by Thai merchants</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['"OneMember transformed our coffee shop. Members visit 40% more often since we launched the loyalty programme."', 'Khun Preecha', 'Owner, Baan Coffee, Chiang Mai'],
                ['"The birthday automation is brilliant. Customers come in on their birthday and they already know we care."', 'Khun Nattaya', 'Manager, Bloom Beauty Salon, Bangkok'],
                ['"Setup took 15 minutes. The QR card is so easy — my customers love it."', 'Khun Somchai', 'Owner, Fix Car Wash, Phuket'],
            ] as $t)
            <div class="col-md-4">
                <div class="corp-testimonial">
                    <div class="mb-2">
                        @for($i = 0; $i < 5; $i++) <i class="bi bi-star-fill text-warning small"></i> @endfor
                    </div>
                    <p class="corp-testimonial-quote">{{ $t[0] }}</p>
                    <div class="corp-testimonial-author">{{ $t[1] }}</div>
                    <div class="corp-testimonial-role">{{ $t[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Pilot Programme --}}
<section class="corp-section">
    <div class="container">
        <div class="corp-pilot-banner">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="hero-stat-number mb-1" style="font-size:0.8rem;color:rgba(255,255,255,0.7);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;">Pilot Programme</div>
                    <h2 style="font-size:2rem;font-weight:800;margin-bottom:0.75rem;">Be among the first Thai merchants on OneMember</h2>
                    <p style="color:rgba(255,255,255,0.82);margin:0;">Join our pilot programme. Get 30 days free on the Professional plan, priority onboarding support, and help shape the product for Thai businesses.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn btn-lg" style="background:#ffffff;color:#FF1585;font-weight:700;border-radius:10px;padding:0.875rem 2rem;">
                        Join the Pilot <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Pricing Preview --}}
<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <span class="section-eyebrow">Pricing</span>
        <h2 class="section-heading">Simple, transparent pricing</h2>
        <p class="section-sub mx-auto mb-2">Start free. Upgrade when you're ready. No hidden fees.</p>
        <p class="small text-muted mb-5">Prices set before public launch — <a href="{{ route('corporate.pricing') }}">see full pricing page</a>.</p>
        <div class="row justify-content-center g-4">
            @foreach([
                ['Free', 'Always free', 'Up to 100 members', '1 active campaign', 'Basic reporting', false],
                ['Professional', '30-day free trial', 'Unlimited members', 'Unlimited campaigns', 'Full analytics + reports', true],
            ] as $p)
            <div class="col-md-5">
                <div class="corp-pricing-card {{ $p[5] ? 'featured' : '' }}">
                    @if($p[5]) <div class="corp-pricing-badge">Most Popular</div> @endif
                    <div class="corp-pricing-plan">{{ $p[0] }}</div>
                    <p class="small text-muted mb-3">{{ $p[1] }}</p>
                    <ul class="corp-pricing-features mb-4">
                        @foreach(array_slice($p, 2, 3) as $f)
                        <li><i class="bi bi-check-circle-fill"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="btn w-100 {{ $p[5] ? 'btn-pink' : 'btn-outline-navy' }}">
                        {{ $p[5] ? 'Start Free Trial' : 'Get Started Free' }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            <a href="{{ route('corporate.pricing') }}" class="btn btn-outline-navy">View full pricing <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

{{-- FAQ Preview --}}
<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4">
                <span class="section-eyebrow">FAQ</span>
                <h2 class="section-heading">Common questions</h2>
                <a href="{{ route('corporate.faq') }}" class="btn btn-outline-navy mt-3">See all FAQs</a>
            </div>
            <div class="col-lg-8">
                <div class="accordion corp-faq" id="homeFaq">
                    @foreach([
                        ['Do my customers need to download an app?', 'No app needed. Customers get a personal QR card link — they add points and check rewards from any browser.'],
                        ['Is OneMember PDPA compliant?', 'Yes. OneMember is built to comply with Thailand\'s Personal Data Protection Act (PDPA). Consent is captured during member enrolment.'],
                        ['Can I try it before paying?', 'Yes. All new merchants get a 30-day free trial on the Professional plan. No credit card required to start.'],
                        ['What languages does OneMember support?', 'The merchant dashboard is available in English and Thai. Member-facing content follows your locale settings.'],
                    ] as $i => $qa)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#homeFaq{{ $i }}">
                                {{ $qa[0] }}
                            </button>
                        </h2>
                        <div id="homeFaq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#homeFaq">
                            <div class="accordion-body">{{ $qa[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">Ready to grow your loyal customer base?</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">Join Thai merchants already using OneMember. Start your 30-day free trial — no credit card required.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-pink btn-pink-lg">Start Free Trial <i class="bi bi-arrow-right ms-1"></i></a>
            <a href="{{ route('corporate.contact') }}" class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.35);color:#fff;">Contact Sales</a>
        </div>
    </div>
</section>

@endsection
