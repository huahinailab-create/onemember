@extends('layouts.corporate')

@section('title', 'Careers — OneMember')
@section('description', 'Join the OneMember team. Help us build the loyalty platform for Thai SMEs and ASEAN small businesses.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Careers</span>
        <h1>Build the future of Thai loyalty</h1>
        <p>Join a team on a mission to give every Thai small business the loyalty tools they deserve.</p>
    </div>
</section>

<section class="corp-section">
    <div class="container">
        <div class="row g-5 align-items-center mb-5">
            <div class="col-lg-6">
                <span class="section-eyebrow">Why OneMember</span>
                <h2 class="section-heading">Work that matters to real businesses</h2>
                <p class="section-sub mb-4">At OneMember, your work directly helps Thai shop owners grow their businesses and build relationships with their customers. Small team, big impact.</p>
                <div class="d-flex flex-column gap-3">
                    @foreach([
                        ['bi-lightning', 'Early stage', 'Join at the beginning and shape the product direction.'],
                        ['bi-people', 'Tight-knit team', 'Small, focused team where your contribution is visible every day.'],
                        ['bi-globe-asia-australia', 'ASEAN ambition', 'Thailand first — then ASEAN. Long-term opportunity.'],
                        ['bi-heart', 'Meaningful work', 'Real merchants depend on what we build.'],
                    ] as $v)
                    <div class="d-flex gap-3 align-items-start">
                        <div style="width:36px;height:36px;border-radius:8px;background:rgba(26,46,90,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $v[0] }}" style="color:#1A2E5A;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">{{ $v[1] }}</div>
                            <div class="text-muted small">{{ $v[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height:280px;border:1px solid rgba(26,46,90,0.08);">
                    <div class="text-center text-muted">
                        <i class="bi bi-people" style="font-size:4rem;color:#1A2E5A;opacity:0.2;"></i>
                        <p class="mt-2 small">Team photo coming soon</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Open Roles --}}
        <h2 class="section-heading mb-4">Open positions</h2>
        <div class="d-flex flex-column gap-3">
            @foreach([
                ['Full-Stack Developer (Laravel)', 'Engineering', 'Bangkok / Remote', 'Full-time'],
                ['Product Designer (Mobile-first)', 'Design', 'Bangkok / Remote', 'Full-time'],
                ['Merchant Success Manager', 'Customer Success', 'Bangkok', 'Full-time'],
                ['Growth & Marketing Lead', 'Marketing', 'Bangkok', 'Full-time'],
            ] as $role)
            <div class="d-flex align-items-center justify-content-between p-4 rounded-3" style="background:#ffffff;border:1px solid rgba(26,46,90,0.1);">
                <div>
                    <div class="fw-semibold" style="color:#1A1A2E;">{{ $role[0] }}</div>
                    <div class="text-muted small">{{ $role[1] }} · {{ $role[2] }} · {{ $role[3] }}</div>
                </div>
                <a href="mailto:careers@onemember.co?subject=Application: {{ urlencode($role[0]) }}" class="btn btn-sm btn-outline-navy">Apply</a>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 p-4 rounded-3" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <h4 class="fw-bold mb-2">Don't see a role that fits?</h4>
            <p class="text-muted mb-3">We're always interested in hearing from talented people who believe in what we're building.</p>
            <a href="mailto:careers@onemember.co" class="btn btn-pink">Send us your CV <i class="bi bi-send ms-1"></i></a>
        </div>
    </div>
</section>

@endsection
