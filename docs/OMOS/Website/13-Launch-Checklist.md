# 13 — Website Launch Checklist

> Gate: the website goes live when every 🔴 is done; 🟠 within 30 days of launch. One owner per line at execution time. This checklist assumes implementation happens in a future approved sprint — it is the *definition of done* for that sprint.

## Content 
- [ ] 🔴 All 13 blueprint pages written in final EN + native TH (no machine translation)
- [ ] 🔴 Home, Pricing, Features, 10 Industries copy-edited by a native Thai writer
- [ ] 🔴 All product screenshots current (post MR-001–003 UI), device-framed, both languages
- [ ] 🔴 Placeholder testimonial sections HIDDEN until ≥3 real pilot testimonials (no fake proof)
- [ ] 🔴 Every CTA points to the correct target (Start Free → register; LINE → OA; Enterprise → contact)
- [ ] 🟠 90-second demo video (TH, subtitled) live in hero overlay
- [ ] 🟠 Press kit downloadable; About founder content real

## SEO
- [ ] 🔴 Unique title + meta description per page, written natively per locale
- [ ] 🔴 hreflang th/en pairs + canonicals correct; XML sitemaps submitted (Search Console + Bing)
- [ ] 🔴 FAQPage schema on /faq; Organization schema sitewide; breadcrumbs marked up
- [ ] 🔴 Redirect map from any existing corporate URLs (no 404s from old links)
- [ ] 🟠 OG images per key page with Thai text (LINE/FB share preview tested in real LINE chat)
- [ ] 🟠 Rank tracking configured for the §10 keyword clusters

## Analytics & Tracking
- [ ] 🔴 Analytics property live; funnel events firing (hero_view … start_free_click) — verified on real phone
- [ ] 🔴 Website→app signup handoff attribution tested end-to-end (UTM survives registration)
- [ ] 🔴 UTM convention documented; all sales/poster/social links minted from it
- [ ] 🟠 Weekly funnel dashboard (even a sheet) wired to the SALES-001 KPI review
- [ ] 🟠 Consent-respecting analytics config matches Cookie Policy (no tracking before consent where required)

## Performance
- [ ] 🔴 Hero readable <1.5s on throttled 4G, mid-range Android (the actual audience device)
- [ ] 🔴 Core Web Vitals green (LCP/CLS/INP) on Home, Pricing, one industry page — mobile
- [ ] 🔴 Images responsive + lazy-loaded; hero image preloaded; total page weight budgeted (<1MB Home)
- [ ] 🟠 Fonts self-hosted incl. Thai face (INTERNATIONAL-001 Phase 1 item — do together)

## Security
- [ ] 🔴 HTTPS everywhere, HSTS; forms POST-only with CSRF; no mixed content
- [ ] 🔴 Security headers (CSP, X-Frame-Options, referrer-policy) on the marketing site
- [ ] 🔴 Contact forms rate-limited + spam-protected (honeypot > CAPTCHA)
- [ ] 🟠 /legal/security page live with responsible-disclosure contact

## Accessibility
- [ ] 🔴 Keyboard navigable; visible focus states (reuse product's :focus-visible standard)
- [ ] 🔴 All images alt-texted (localized); headings hierarchical; landmarks correct
- [ ] 🔴 Color contrast: body text ≥4.5:1 — brand pink not used for body-size text on white (MR-004 guideline)
- [ ] 🟠 Screen-reader pass of Home + Pricing (TH + EN); video captioned

## Localization
- [ ] 🔴 TH default for Thai visitors; switcher persistent; no mixed-language pages
- [ ] 🔴 Thai typography checked (line-breaking, font rendering on Android)
- [ ] 🔴 All dates/currency examples localized (฿, no US formats on TH pages)
- [ ] 🟠 Burmese landing page — ONLY at MM launch gate (do not pre-ship)

## Legal
- [ ] 🔴 Privacy, Terms, PDPA pages live and legal-reviewed (DR-33 closed)
- [ ] 🔴 Cookie banner honest, dismissible, remembered; policy matches actual cookies
- [ ] 🔴 Footer: legal entity, links to all legal pages; forms carry consent lines
- [ ] 🔴 In-app terms link and website terms identical version
- [ ] 🟠 Refund policy aligned with DECISION-014 billing reality

## Merchant Journey (the promise-keeping check)
- [ ] 🔴 A stranger with a phone can go website → Start Free → Launch Ready in ≤15 min (tested with 3 real non-team humans, in Thai)
- [ ] 🔴 Every claim on the site verified true in-product (10 minutes, 100 free, 2 taps, Thai UI, export)
- [ ] 🔴 Pricing page numbers match config/subscriptions grid exactly (DECISION-014)
- [ ] 🔴 LINE OA answers within promised window during launch week (staffed, not aspirational)
- [ ] 🟠 404 page, error pages styled + selling; /rewards-style dead ends: zero

## Launch-day runbook (abbrev.)
1. Freeze content 48h prior · 2. DNS/SSL verified · 3. Smoke-test all CTAs on real phone (TH + EN) · 4. Analytics live-checked · 5. Announce: LINE OA broadcast, FB post, pilot merchants get "we're public" message with share ask · 6. Monitor funnel + forms daily for week 1 · 7. Day-7 retro against §12 targets.
