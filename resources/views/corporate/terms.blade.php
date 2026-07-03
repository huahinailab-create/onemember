@extends('layouts.corporate')

@section('title', 'Terms of Service — OneMember')
@section('description', 'OneMember Terms of Service. The terms and conditions governing use of the OneMember loyalty platform.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Legal</span>
        <h1>Terms of Service</h1>
        <p>Last updated: {{ date('d F Y') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        <div class="d-flex flex-column gap-5">

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">1. Agreement to Terms</h2>
                <p class="text-muted">By accessing or using the OneMember loyalty platform ("Service"), operated by OneMember Co., Ltd. ("OneMember", "we", "us"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree, do not use the Service.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">2. Description of Service</h2>
                <p class="text-muted">OneMember provides a Software-as-a-Service (SaaS) loyalty platform that allows merchants to create and manage customer loyalty programmes including points, stamps, and rewards. The Service is available at <strong>app.onemember.co</strong>.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">3. Accounts</h2>
                <p class="text-muted">You must provide accurate information when creating an account. You are responsible for maintaining the security of your account credentials. Notify us immediately of any unauthorised access at <a href="mailto:security@onemember.co" style="color:#1A2E5A;">security@onemember.co</a>. We reserve the right to suspend accounts that violate these Terms.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">4. Free Trial</h2>
                <p class="text-muted">New merchant accounts receive a 30-day free trial on the Professional plan. At the end of the trial, your account will automatically move to the Free plan unless you subscribe to a paid plan. No credit card is required to start a trial.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">5. Payments & Billing</h2>
                <p class="text-muted">Paid subscriptions are billed monthly in Thai Baht (฿). Payments are processed by Stripe. You authorise us to charge your payment method on your billing date each month. Subscriptions auto-renew unless cancelled. We do not issue refunds for partial months unless required by law.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">6. Merchant Responsibilities</h2>
                <p class="text-muted">You are responsible for:</p>
                <ul class="text-muted">
                    <li>Obtaining valid PDPA consent from your loyalty members</li>
                    <li>The accuracy of your loyalty programme terms (points, rewards, expiry)</li>
                    <li>Honouring rewards and points you issue through the Service</li>
                    <li>Complying with all applicable Thai laws in your use of member data</li>
                    <li>Not using the Service for unlawful purposes</li>
                </ul>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">7. Acceptable Use</h2>
                <p class="text-muted">You may not: (a) use the Service to harm, defraud, or mislead others; (b) attempt to gain unauthorised access to any part of the Service; (c) reverse-engineer or resell the Service without written permission; (d) violate any applicable laws.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">8. Intellectual Property</h2>
                <p class="text-muted">All rights in the OneMember platform, including software, design, and trademarks, are owned by OneMember Co., Ltd. Your account data remains your property. We grant you a limited, non-exclusive licence to use the Service during your subscription.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">9. Limitation of Liability</h2>
                <p class="text-muted">To the maximum extent permitted by Thai law, OneMember shall not be liable for indirect, incidental, or consequential damages arising from your use of the Service. Our total liability to you shall not exceed the fees paid by you in the 3 months preceding the claim.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">10. Termination</h2>
                <p class="text-muted">You may cancel your account at any time from your subscription settings. We may terminate accounts that violate these Terms with or without notice. On termination, your data will be retained for 30 days before deletion, during which time you may request an export.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">11. Governing Law</h2>
                <p class="text-muted">These Terms are governed by the laws of Thailand. Any disputes shall be subject to the exclusive jurisdiction of the Thai courts.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">12. Changes to Terms</h2>
                <p class="text-muted">We may update these Terms from time to time. Material changes will be communicated by email at least 14 days before taking effect. Continued use of the Service after changes take effect constitutes acceptance.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">13. Contact</h2>
                <p class="text-muted">Questions about these Terms: <a href="mailto:hello@onemember.co" style="color:#1A2E5A;">hello@onemember.co</a></p>
            </div>

        </div>
    </div>
</section>

@endsection
