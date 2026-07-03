@extends('layouts.corporate')

@section('title', 'Features — OneMember')
@section('description', 'Explore all OneMember features: points, stamps, birthday rewards, analytics, member self-service, PDPA compliance, and more.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Features</span>
        <h1>Everything your loyalty programme needs</h1>
        <p>Purpose-built for Thai SMEs. Every feature earns its place — no bloat, no complexity.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        @php
        $features = [
            ['bi-lightning', false, 'Points Programme', 'Award points for every purchase. Set your own earn rate. Members redeem points for rewards you define. Works with any transaction amount.'],
            ['bi-grid-3x3', true, 'Stamp Cards', 'Classic stamp card, digitised. Set how many stamps for a reward. Members collect stamps per visit or per purchase — your choice.'],
            ['bi-balloon-heart', false, 'Birthday Rewards', 'Automatically award bonus points or a reward on a member\'s birthday. The most-loved loyalty feature in Thai retail.'],
            ['bi-gift', true, 'Reward Catalogue', 'Create as many rewards as you want: free drinks, discounts, vouchers, or special experiences. Set point thresholds for each.'],
            ['bi-phone', false, 'Member QR Cards', 'Every member gets a personal QR card link — no app required. They open it in any browser to check points, stamps, and rewards.'],
            ['bi-qr-code', true, 'QR Enrolment', 'Members join your loyalty programme by scanning your shop QR code. The whole sign-up takes under 60 seconds.'],
            ['bi-bar-chart', false, 'Analytics Dashboard', 'See active members, points issued, rewards redeemed, top members, and campaign performance — all in real time.'],
            ['bi-file-earmark-bar-graph', true, 'Reports & Exports', 'Download member lists, transaction history, and campaign reports as CSV for your own analysis.'],
            ['bi-people', false, 'Staff Accounts', 'Add team members to your account. Control who can record transactions, issue points, or view reports.'],
            ['bi-shield-lock', true, 'PDPA Compliance', 'Consent captured at enrolment. Members can request data deletion. Built to meet Thailand\'s PDPA requirements from day one.'],
            ['bi-translate', false, 'Thai & English', 'The merchant dashboard is available in Thai and English. Switch languages any time from your account settings.'],
            ['bi-megaphone', true, 'Campaigns', 'Run bonus point promotions, seasonal offers, and double-stamp events. Schedule start and end dates with automatic activation.'],
        ];
        @endphp
        <div class="row g-4">
            @foreach($features as $feat)
            <div class="col-md-6 col-lg-4">
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
    </div>
</section>

<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">All features. 30-day free trial.</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">No feature gating during your trial. Try every feature on the Professional plan — free for 30 days.</p>
        <a href="{{ route('register') }}" class="btn btn-pink btn-pink-lg">Start Free Trial <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</section>

@endsection
