@extends('layouts.corporate')

@section('title', 'Blog — OneMember')
@section('description', 'Loyalty programme insights, tips for Thai merchants, and OneMember product updates.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Blog</span>
        <h1>Insights for Thai merchants</h1>
        <p>Loyalty programme tips, industry insights, and OneMember product news.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['5 reasons Thai customers stop coming back — and how loyalty programmes fix them', 'Customer Retention', '3 min read', 'bi-people'],
                ['Points vs Stamps: which loyalty type is right for your business?', 'Strategy', '4 min read', 'bi-lightning'],
                ['PDPA and loyalty programmes: what Thai merchants need to know in 2026', 'Compliance', '5 min read', 'bi-shield-lock'],
                ['Birthday rewards: the highest-ROI loyalty feature for Thai SMEs', 'Features', '3 min read', 'bi-balloon-heart'],
                ['How a Bangkok café grew repeat visits by 40% with OneMember', 'Case Study', '6 min read', 'bi-cup-hot'],
                ['Setting up your first loyalty campaign: a step-by-step guide', 'How To', '5 min read', 'bi-list-check'],
            ] as $post)
            <div class="col-md-6 col-lg-4">
                <div class="corp-feature-card h-100">
                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="height:140px;">
                        <i class="bi {{ $post[3] }}" style="font-size:3rem;color:#1A2E5A;opacity:0.25;"></i>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill" style="background:rgba(26,46,90,0.08);color:#1A2E5A;font-size:0.7rem;font-weight:600;">{{ $post[1] }}</span>
                        <span class="text-muted" style="font-size:0.75rem;">{{ $post[2] }}</span>
                    </div>
                    <h4>{{ $post[0] }}</h4>
                    <p class="text-muted small">Coming soon — subscribe to be notified when we publish.</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 p-4 rounded-3" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <h4 class="fw-bold mb-2">Get notified when we publish</h4>
            <p class="text-muted mb-3">Subscribe to the OneMember newsletter for merchant tips and product updates.</p>
            <div class="d-flex gap-2 justify-content-center" style="max-width:400px;margin:0 auto;">
                <input type="email" class="form-control" placeholder="your@email.com">
                <button class="btn btn-pink" style="white-space:nowrap;">Subscribe</button>
            </div>
        </div>
    </div>
</section>

@endsection
