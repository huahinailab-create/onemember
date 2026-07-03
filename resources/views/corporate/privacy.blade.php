@extends('layouts.corporate')

@section('title', 'Privacy Policy — OneMember')
@section('description', 'OneMember Privacy Policy. How we collect, use, and protect personal data in compliance with Thailand\'s PDPA.')

@section('content')

<section class="corp-page-hero">
    <div class="container">
        <span class="section-eyebrow" style="color:#FF1585;">Legal</span>
        <h1>Privacy Policy</h1>
        <p>Last updated: {{ date('d F Y') }}</p>
    </div>
</section>

<section class="corp-section">
    <div class="container" style="max-width:800px;">
        <div class="d-flex flex-column gap-5">

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">1. Introduction</h2>
                <p class="text-muted">OneMember Co., Ltd. ("OneMember", "we", "us") operates the OneMember loyalty platform at <strong>app.onemember.co</strong> and the corporate website at <strong>www.onemember.co</strong>. This Privacy Policy explains how we collect, use, and protect personal data in compliance with Thailand's Personal Data Protection Act B.E. 2562 (PDPA).</p>
                <p class="text-muted">By using our platform, you agree to the collection and use of information in accordance with this policy.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">2. Data We Collect</h2>
                <h6 class="fw-semibold mt-3">Merchant Data</h6>
                <ul class="text-muted">
                    <li>Business name, owner name, email address, phone number</li>
                    <li>Business type and industry</li>
                    <li>Billing information (processed by Stripe — we do not store card numbers)</li>
                    <li>Usage data: login times, feature usage, report activity</li>
                </ul>
                <h6 class="fw-semibold mt-3">Member Data (collected on behalf of Merchants)</h6>
                <ul class="text-muted">
                    <li>Name, phone number</li>
                    <li>Birthday (optional, required for birthday rewards)</li>
                    <li>Email address (optional)</li>
                    <li>Transaction history: points earned, rewards redeemed, stamps collected</li>
                </ul>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">3. How We Use Data</h2>
                <ul class="text-muted">
                    <li>To operate and deliver the OneMember loyalty platform</li>
                    <li>To send transactional emails (account verification, password reset, subscription notices)</li>
                    <li>To process payments via Stripe</li>
                    <li>To provide customer support</li>
                    <li>To improve our product based on aggregated, anonymised usage patterns</li>
                    <li>To comply with legal obligations</li>
                </ul>
                <p class="text-muted">We do not sell personal data to third parties. We do not use member data for advertising.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">4. Data Sharing</h2>
                <p class="text-muted">We share personal data only with trusted sub-processors necessary to deliver our service:</p>
                <ul class="text-muted">
                    <li><strong>Stripe</strong> — payment processing (PCI-DSS Level 1 certified)</li>
                    <li><strong>Resend / Amazon SES</strong> — transactional email delivery</li>
                    <li><strong>DigitalOcean</strong> — cloud infrastructure (Asia-Pacific region)</li>
                </ul>
                <p class="text-muted">All sub-processors are bound by data processing agreements.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">5. Your Rights (PDPA)</h2>
                <p class="text-muted">Under Thailand's PDPA, you have the right to:</p>
                <ul class="text-muted">
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Delete your data (right to erasure)</li>
                    <li>Withdraw consent at any time</li>
                    <li>Lodge a complaint with the PDPC (Personal Data Protection Committee)</li>
                </ul>
                <p class="text-muted">To exercise these rights, contact <a href="mailto:privacy@onemember.co" style="color:#1A2E5A;">privacy@onemember.co</a>.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">6. Data Retention</h2>
                <p class="text-muted">We retain merchant account data for the duration of the account plus 1 year after closure. Member transaction data is retained for the duration of the merchant account. You may request earlier deletion at any time.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">7. Security</h2>
                <p class="text-muted">All data is encrypted in transit (TLS 1.2+) and at rest. Passwords are hashed using bcrypt. We conduct regular security reviews. See our <a href="{{ route('corporate.security') }}" style="color:#1A2E5A;">Security & PDPA page</a> for details.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">8. Cookies</h2>
                <p class="text-muted">We use session cookies to keep you logged in and CSRF tokens to protect form submissions. We do not use advertising cookies or third-party tracking cookies.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">9. Changes to This Policy</h2>
                <p class="text-muted">We may update this policy from time to time. Material changes will be communicated by email to active merchants. The "Last updated" date at the top of this page reflects the most recent revision.</p>
            </div>

            <div>
                <h2 class="fw-bold" style="font-size:1.25rem;color:#1A1A2E;">10. Contact</h2>
                <p class="text-muted">For privacy enquiries: <a href="mailto:privacy@onemember.co" style="color:#1A2E5A;">privacy@onemember.co</a><br>
                For general enquiries: <a href="mailto:hello@onemember.co" style="color:#1A2E5A;">hello@onemember.co</a></p>
            </div>

        </div>
    </div>
</section>

@endsection
