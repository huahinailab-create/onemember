@extends('layouts.corporate')

@section('title', 'FAQ — OneMember')
@section('description', 'Frequently asked questions about OneMember: setup, pricing, PDPA compliance, member management, and more.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">FAQ</span>
        <h1>Frequently asked questions</h1>
        <p>Everything you need to know about OneMember. Can't find your answer? <a href="{{ route('corporate.contact') }}" style="color:#FF1585;">Contact us.</a></p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        @php
        $categories = [
            ['Getting Started', [
                ['How quickly can I set up OneMember?', 'Most merchants are up and running within 15 minutes. Create your account, choose your loyalty type (points or stamps), set up your first campaign, and share your QR code with customers.'],
                ['Do I need technical skills?', 'No technical skills required. OneMember is designed for busy shop owners. If you can use a smartphone, you can use OneMember.'],
                ['Can I try it before paying?', 'Yes. All new merchants receive a 30-day free trial on the Professional plan. No credit card required.'],
                ['What devices does OneMember work on?', 'OneMember works on any device with a modern web browser — desktop, tablet, or smartphone. No app installation needed.'],
            ]],
            ['Members & Customers', [
                ['Do my customers need to download an app?', 'No. Customers get a personal QR card link. They open it in any browser — Safari, Chrome, LINE browser — to check points and rewards. No app download, no account creation.'],
                ['How do customers join my loyalty programme?', 'Customers scan your shop QR code (display it at your counter or on receipts). They fill in a simple form with their name, phone, and birthday. The whole process takes under 60 seconds.'],
                ['Can I import my existing customer list?', 'During the pilot phase, member import is handled manually by our onboarding team. Contact support@onemember.co and we will assist.'],
                ['What data do I collect about members?', 'Name, phone number, birthday (optional), and email (optional). All collected with explicit PDPA consent. You own your member data.'],
            ]],
            ['Loyalty & Campaigns', [
                ['Can I run both points and stamps at the same time?', 'Each loyalty programme uses either points or stamps — you choose when you set up. You can run multiple campaigns (e.g. double-point weekends) within your chosen type.'],
                ['How do birthday rewards work?', 'When a member\'s birthday arrives, OneMember automatically awards the bonus you configured — extra points, a reward, or both. It runs automatically, no action needed from you.'],
                ['Can I limit rewards to specific campaigns?', 'Yes. Each campaign has its own settings, including which rewards are available and for how long.'],
            ]],
            ['Billing & Pricing', [
                ['What happens after my 30-day trial?', 'You choose a plan. If you don\'t upgrade, your account moves to the Free plan (100 members, 1 campaign). Your data is never deleted.'],
                ['Is there a contract?', 'No. All paid plans are month-to-month. Cancel any time from your subscription settings.'],
                ['Do you offer annual billing?', 'Annual billing with a discount will be available at public launch. Contact sales@onemember.co to discuss early-adopter pricing.'],
            ]],
            ['Security & PDPA', [
                ['Is OneMember PDPA compliant?', 'Yes. OneMember is built to comply with Thailand\'s PDPA (พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล). Consent is collected at enrolment. Members can request data deletion.'],
                ['Where is my data stored?', 'Data is stored on servers in the Asia-Pacific region. We use industry-standard encryption in transit and at rest.'],
                ['Can members delete their data?', 'Yes. You can process member deletion requests from your member management dashboard.'],
            ]],
        ];
        @endphp

        @foreach($categories as $ci => $cat)
        <div class="mb-5">
            <h2 class="section-heading" style="font-size:1.5rem;margin-bottom:1.25rem;">{{ $cat[0] }}</h2>
            <div class="accordion corp-faq" id="faqCat{{ $ci }}">
                @foreach($cat[1] as $qi => $qa)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqQ{{ $ci }}_{{ $qi }}">
                            {{ $qa[0] }}
                        </button>
                    </h2>
                    <div id="faqQ{{ $ci }}_{{ $qi }}" class="accordion-collapse collapse" data-bs-parent="#faqCat{{ $ci }}">
                        <div class="accordion-body">{{ $qa[1] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="text-center mt-4 p-4 rounded-3" style="background:rgba(26,46,90,0.04);border:1px solid rgba(26,46,90,0.08);">
            <h4 class="fw-bold mb-2">Still have questions?</h4>
            <p class="text-muted mb-3">Our team is happy to help. Reach out and we'll reply within 1 business day.</p>
            <a href="{{ route('corporate.contact') }}" class="btn btn-pink">Contact Us</a>
        </div>
    </div>
</section>

@endsection
