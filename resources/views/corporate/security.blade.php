@extends('layouts.corporate')

@section('title', 'Security & PDPA — OneMember')
@section('description', 'OneMember is built with security and PDPA compliance at the core. Learn how we protect your merchant and member data.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Security & PDPA</span>
        <h1>Your data is safe with OneMember</h1>
        <p>Security and privacy are not features — they are the foundation. Built PDPA-compliant from day one.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4 mb-5">
            @foreach([
                ['bi-shield-lock', false, 'PDPA Compliance', 'OneMember is designed to comply with Thailand\'s Personal Data Protection Act. Consent is captured at member enrolment. Members can request data access or deletion at any time.'],
                ['bi-lock', true, 'Data Encryption', 'All data is encrypted in transit (TLS 1.2+) and at rest. Passwords are hashed using bcrypt. No plain-text credentials ever stored.'],
                ['bi-person-lock', false, 'Email Verification', 'All merchant accounts require verified email addresses before access is granted. This prevents fraudulent account creation.'],
                ['bi-building-lock', true, 'Multi-Tenant Isolation', 'Every merchant\'s data is strictly isolated. No cross-merchant data leakage is architecturally possible. All resource access is authorised at the query level.'],
                ['bi-credit-card', false, 'Secure Payments', 'Billing is handled by Stripe, a PCI-DSS Level 1 certified payment processor. OneMember never stores card numbers.'],
                ['bi-eye-slash', true, 'No Developer Tools in Production', 'Debug tools and developer routes are completely disabled in production. Multiple gates prevent accidental exposure.'],
            ] as $s)
            <div class="col-md-6 col-lg-4">
                <div class="corp-feature-card">
                    <div class="corp-feature-icon {{ $s[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $s[0] }}"></i>
                    </div>
                    <h4>{{ $s[2] }}</h4>
                    <p>{{ $s[3] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- PDPA Detail --}}
        <div class="corp-contact-card mb-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3 style="font-weight:700;color:#1A1A2E;">PDPA — Thailand Personal Data Protection Act</h3>
                    <p class="text-muted mb-3">OneMember is built to comply with the PDPA (พระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562). Key protections:</p>
                    <ul class="text-muted" style="line-height:2;">
                        <li>Explicit consent is obtained from every member at enrolment</li>
                        <li>Members can access their own data via their QR card portal</li>
                        <li>Merchants can process member data deletion requests from the dashboard</li>
                        <li>Data is processed only for the purpose stated at enrolment (loyalty programme operation)</li>
                        <li>Data is not sold or shared with third parties</li>
                        <li>Breach notification procedures are documented internally</li>
                    </ul>
                </div>
                <div class="col-lg-4 text-center">
                    <div style="width:80px;height:80px;background:rgba(26,46,90,0.08);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="bi bi-shield-check" style="font-size:2rem;color:#1A2E5A;"></i>
                    </div>
                    <p class="small text-muted">PDPA compliant since launch</p>
                </div>
            </div>
        </div>

        {{-- Responsible Disclosure --}}
        <div class="corp-contact-card">
            <h3 style="font-weight:700;color:#1A1A2E;">Responsible Disclosure</h3>
            <p class="text-muted mb-3">We take security vulnerabilities seriously. If you discover a security issue in OneMember, please report it responsibly.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="fw-semibold">What to report</h6>
                    <ul class="text-muted small">
                        <li>Authentication or authorisation bypasses</li>
                        <li>Cross-tenant data access</li>
                        <li>SQL injection or XSS vulnerabilities</li>
                        <li>Sensitive data exposure</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold">How to report</h6>
                    <p class="text-muted small mb-2">Email us at <a href="mailto:security@onemember.co" style="color:#1A2E5A;font-weight:600;">security@onemember.co</a></p>
                    <p class="text-muted small">We will acknowledge your report within 48 hours and aim to resolve critical issues within 7 days. We do not currently run a bug bounty programme, but we will publicly credit responsible disclosures if the reporter wishes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <h2 class="section-heading">Questions about security or privacy?</h2>
        <p class="section-sub mx-auto mb-4">Our team is happy to answer security questions or discuss data processing agreements for enterprise customers.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="mailto:security@onemember.co" class="btn btn-pink btn-pink-lg">Email Security Team</a>
            <a href="mailto:privacy@onemember.co" class="btn btn-outline-navy btn-outline-navy-lg">Privacy Enquiries</a>
        </div>
    </div>
</section>

@endsection
