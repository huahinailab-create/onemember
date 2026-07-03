@extends('layouts.corporate')

@section('title', 'Resources — OneMember')
@section('description', 'Guides, templates, and tools to help Thai merchants get the most from their OneMember loyalty programme.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Resources</span>
        <h1>Everything you need to succeed</h1>
        <p>Guides, templates, and best practices for running a great loyalty programme.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['bi-book', false, 'Getting Started Guide', 'Set up OneMember in 15 minutes. Step-by-step walkthrough for new merchants.', 'Download PDF', '#'],
                ['bi-qr-code', true, 'QR Code Display Templates', 'Print-ready QR code holder templates for your counter, window, and receipts.', 'Download Templates', '#'],
                ['bi-megaphone', false, 'Campaign Playbook', 'Proven loyalty campaign ideas for cafés, salons, retail, and more.', 'Download PDF', '#'],
                ['bi-shield-lock', true, 'PDPA Compliance Checklist', 'A checklist for Thai merchants to ensure their loyalty programme meets PDPA requirements.', 'Download PDF', '#'],
                ['bi-person-plus', false, 'Member Onboarding Script', 'A simple script for staff to explain your loyalty programme to new members.', 'Download Template', '#'],
                ['bi-bar-chart', true, 'Loyalty ROI Calculator', 'Estimate the revenue impact of your loyalty programme before you launch.', 'Use Calculator', '#'],
            ] as $r)
            <div class="col-md-6 col-lg-4">
                <div class="corp-feature-card h-100">
                    <div class="corp-feature-icon {{ $r[1] ? 'corp-feature-icon-pink' : '' }}">
                        <i class="bi {{ $r[0] }}"></i>
                    </div>
                    <h4>{{ $r[2] }}</h4>
                    <p class="mb-3">{{ $r[3] }}</p>
                    <a href="{{ route('corporate.contact') }}" class="btn btn-sm btn-outline-navy">{{ $r[4] }} <i class="bi bi-download ms-1"></i></a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <p class="text-muted">Resources are available to OneMember merchants. <a href="{{ route('register') }}" style="color:#1A2E5A;font-weight:600;">Start your free trial</a> to access all resources.</p>
        </div>
    </div>
</section>

@endsection
