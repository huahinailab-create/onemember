@extends('layouts.corporate')

@section('title', 'Industries — OneMember')
@section('description', 'OneMember loyalty platform for cafés, restaurants, salons, clinics, retail shops, car washes, and more. Designed for Thai SMEs across all industries.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Industries</span>
        <h1>Built for every type of Thai business</h1>
        <p>Any business with repeat customers can benefit from loyalty. Here's how OneMember works for your industry.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        @php
        $industries = [
            ['bi-cup-hot', 'Cafés & Coffee Shops', 'Turn your regulars into loyalty members. Reward every visit with stamps or points. Birthday bonus for your best customers.', ['Stamp card per visit', 'Double-stamp promotions', 'Birthday rewards', 'Top customer leaderboard']],
            ['bi-egg-fried', 'Restaurants & Food', 'Build loyalty around your dining experience. Points on spend, special rewards for frequent diners, birthday dinners.', ['Points per ฿ spent', 'Table visit tracking', 'Seasonal campaign promotions', 'Member-only specials']],
            ['bi-scissors', 'Beauty & Hair Salons', 'Keep clients coming back with stamp rewards after every service. Birthday bonus treatments drive repeat bookings.', ['Stamp per service visit', 'Reward: free treatment', 'Birthday bonus stamps', 'Client return tracking']],
            ['bi-hospital', 'Clinics & Wellness', 'PDPA-compliant loyalty for healthcare. Reward regular health check-ups and wellness visits. Patient data kept private.', ['PDPA consent built-in', 'Visit-based stamps', 'Wellness milestone rewards', 'Secure member data']],
            ['bi-bag', 'Retail Shops', 'Points on every purchase. Customers redeem for discounts or free products. Compete with the big retailers.', ['Points per ฿ spent', 'Discount rewards', 'Free product rewards', 'Multi-campaign capability']],
            ['bi-droplet', 'Car Wash & Auto', 'Digital stamp card replaces paper. After X washes, get a free wash. Never lose a stamp card again.', ['Stamp per wash', 'Free wash reward', 'Multiple car tracking', 'Recurring visit pattern']],
            ['bi-book', 'Tutoring & Education', 'Loyalty for ongoing learning relationships. Reward consistent attendance and long-term enrolment.', ['Attendance stamps', 'Term completion rewards', 'Referral tracking', 'Parent/student accounts']],
            ['bi-house', 'Hotels & Guesthouses', 'Reward repeat stays. Birthday upgrades. Long-stay bonuses. Build loyalty in Thailand\'s competitive hospitality market.', ['Stay-based points', 'Nights milestone rewards', 'Birthday room upgrades', 'Direct booking loyalty']],
        ];
        @endphp

        <div class="row g-4">
            @foreach($industries as $ind)
            <div class="col-md-6 col-lg-3">
                <div class="corp-industry-card h-100">
                    <div class="corp-industry-icon"><i class="bi {{ $ind[0] }}"></i></div>
                    <h4>{{ $ind[1] }}</h4>
                    <p class="mb-3">{{ $ind[2] }}</p>
                    <ul class="list-unstyled" style="position:relative;z-index:1;">
                        @foreach($ind[3] as $point)
                        <li class="d-flex align-items-center gap-2 mb-1" style="font-size:0.8rem;color:rgba(255,255,255,0.8);">
                            <i class="bi bi-check2" style="color:#FF1585;"></i> {{ $point }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="corp-section corp-section-alt">
    <div class="container text-center">
        <h2 class="section-heading">Don't see your industry?</h2>
        <p class="section-sub mx-auto mb-4">OneMember works for any business with repeat customers. If your customers come back, we can build loyalty around that.</p>
        <a href="{{ route('corporate.contact') }}" class="btn btn-pink btn-pink-lg">Talk to Us <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
</section>

@endsection
