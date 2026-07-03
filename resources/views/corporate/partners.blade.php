@extends('layouts.corporate')

@section('title', 'Partners — OneMember')
@section('description', 'Partner with OneMember. Agency partners, resellers, and technology integrations for the Thai loyalty market.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Partners</span>
        <h1>Grow together with OneMember</h1>
        <p>Join the OneMember partner ecosystem. Help Thai businesses build loyalty — and grow your own business in the process.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4 mb-5">
            @foreach([
                ['bi-briefcase', false, 'Agency Partner', 'Digital marketing agencies, web studios, and consultants. Resell OneMember to your clients and earn recurring commission.'],
                ['bi-shop', true, 'Reseller Partner', 'Regional distributors and local business service providers. Sell OneMember directly in your market.'],
                ['bi-plug', false, 'Technology Partner', 'POS systems, booking platforms, and business software. Integrate with OneMember to add loyalty to your product.'],
                ['bi-building', true, 'Enterprise Partner', 'Multi-location chains and franchise networks. Custom implementation, dedicated support, and volume pricing.'],
            ] as $p)
            <div class="col-md-6">
                <div class="corp-feature-card h-100">
                    <div class="corp-feature-icon {{ $p[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $p[0] }}"></i>
                    </div>
                    <h4>{{ $p[2] }}</h4>
                    <p>{{ $p[3] }}</p>
                    <a href="mailto:partners@onemember.co" class="btn btn-sm btn-outline-navy mt-2">Learn More</a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Partner Benefits --}}
        <div class="corp-contact-card mb-5">
            <h3 class="fw-bold mb-4" style="color:#1A1A2E;">Partner benefits</h3>
            <div class="row g-4">
                @foreach([
                    ['Recurring revenue', 'Earn commission on every merchant you refer, for as long as they stay on OneMember.'],
                    ['Co-marketing support', 'Joint case studies, co-branded materials, and listing in our partner directory.'],
                    ['Partner portal access', 'Manage your referrals, track commissions, and access partner-only resources.'],
                    ['Priority support', 'Dedicated partner success manager and escalation path for your clients.'],
                ] as $b)
                <div class="col-md-6">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-check-circle-fill mt-1" style="color:#FF1585;font-size:0.875rem;"></i>
                        <div>
                            <div class="fw-semibold small" style="color:#1A1A2E;">{{ $b[0] }}</div>
                            <div class="text-muted small">{{ $b[1] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center">
            <h3 class="fw-bold mb-3">Interested in partnering?</h3>
            <p class="text-muted mb-4">Tell us about your business and how you'd like to work with OneMember.</p>
            <a href="mailto:partners@onemember.co" class="btn btn-pink btn-pink-lg">Contact Partner Team <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

@endsection
