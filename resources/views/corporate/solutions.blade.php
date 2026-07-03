@extends('layouts.corporate')

@section('title', 'Solutions — OneMember')
@section('description', 'OneMember loyalty solutions for Thai SMEs: points programmes, digital stamp cards, birthday automation, and member analytics.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Solutions</span>
        <h1>The loyalty solution that fits your business</h1>
        <p>Whether you run a single location or a growing chain, OneMember has a solution built for you.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        @foreach([
            ['Points Loyalty Programme', 'bi-lightning', false, 'Award points on every purchase. Members accumulate points and redeem them for rewards you define. The most flexible loyalty format for retail, F&B, and services.', ['Set your own earn rate (e.g. ฿10 = 1 point)', 'Unlimited reward tiers', 'Real-time point balance via QR card', 'Automatic reward triggers']],
            ['Digital Stamp Cards', 'bi-grid-3x3', true, 'Replace paper stamp cards with a digital version that never gets lost. Members earn stamps per visit or purchase. Perfect for cafés, salons, and service businesses.', ['Stamp per visit or per purchase', 'Custom completion rewards', 'Multiple stamp cards active simultaneously', 'Members check progress any time']],
            ['Birthday Automation', 'bi-balloon-heart', false, 'The single highest-converting loyalty feature in Thai retail. OneMember automatically awards birthday points or rewards — no manual effort required from you.', ['Auto-detect member birthdays', 'Configure bonus points or special rewards', 'Runs 24/7 without any manual action', 'Customers feel remembered and valued']],
            ['Member Analytics', 'bi-bar-chart', true, 'Go from zero customer data to a complete picture of who your members are, when they visit, and what drives them to return.', ['Active vs inactive member tracking', 'Top member leaderboard', 'Campaign performance metrics', 'Export for external analysis']],
        ] as $i => $sol)
        <div class="row align-items-center g-5 mb-5 {{ $i % 2 === 1 ? 'flex-row-reverse' : '' }}">
            <div class="col-lg-6">
                <div class="corp-feature-icon {{ $sol[2] ? 'corp-feature-icon-pink' : '' }}" style="width:60px;height:60px;font-size:1.625rem;">
                    <i class="bi {{ $sol[1] }}"></i>
                </div>
                <h2 class="section-heading mt-3">{{ $sol[0] }}</h2>
                <p class="section-sub mb-4">{{ $sol[3] }}</p>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    @foreach($sol[4] as $point)
                    <li class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill" style="color:#FF1585;font-size:0.875rem;"></i>
                        <span style="font-size:0.9rem;color:#334155;">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="mt-4">
                    <a href="{{ route('register') }}" class="btn btn-pink">Start Free Trial</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height:280px;border:1px solid rgba(26,46,90,0.08);">
                    <div class="text-center text-muted">
                        <i class="bi {{ $sol[1] }}" style="font-size:4rem;color:{{ $sol[2] ? '#FF1585' : '#1A2E5A' }};opacity:0.25;"></i>
                    </div>
                </div>
            </div>
        </div>
        @if($i < 3)<hr class="my-5" style="border-color:rgba(26,46,90,0.06);">@endif
        @endforeach
    </div>
</section>

<section class="corp-section corp-section-dark">
    <div class="container text-center">
        <h2 class="section-heading section-heading-light">Ready to find the right solution?</h2>
        <p class="section-sub section-sub-light mx-auto mb-4">Book a demo and we'll help you set up the loyalty solution that fits your business best.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('corporate.demo') }}" class="btn btn-pink btn-pink-lg">Book a Demo</a>
            <a href="{{ route('register') }}" class="btn btn-outline-navy btn-outline-navy-lg" style="border-color:rgba(255,255,255,0.35);color:#fff;">Start Free Trial</a>
        </div>
    </div>
</section>

@endsection
