## 2026-07-16 — INTEGRATION-001: Unified Beta Release Candidate

Integration sprint on `integration-001-beta-rc` (from main `20084eb`):
merged `website-002a-public-site` @ fc5b4b9 (WEBSITE-002A + polish),
then `release-001-beta-readiness` @ ca2313d (CUSTOMER-001A/B/C +
RELEASE-001). No features, no redesign.

- Conflicts resolved as unions (CSS append overlap — one swallowed
  closing brace restored; decision log 099+100–102 ordered; changelog
  interleaved; sprint board rebuilt; product state unified); generated
  Vite assets rebuilt from source, never hand-edited
- No application-code conflicts — the branch lines touch disjoint code
- 10 new cross-system integration tests (guard isolation both
  directions via real HTTP, slim corporate bundle vs app bundle,
  storefront → saved-address checkout → wallet order history, asset
  survival, branded 404, Thai on website + wallet)
- 991 tests green (2,762 assertions); build clean; route/view/lang/
  migration integrity verified; browser smoke clean at 375/768/1440
  in EN+TH for guest, customer, and merchant journeys
- Release blockers documented, unchanged: email-first OTP decision,
  production duplicate-email pre-check, customer self-join gap,
  order→points gap, infrastructure checklist, DECISION-014/DR-33.
  Not merged to main, not pushed, not deployed

## 2026-07-16 — RELEASE-001: Beta Readiness Audit

Cross-platform release audit on `release-001-beta-readiness` (stacked
on the CUSTOMER-001 stack). Safe fixes only; everything else documented
in the sprint report for CTO decision.

- og:image switched to PNG (SVG og:images render blank on LINE/FB);
  brand OG art PNG added with width/height meta
- /icons/icon-192.png + icon-512.png created (manifest and
  apple-touch-icon pointed at missing files); 0-byte favicon.ico
  replaced by a real /favicon.png linked from every layout
- manifest.webmanifest corrected to brand colors (#1A2E5A / #F0F0F4)
- robots.txt disallows /account
- Branded, localized (EN/TH), self-contained 403/404/419/500/503
  error pages
- .env.example documents CUSTOMER_SMS_PROVIDER and the
  no-production-SMS constraint
- Audit found: EN/TH lang parity 100%, no debug leftovers, no
  unguarded models, DevTools hard-gated off in production, CSP +
  security headers in place, honest pricing (฿0 + TBA), all corporate
  pages 200, sitemap OK. 949 tests green; build clean

## 2026-07-16 — CUSTOMER-001C: OneMember Wallet MVP

The customer's home inside OneMember — a long-term relationship hub,
not a points viewer. Branch `customer-001c-wallet-mvp`, stacked on
CUSTOMER-001B (001A/B/C reviewed together; not merged, not pushed).
DECISION-102; ADR-018.

- Architecture — WalletService read model aggregating ONLY over the
  customer's own consented CustomerMemberLinks: memberships, summary,
  rewards by merchant, chronological activity, order history.
  Cross-customer/merchant leakage impossible by construction; future
  features (gift cards, subscriptions, appointments, membership
  cards, notifications, AI recommendations) are new aggregate methods
  + nav items — no redesign
- Home — "Welcome back, {first name}", merchants joined, rewards
  available, memberships + activity previews, quick links, six
  honestly-labelled Coming Soon tiles (no fake functionality)
- My Places — merchant cards (logo/initials, member since, last
  visit, status) each with its OWN balance labelled points or stamps
  per the merchant's programme; balances never combined (tested)
- Membership detail (read-only) — balance hero, membership info,
  campaign, rewards with a visibly disabled Redeem (counter
  redemption explained), 10 recent transactions, merchant contact,
  storefront link when Commerce is installed
- My Rewards — grouped by merchant; real statuses only (Available /
  Coming soon — the domain has no reward expiry, none invented)
- Activity — joins, earn/redeem/adjust/expire/birthday, orders;
  newest first
- My Orders — items, total, status, delivery-address snapshot used,
  Order-again link; orders placed signed-in now carry customer_id
  (guest orders stay NULL and untouched)
- Preferences — customers.preferences JSON: communication channel +
  marketing consent; notifications placeholder; extensible without
  further migration
- UX — scrollable pill nav (44px targets, aria-current), wider wallet
  shell, login/OTP/reset land on the wallet, reduced-motion support,
  empty states everywhere, EN + native TH (~110 keys each)
- Tests — 18 new; 949 total green, 2525 assertions; build clean

## 2026-07-15 — CUSTOMER-001B: Customer Saved Addresses & Checkout Foundation

The customer's permanent address book — one customer, many merchants,
one address book. Branch `customer-001b-saved-addresses`, stacked on
CUSTOMER-001A per CTO instruction (both reviewed together; not merged,
not pushed). DECISION-101; ADR-017. Not a delivery form: the same
architecture serves food delivery, retail shipping, service
appointments, hotel delivery, and future Wallet capabilities.

- Address book — unlimited labelled addresses (suggested labels +
  custom), recipient + contact phone (E.164-normalized), exactly one
  default among active addresses; create/edit/rename, delete (soft),
  archive/restore, duplicate, search, set default. Deleting or
  archiving the default promotes the most recently used active address
- Country model — administrative areas stored generically
  (admin_area_1 largest → admin_area_4 smallest); per-country field
  names, required fields and postcode patterns in
  config/customer_address.php (TH: province/district/subdistrict;
  MM: state-region/district/township/ward-village). A new country is
  one config entry — no migration, no code change. Lat/lng columns
  nullable, never required (reserved for future GPS work)
- Checkout — signed-in customers see "Deliver to" with their active
  addresses (default first) or "Add new address" (full country-aware
  form + optional save-to-book); guests keep the existing free-text
  address field untouched; works without JavaScript
- Merchant privacy by construction — orders store only a plain-text
  snapshot of the chosen address in the existing orders.address column
  (no FK, no uuid, no orders schema change): merchants can never
  traverse into the book; later book edits never rewrite a received
  order; chosen addresses must be the signed-in customer's own and
  active (foreign addresses 404 / fail validation without confirming
  existence)
- Security — all 10 book routes behind the customer guard; merchant
  web sessions and guests redirected; sanity cap on book size;
  country-specific validation with length caps
- Tests — 32 new (18 address book, 14 checkout incl. guest-unchanged
  and privacy-boundary proofs); FakeSmsProvider extracted to
  tests/Support so CUSTOMER-001A files pass standalone. 931 total
  green, 2465 assertions; build clean

## 2026-07-15 — CUSTOMER-001A: OneMember Identity Foundation

Customer identity foundation — the beginning of the future OneMember
Wallet. Branch `customer-001a-identity-foundation` (off main `20084eb`;
awaiting CTO review, not merged, not pushed). "One person. One identity.
Many merchants." Merchant authentication is completely untouched; guest
checkout remains possible. DECISION-100; ADR-016.

- Identity — the existing PH2-001A `customers` row becomes the account
  (no parallel identity, no member-record migration). Additive fields:
  first/last/nickname/display names, country, timezone, nullable
  password, remember token, email/phone verified timestamps, last
  login, status. Two documented schema relaxations: `phone` nullable
  (email-only accounts) and `email` unique (now a login identifier;
  production must pre-check duplicates)
- Authentication — new `customer` session guard + provider; sign in
  with mobile phone OR email via OTP OR password (customer chooses,
  single form, no-JS formaction branch). Passwords optional at
  registration; OTP-only accounts can add one later
- OTP — `OtpService`: bcrypt-hashed 6-digit codes, 5-minute expiry,
  5-attempt kill, single-use, supersession, 60s resend cooldown +
  5/hour per destination. Delivery via `SmsProvider` contract
  (LogSmsProvider only — no production SMS integration, no fake
  sending) or synchronous email mailable
- Security — named rate limiters (login lockout, OTP request/verify,
  registration), account-existence never leaked on login-side paths,
  session regeneration, `Password::defaults()` (12+ mixed), suspended
  status gate, E.164 normalization (TH +66, MM +95, config-driven).
  SecurityEventSubscriber fixed to identify Customer logins (was
  merchant-User-only and crashed on phone-only customers)
- Profile & settings — names/birthday/language self-service; password
  add/change; email/phone change applies ONLY after OTP verification
  sent to the NEW destination (the destination IS the pending value —
  no pending columns)
- Future seams — `IdentityProvider` contract for Apple/Google/LINE/
  Facebook later (architecture only, zero implementations, no
  customer_identities table until the first real provider)
- UI — customer layout + 8 Blade views (login, register, verify,
  forgot, reset, profile, settings, confirm), Bootstrap 5, OneMember
  branding, EN + native TH (~100 keys each)
- Tests — 45 new (7 unit phone normalization, 38 feature auth/account);
  899 total green, 2370 assertions; production build clean

## 2026-07-15 — WEBSITE-002A Polish: World-Class Pass (branch, not merged)

Approved follow-up on `website-002a-public-site`: performance, accessibility, SEO, trust, content, and code-quality polish on the existing 8 pages. No new pages, no redesign.

**Performance**
- New Bootstrap-only `resources/js/corporate.js` Vite entry for all marketing pages: **~54 KB gz → ~24 KB gz JS per page view (−55%)**. Alpine no longer boots and the 42 KB Cropper chunk is no longer preloaded on pages that can never use them.

**SEO (two real defects fixed)**
- `og:image` pointed at an SVG — LINE/Facebook/Twitter render SVG share images as **blank**; replaced with a generated 1200×630 PNG (GD, on-brand navy/pink wordmark + tagline). Added `twitter:image` and `og:locale`.
- `robots.txt` has advertised `Sitemap: https://onemember.co/sitemap.xml` since RELEASE-1B — **the URL 404'd for crawlers the whole time**. Added the route + `CorporateController::sitemap()` generating valid XML from the same named routes the site links to.

**Accessibility**
- Skip-to-content link (first tab stop, styled on focus) + `<main id="corp-main">` landmark — page content previously sat directly in `<body>` with no main landmark.
- FAQ accordion headers demoted h2→h3 on Home and Pricing (heading hierarchy).
- `prefers-reduced-motion` now disables all corporate transitions/animations.
- 44 px minimum touch targets for small nav/FAQ-category buttons below 992 px (WCAG 2.5.8).

**Trust (§8)**
- Hero dashboard mockup no longer displays figures that read as marketing statistics ("1,247 Active Members / 89% Retention Rate" → a small shop's day view: 128 members / 12 visits today) and is `aria-hidden` as the decorative illustration it is.

**Content**
- Primary CTA now reads **"Start Free"** sitewide per the blueprint (was "Start Free Trial"); Thai เริ่มใช้ฟรี.
- Fixed mismatched hero stat pairing ("PDPA-ready" value under a "No card needed" label → "Thai privacy law").
- Terminology audit: no banned voice words (leverage/cutting-edge/AI-powered/synergy), "programme" used consistently (21×, zero "program"), meta titles all follow "Page — OneMember".

**Code quality**
- 22 duplicated `style="color:#FF1585"` inline styles replaced with the design-system `text-pink` utility across all corporate views.

**Tests:** +6 regressions (sitemap XML validity + page coverage, PNG og-image exists and is referenced, slim bundle loads / Cropper chunk never leaks onto marketing pages, skip link + landmark, mockup carries no statistic-like figures, CTA naming both locales). **880 → 886 green.** Build clean. Verified in-browser at 375/390/414/768/1024/1440 in EN and TH, including nav toggle, dropdowns, and accordions running on the slim bundle.

**Known limitations:** hreflang pairs still not emitted (locale is session-based, not URL-based — needs the `/th/` URL scheme from the SEO blueprint, a future sprint); Lighthouse not run in-sandbox (no Chrome audit tooling) — LCP/CLS reasoning is structural (text-first hero, no webfonts on corporate pages, fixed-size mockup, no images above the fold).

## 2026-07-14 — WEBSITE-002A: Public Marketing Website MVP (branch, not merged)

Implements the approved WEBSITE-001 blueprint against the **existing** onemember.co corporate site rather than building a parallel one. On branch `website-002a-public-site`, awaiting CTO review — not merged to `main`.

**Shipped** — repositioned to "Relationships Matter" merchant-growth framing (never "just loyalty software"):
- **Home** — hero, problem ("the quiet Tuesday"), solution, industries teaser, features, pricing teaser, FAQ teaser, final CTA all rewritten to the approved voice.
- **Features** — 8 outcome-first cards (Members, Campaigns, Rewards, Commerce, Storefront, Launch Kit, Insights, Knowledge Center) replacing settings-list language.
- **Industries** — rebuilt to the exact 10 blueprint segments (Coffee Shops, Restaurants, Hair Salons, Nail Salons, Massage & Spa, Hotels, Retail, Fashion, Pet Shops, Beauty Clinics), each with hook + campaign recipe.
- **Pricing** — Free/Starter/Professional/Enterprise; Starter/Professional correctly show `TBA` (DECISION-014 unresolved, never a fabricated number), Enterprise shows `Custom`/"Talk to us", unshipped features (white-label, multi-branch, corporate controls) now labeled `(planned)`.
- **About** — founding story, mission, 4 founder credos, Thailand + Myanmar (dateless, partner-led) replacing an invented Vietnam/Malaysia/Philippines timeline that was never in any approved doc.
- **FAQ** — 34 curated questions across 9 categories (of the approved 100), sticky category nav, accessible accordion (`aria-expanded`), `FAQPage` structured data.
- **Contact** — LINE-first six doors (Sales/Support/Partnerships/Media/Investors), honest "usually within 2 business hours" promise, client-side `mailto:` form (was promising "1 business day" from a form with no backend).
- **Resources** — Knowledge Center entry point (teaser card → sign in; full in-app manual not exposed publicly this sprint).
- Sitewide: `Organization` JSON-LD, per-page canonical/OG/Twitter meta, mobile-nav `aria-label`, `services.line.oa_url` config gates every LINE CTA (unset — no LINE ID exists, none invented).

**Found and fixed in already-live code** (not introduced this sprint):
- Home page unconditionally rendered 3 fabricated testimonials (fake shop names/quotes/a "40% more often" stat) — violates WEBSITE-001's explicit "no fake quotes, ever." Now gated behind an `is_array()` check on `corporate.home_testimonials`; ships hidden until real Founding Merchant quotes exist.
- Hero claimed "2 min" setup, inconsistent with the approved "10 minutes" proof spine.
- A literal `'@context'` string inside inline Blade PHP is mis-parsed as Laravel's `@context` directive (Laravel 11+ `Context` facade), silently corrupting JSON-LD into invalid HTML — worked around via string concatenation.
- Bootstrap `.row` negative-gutter margins caused real horizontal overflow at 375px — fixed with `overflow-x:hidden` on `.corp-body`.

**Tests:** new `WebsiteMvpTest` (26 tests: all 8 pages 200 for guests in both locales, no dead corporate.* links, no unapproved prices, FAQ/SEO/a11y checks, authenticated app routes unaffected). 3 pre-existing tests updated to match intentionally-changed copy. 878 → 880 tests green. Build clean.

**Deferred / missing before public launch** (see DECISION-099):
- `LINE_OA_URL` not yet provisioned — every LINE CTA is currently invisible until configured.
- Legal pages (`/privacy`, `/terms`, `/pdpa`, `/security`) exist from an earlier sprint but per WEBSITE-001 "ship only after legal review (DR-33)" — content not touched or re-reviewed this sprint.
- No 90-second demo video, no press kit, no native-Thai-writer copy-edit pass (Thai content here is fluent but not professionally reviewed) — all explicitly gated 🟠/🔴 in the blueprint's own Launch Checklist (§13), not blockers for this MVP implementation sprint.
- Remaining 66 of the 100 approved FAQ questions not published (by design — MVP scope).
- Real Founding Merchant testimonials, pilot logos, and demo video remain placeholders until pilot data exists.

## 2026-07-10 — WEBSITE-001: Public Website Master Blueprint

Marketing/UX-writing documentation sprint (final documentation
assignment before pilot merchant acquisition; no implementation).
New folder docs/OMOS/Website/ — 13 documents designing the complete
public site with one objective: convert visitors into merchants.

- 01 Strategy — merchant-GROWTH positioning (never "just another
  loyalty system"): "delivery apps rent you customers; OneMember helps
  you keep yours"; voice/tone system; Thai written natively, never
  translated; anti-goals (no gated PDFs, no dark patterns)
- 02 Site Map — shallow phone-first nav, Start Free always one tap,
  full URL tree incl. 10 industry + 8 feature pages, footer, 404 rules
- 03 Home Page — 10 sections with headline/subhead/body/CTAs/visual
  direction/trust elements per section; real-screenshot rule;
  testimonials hidden until real (no fake proof, ever)
- 04 Features — 9 outcome pages ("Know every regular by name", "Sell
  more, commission-free"); business outcomes only, zero software-speak
- 05 Industries — 10 landing pages: problems in the owner's words,
  campaign recipes from SALES-001, story placeholders, per-page CTAs
- 06 Pricing — value story per tier (Free "prove it works" →
  Enterprise "your brand, your network"); honest comparison table;
  amounts remain DECISION-014 placeholders
- 07 About — founding story, mission/vision, 5 founder credos
  (free must be real; your data is yours; simple is respect)
- 08 FAQ — 100 questions in 11 groups (pricing, setup, security,
  loyalty, commerce, payments, storefront, languages, Thailand,
  Myanmar, Enterprise); legal-gated answers flagged ⚖️
- 09 Contact — six doors (sales/support/partnerships/media/investors/
  merchant success), LINE-first, kept response-time promises
- 10 SEO — Thai-first keyword clusters (ระบบสะสมแต้ม + industries),
  hub-and-spoke internal linking, 4 content pillars, Myanmar
  deliberately light (Facebook > Google there)
- 11 Legal — 7 required pages inventoried with priorities, all gated
  on DR-33; plain-language-summary format
- 12 Conversion Funnel — Visitor→Learn→Trust→Start Free→Launch Ready→
  Upgrade→Refer with drop-off risks and counters per stage; the real
  conversion is Launch Ready, not signup; golden rule: every website
  promise provably true in-product within 10 minutes
- 13 Launch Checklist — gated 🔴/🟠 checklist across content, SEO,
  analytics, performance (hero <1.5s on 4G), security, accessibility,
  localization, legal, and the merchant-journey promise-keeping test

## 2026-07-10 — SALES-001: OneMember Sales Operating System

Business documentation sprint (final assignment of this development
cycle; no software). New folder docs/OMOS/Sales/ with the complete
sales operating manual — detailed enough for a new salesperson to sell
OneMember after reading it:

- 01 Sales Playbook — philosophy (sell repeat customers, demo is the
  pitch, Free is a real answer, honesty compounds), consultative rules,
  9-stage process with exit criteria, cycle-length expectations per
  persona, daily activity targets (10 conversations / 3-4 demos)
- 02 Ideal Customer Profiles — 10 segments with size, owner, pains,
  triggers, modules, red flags + universal qualify-out rules
- 03 Discovery Questions — 3-question street qualifier + 12-topic
  framework (loyalty, repeats, marketing, FB, LINE, POS, database,
  challenges, goals, budget-indirect, decision makers, next steps)
- 04 Industry Playbooks — per-industry mechanics (points vs stamps),
  reward ladders, birthday/referral/seasonal patterns, launch sequence,
  week-one quick wins
- 05 10-Minute Demo Script — minute-by-minute beats: customer scan →
  Counter Mode → win-back reveal → storefront/Launch Kit fork →
  Free-100 close with on-the-spot signup
- 06 Objection Handling — 8 core + 5 bonus objections
  (acknowledge/reframe/evidence/advance) + graceful walk-away criteria
- 07 ROI Calculator — formulas only + 3 worked examples; break-even-
  visits framing; prices remain DECISION-014 placeholders
- 08 Follow-Up Playbook — day 1/3/7/14/21/30 cadences for trials and
  prospects; LINE-first channel rules; max-5-touches rule
- 09 Pilot Merchant Program — Founding Merchants operating manual:
  the exchange, weekly review ritual ("what almost made you stop?"),
  feedback routing to CTO, case-study/testimonial production,
  graduation, referral incentives, tripwires
- 10 Sales KPIs — daily/weekly/monthly tables, funnel math proving
  one seller ≈ 100 activated merchants in ~4 months, anti-gaming rules

## 2026-07-10 — GO-TO-MARKET-001: Merchant Acquisition Strategy

Business-strategy sprint, documentation only (no software). New:
docs/OMOS/Roadmaps/GO-TO-MARKET-001.md — the complete GTM plan for the
first 100 merchants.

- Vision & positioning: "delivery apps rent you customers; OneMember
  helps you keep yours"; UVP anchored to the built 10-minute guided
  launch journey
- Markets: Thailand direct/founder-led (all first-100 effort); Myanmar
  partner-led and pilot-gated per INTERNATIONAL-001; LA/KH/VN/MY/SG
  sequenced
- 10 merchant segments mapped (size, current loyalty, pains, triggers,
  rejections, modules); beachhead = coffee shops + hair/nail salons
- 4 personas (owner-operator, hired manager, multi-branch owner, ops
  manager) with decision processes and sales approaches
- Pricing positioning for Free/Starter/Professional/Enterprise
  (Free-100 as distribution; trial = Professional; Enterprise =
  chains/white-label/corporate controls); amounts stay DECISION-014
- 9-stage sales journey wired to existing product telemetry (launch
  checklist = activation, MerchantHealthService = churn signals)
- 14 channels evaluated: street cold outreach + LINE + Facebook core;
  email de-prioritized; POS vendors strategic
- Thailand playbook (street-cluster demos, LINE-first comms, objection
  battle-card, scripted 10-minute demo) and Myanmar entry (exclusive
  Yangon reseller, Burmese-first support, gated not dated)
- Founding Merchant pilot: 20 merchants, 8 weeks, success criteria,
  feedback/support processes, 5 case studies + video testimonials
- Sales toolkit inventory (exists / needs build), KPI ladder for
  30 days / 100 / 1,000 merchants, 90-180-365-day roadmap
- Revenue blockers surfaced: DECISION-014 (pricing) + DR-33 (terms
  legal review) gate charging anyone

## 2026-07-10 — INTERNATIONAL-001: Thailand + Myanmar Readiness Blueprint

Documentation-only architecture & product planning sprint (MERCHANT-READY-001
declared COMPLETE by CTO). New: docs/OMOS/Roadmaps/INTERNATIONAL-001.md —
no code, no schema, no routes, no features.

- Country strategy: TH (ready) → MM (this blueprint) → LA → KH → VN → MY → SG,
  with the rule that Phase 2 machinery makes markets 3–7 config + content only
- Address architecture per country: postcode required/optional/HIDDEN (Myanmar
  hides it), Township replaces District for MM, tier/label profiles for
  TH/MM/LA/KH/VN/MY/SG — config-first, no migrations planned
- Language strategy per surface (merchant UI 100%-or-nothing policy; customer
  surfaces translate first; Burmese support staffing is a launch gate)
- Currency strategy: billing vs display separated (ADR-011); per-currency
  decimal rules flagged as first Phase 2 item (MMK/VND/LAK/KHR/JPY = 0 dp)
- Date/time: Buddhist-Era as TH display-only preference; Gregorian for MM;
  Asia/Yangon +06:30 confirmed handled
- Typography: Unicode-only Burmese strings, self-hosted Noto Sans Thai/Myanmar
  via unicode-range, Zawgyi detection strategy
- Payments: Stripe kept global; Omise/PromptPay recommended evaluation for TH
  billing; 2C2P deferred to market #3; MMK billing options (USD/manual/partner)
  deferred to MM pilot; storefront QR model confirmed provider-agnostic
- Legal: PDPA + DR-33 (TH, on the clock), MM data/sanctions screening areas
- 3-phase roadmap + 8 ranked risks (Zawgyi, MMK rails, sanctions top the list)

## 2026-07-10 — MERCHANT-READY-001 / MR-004: Merchant Readiness Audit

Senior-QA/UX and production-readiness audit of the entire merchant
experience. No features, no business logic, no architecture, no schema —
small safe fixes only; full findings in
docs/OMOS/Sprints/MERCHANT-READY-001.md (audit summary).

- **Localization** — the last 8 raw-English success flashes (member/
  campaign/reward update, archive, pause, configure) and 3 plan-limit
  errors now use their existing EN/TH messages.* keys; hardcoded
  alt="QR Code" localized; duplicate qr_coming_soon lang key removed.
  Scripted check: EN↔TH key parity is 100% across all lang files.
- **Accessibility** — reward-search button (icon-only) and input gained
  accessible names; earn-method read-only select associated with its
  label; x-ui.media-upload's native fallback file input gained an
  aria-label (shared component — fixes every media upload form).
- **Mobile** — real-browser sweep of ~20 merchant pages at 375px and
  768px (logged-in merchant with data): zero horizontal overflow
  anywhere; desktop verified during MR-001–003.
- **Consistency** — page titles all localized slots; no dead links or
  view TODOs; button/CTA/help-button patterns consistent post MR-002/3.
- **International readiness (documented only)** — Thailand READY.
  Myanmar partially ready: MM/MMK/Asia yangon +06:30/Burmese-offer all
  in place, but merchant UI is EN/TH-only, Zawgyi-vs-Unicode input risk,
  MMK shows 2 decimals, Latin-only webfont. Recommendations recorded
  (per-currency decimals, lang/my translation, self-hosted Myanmar
  Unicode font, phone normalization, per-market legal review).
- Known recommendations left open: skip-to-content link; brand pink not
  for body-size text on white (≈3.7:1); BE-date display option for TH.

## 2026-07-09 — MERCHANT-READY-001 / MR-003: Merchant Onboarding Experience

Onboarding becomes a guided launch journey: a merchant never has to ask
"what should I do next?". UX only — wizard business rules, validation,
trial start and starter-campaign logic untouched; no new architecture.

- **Step-success guidance** — completing a launch step (profile, logo,
  store URL, first product/campaign/reward/member) now shows, under the
  success message: why this matters + the ONE next recommended action
  (new `<x-launch.step-success>`, rendered once globally under the flash;
  driven by the same deterministic LaunchChecklistService — no AI).
  When the step finishes the checklist, it celebrates and points to the
  dashboard instead.
- **Localized success flashes** — Part 1 friction find: member/campaign/
  reward/profile create-flashes were raw English while EN+TH keys already
  existed in messages.php; they now use them.
- **Progress experience** — checklist card adds encouraging steps-left
  copy ("Just 1 step to go — you're almost there!"); done items carry a
  screen-reader "completed" suffix.
- **First-launch celebration** — at 100% the checklist becomes a calm 🎉
  "Congratulations! Your business is now ready to welcome customers."
  with quick actions: View storefront, Print QR poster, Add a customer,
  Read the merchant guide. No animations.
- **Onboarding handoff** — the wizard finish page's primary CTA is now
  "See your launch plan" (dashboard); Launch Kit and Add member stay as
  secondary actions.
- tests: +7 (GuidedLaunchJourneyTest — why+next after each step, final-
  step celebration, quick actions, Thai guidance, finish handoff);
  854 green. Verified in-browser at 375px (EN + TH): step-success after
  campaign creation, celebration grid (76px touch targets, no horizontal
  scroll), ARIA progressbars, aria-hidden decorative emoji.

## 2026-07-09 — MERCHANT-READY-001 / MR-002: Empty States & Contextual Help

Reduce merchant confusion: every page explains what to do when there is no
data, and the Knowledge Center is one tap away from wherever the merchant is.
No business-logic changes; no new architecture.

- **`<x-ui.empty-state>` evolved** — new `help-topic` prop renders a
  contextual "Learn how in the Help Center" link; body width/typography
  unified in the component (per-view inline styles removed).
- **Empty states upgraded** — Members, Campaigns (real CTA button instead of
  an inline text link), Products, Orders (new "View My Store" CTA — orders
  come from the storefront), and the campaign Rewards tab (converted from
  hand-rolled markup to the design-system component). Copy pass: encouraging,
  non-technical, EN + TH.
- **Dead ends removed** — `/rewards` no longer shows hardcoded-English
  "Coming Soon": it explains rewards live inside campaigns and routes there.
  The shared placeholder view (Reports, Transactions) is localized, friendly,
  and always offers a way back to the dashboard.
- **Contextual help on all 8 primary screens** — ? buttons added to
  Dashboard, Rewards (campaign tab + landing), Orders and Settings, joining
  the existing Members, Campaigns, Products and Launch Kit buttons. New
  `rewards` context on the `create-rewards` article.
- **No dead help links, enforced** — a regression test scans every literal
  `topic=`/`help-topic=` in Blade views and fails when a topic doesn't
  resolve to a published article.
- tests: +10 (EmptyStateExperienceTest 8, MerchantHelpContentTest +2);
  847 green. Verified in-browser (members/rewards/orders, EN, mobile width).

## 2026-07-09 — MERCHANT-READY-001 / MR-001: Merchant Launch Dashboard

Help every new merchant understand exactly what to do next. No new platform
architecture, no new business modules — evolves the LAUNCH-001 checklist
(`LaunchChecklistService`, same `launch_flags` mechanism) to the full launch
path. Sprint umbrella defined in docs/OMOS/Sprints/MERCHANT-READY-001.md.

- **Launch Checklist** — reusable tenant-scoped `<x-launch.checklist>`:
  Business Profile, Logo, Store URL, First Product*, First Campaign,
  First Reward, First Member, QR Poster viewed, Storefront visited*
  (* = shown when the Commerce App is installed, per LAUNCH-001 precedent).
  Completed count, progress %, visual progress bar, Launch Ready badge at 100%.
- **Next Recommended Action** — the dashboard recommends exactly ONE next
  action: first incomplete item in fixed priority order. Deterministic —
  no AI, no randomness.
- **Merchant Health Card** — `<x-launch.health-card>` with green/amber/red
  per dimension (Profile, Logo, Store URL, Products, Campaigns, Members,
  Storefront, Launch %).
- **Tracking flags** — `qr_poster_viewed` set when the Launch Kit poster is
  opened; `storefront_visited` set when the merchant views their own public
  storefront (never another merchant's — tenant isolation tested).
- Superseded checklist items "Open Launch Kit" / "Try Counter Mode" removed
  from the list; their flags continue to be recorded.
- Mobile responsive; ARIA on progress bar and status indicators; EN + TH.

## 2026-07-09 — MERCHANT-READY-001: Help Center & User Manual

- content: 47 English merchant articles (Getting Started, Members, Loyalty,
  Commerce, Launch Kit, Settings, Troubleshooting, 8 Industry Quick Starts)
  + 6 Thai Getting Started articles; short, step-by-step, product-accurate
- system: articles live as git-versioned markdown (database/seeders/knowledge/)
  imported by an idempotent KnowledgeArticleSeeder (front-matter parser);
  Help Center category ordering fixed (Getting Started → … → Quick Starts)
- surfacing: Help Center added to the merchant sidebar; contextual ? buttons
  on Members, Campaigns, Products and Launch Kit resolving to their articles
- tests: MerchantHelpContentTest (8) — seeder import/idempotency, category
  order, markdown rendering, search, Thai + fallback, guest auth, context
  resolution, sidebar link

## 2026-07-09 — merge: integrate merchant readiness and OMEGA platform work

Integration of two parallel lines into main. Both feature sets preserved:
fable-dev (PLATFORM-002 foundation, OMEGA-001A/B image UX + polish,
MERCHANT-READY-001 manual) and origin/main (OMEGA-001C Media Foundation,
reusable media-upload frontend + CSP blob fix, OMEGA-001D polish,
OMEGA-001E Store Identity). Reconciliations: ProductController now uses
MediaService; fable-dev's GD optimizer became the bound ImagePipeline
(GdImagePipeline — WebP ≤1200px, replacing the inert NullImagePipeline
default); product form keeps OMEGA-001B section grouping around the
reusable x-ui.media-upload; cropperjs pinned to ^1.6.2 (the surviving
frontend's API).

## 2026-07-08 — OMEGA-001E: Store Identity & Public URL Foundation

Formalizes Business Name and Store URL as two distinct merchant identities. Reuses the existing `merchants.slug` column — no migration, no existing route changed, no breaking changes. Spec marks this as the final platform architecture sprint before Merchant Readiness.

- **`App\Services\StoreIdentity\StoreIdentityService`** — the one place that generates (`uniqueSlugFor()`), validates (`isReserved()`, `isAvailable()`), and resolves (`publicStoreUrl()`) a Store URL. `Merchant::booted()` now delegates to it instead of holding the algorithm inline — identical output, existing merchants' slugs untouched (verified: `MerchantSlugTest`'s 5 pre-existing cases pass unmodified).
- **`config/store_identity.php`** — reserved words (`admin`, `store`, `settings`, ...) documented in one place, enforced at both auto-generation and manual edit.
- **Settings → Business Profile** — new editable "Store URL" field (merchant-facing UI never says "slug"): live sanitize-as-you-type, debounced live-availability check (`GET /settings/store-url/availability`, read-only, auth-scoped), copyable public-URL preview, and a plain `confirm()` warning — never a silent change, never a redirect — when the value actually differs on submit.
- **Backward compatible** — Storefront, Join, Launch Kit, Commerce, and Identity all continue reading `$merchant->slug` exactly as before; this sprint centralizes for *future* callers per spec, not a retrofit of every existing one.
- Caught via in-browser verification (not just tests) before commit: the URL-prefix input group squeezed the editable field to a few characters wide at both mobile and desktop widths — fixed with a full-width field and a mobile-hidden prefix badge.
- See [ADR-015](./OMOS/12-ADR/ADR-015-Store-Identity-and-Public-URL.md) for the full architecture and future-work recommendations (slug history/redirects, migrating existing `->slug` readers to the service).

Suite: 739 → 754 tests green (15 new `StoreIdentityTest` cases). Build clean. Awaiting CTO review (Type B).

## 2026-07-08 — OMEGA-001D: Merchant Branding & Product Experience Polish

Pure UI polish — no business logic, schema, route, or `MediaService` behaviour change.

- **Sidebar branding** — logo container now scales any aspect ratio via `object-fit: contain` inside a fixed, light-backed box (never crops, stays visible for dark transparent PNGs). No-logo merchants get a generated initials avatar (`Merchant::initials()`) instead of the generic wordmark. Business name now shown under the logo/avatar; "Powered by onemember" always present underneath.
- **Business name presentation** — new `Merchant::displayName()` normalizes all-lowercase or ALL-CAPS names to title case for display only ("mike's coffee" → "Mike's Coffee"). Deliberately-formatted mixed-case names ("Wilkinson LLC", "Aufderhar and Sons") are left untouched — the naive approach (unconditional title-case) was tried first and caught by the existing test suite mangling acronyms/connector words, so the rule was narrowed to only fire on all-lower/all-upper input. Applied to sidebar, storefront, order confirmation, join landing, launch-kit prints, and identity consent screens. Stored `name` value is never modified.
- **Settings — Business Logo** — current-logo status line, and `<x-ui.media-upload>` now carries logo-specific guidance ("400 × 400 px or larger, square or landscape", 1:1/16:9 presets) instead of the product-image defaults.
- **Commerce — View My Store** — prominent button on the Products page opening the existing storefront route in a new tab.
- **Product list & storefront polish** — subtle row-hover treatment on the product table; storefront thumbnails enlarged (48px → 56px) with a border, still `object-fit: cover` (preserves aspect ratio, never stretches); friendly icon added to the empty-catalogue state.
- **Accessibility** — global `:focus-visible` outline added as a safety net for custom interactive elements (sidebar brand, thumbs, dropzone) not already covered by Bootstrap's own focus styles.

Verified in-browser (not just tests) at desktop and 375px mobile widths, for both a logo-less merchant (initials avatar) and a logo-bearing merchant.

Suite: 734 → 739 tests green (5 new `MerchantDisplayNameTest` cases). Build clean. Awaiting CTO review (Type B).

## 2026-07-08 — OMEGA-001A (frontend): Reusable Premium Image Upload UI

A ticket asked to fix a "broken" drag/drop + Cropper.js product-image UI. A repo-wide search before writing any code found no such JS file, dependency, or UI pattern ever existed — the form had a plain file input. Raised to the Product Owner/CTO and approved as new work (DECISION-097), built generically so future modules (merchant logo, staff avatar, supplier logo, galleries, documents) can reuse it.

- **`<x-ui.media-upload>`** — new generic Blade component (name/aspect/presets/remove-name all configurable via props, nothing Product-specific).
- **`resources/js/product-image.js`** — enhances every `[data-media-upload]` root: drag/drop, click-to-browse, live preview, filename/dimensions/file-size display, Cropper.js crop stage with 1:1/4:5/16:9 presets, rotate left/right, replace/remove.
- **Cropper.js `^1.6.2`** added as a dependency (classic stable API, not the v2 web-component rewrite).
- **Progressive enhancement is structural**: the plain `<input type="file">` (and, in edit mode, its remove checkbox) are always present and functional; JS only hides them and reveals the rich UI after successfully wiring up, inside a try/catch — a JS failure leaves the native input working exactly as before.
- **Cropping is client-side only** — `Cropper.getCroppedCanvas().toBlob()` swaps a cropped `File` into the real input via `DataTransfer` right before submit. The backend (`MediaService`/`ProductController`, from ADR-013) is unmodified; it just receives cropped bytes instead of the original.
- `resources/views/commerce/products/form.blade.php` now renders `<x-ui.media-upload>` instead of its own inline file input + preview script.
- See [ADR-014](./OMOS/12-ADR/ADR-014-Reusable-Media-Upload-UI.md) for the full architecture.

728/728 tests green (`ProductImageTest` unmodified). Build clean. No database, route, or backend business-logic change. Awaiting CTO review (Type B).

## 2026-07-07 — OMEGA-001C: Unified Media Foundation

Architecture-only sprint building on the approved OMEGA-001A/B foundation (BETA-008A product images, BETA-008B localization — DECISION-094/095). No merchant-facing behaviour, route, or schema changed; `ProductImageTest` passes unmodified.

- **`App\Services\Media\MediaService`** — the one place that stores, replaces, deletes, validates, and resolves URLs for uploaded media. `Commerce\ProductController` now calls it instead of touching `Storage`/`UploadedFile` directly.
- **`config/media.php`** — centralizes mime types, max upload size, default disk, WebP quality, named variant sizes, and per-collection storage-path prefixes. Controllers no longer hardcode validation values.
- **Storage abstraction** — business logic never references a disk name; swapping to S3/R2/Spaces/Azure/Backblaze later is a config change.
- **Variant architecture (not yet generated)** — `App\Services\Media\Contracts\ImagePipeline` is the processing seam (`optimize()`/`variant()`), bound today to a no-op `NullImagePipeline` so behaviour is unchanged. `config('media.variants')` declares thumbnail/medium/large sizes for a future real pipeline to fill in without changing callers.
- **Future gallery scaffolding** — `MediaItem`/`MediaCollection` plain DTOs (no migration) give a settled shape for a later multi-image gallery feature.
- See [ADR-013](./OMOS/12-ADR/ADR-013-Unified-Media-Foundation.md) for the full storage/variant/migration strategy.

Suite: 701 → 728 tests green (7 new `MediaServiceTest` cases). Build clean. Awaiting CTO review (Type B).

## 2026-07-07 — OMEGA-001A/B: Commerce image experience + production UX polish

- OMEGA-001A: upload card (drag & drop/keyboard/camera), guidance, live
  preview, Cropper.js v2 crop/rotate, server-side WebP optimization ≤1200px
- OMEGA-001B: product list badges + 56px thumbnails, grouped product form
  with helper text, storefront availability nudges, image shimmer skeletons,
  sticky table headers, a11y audit

## 2026-07-07 — PLATFORM-002: Platform Foundation Sprint (12 parts)

Architectural foundation for the next platform phase. Laravel 13 monolith
throughout (ADR-004/009/012) — no microservices; everything backward
compatible; one commit per part.

- **P1 Marketplace**: typed App Manifests + AppRegistry (runtime-registrable),
  AppManager lifecycle (install/uninstall with dependency checks,
  enable/disable, per-merchant config), merchant_apps state table, health
  snapshots, lifecycle events + audit. Legacy installs unchanged.
- **P2 Plugin SDK**: Sdk\AppProvider base (routes/translations/policies/
  events/nav/widgets/settings schema) + 11 Provides* contracts; sidebar
  renders manifest navigation — new apps appear without Core view changes.
- **P3 Event Bus**: DomainEvent base + 10 stable events emitted from model
  lifecycle hooks (member.created, purchase.recorded, reward.redeemed,
  merchant.registered, order.placed, payment.received, subscription.changed,
  queue.ticket_created, supplier.created, purchase_order.approved).
- **P4 Webhooks**: merchant endpoints + delivery log, HMAC-signed queued
  delivery with 5-try backoff, auto-disable after 10 consecutive failures.
- **P5 Public API**: /api/v1 (ping + read-only members reference), hashed
  om_live_* keys with abilities, per-key rate limiting, standard error
  envelope, OpenAPI 3.1 skeleton.
- **P6 Automation**: WHEN/IF/THEN rule engine on the event bus (conditions
  evaluator fails closed; ActionRegistry with reference log action; queued
  execution). Visual builder = future.
- **P7 Knowledge Center**: markdown articles with categories/search/
  versioning/locale fallback/video placeholders + /help surface.
- **P8 Queue App**: first SDK app — counters, tickets (walk-in/reservation,
  priority, status machine), daily numbering, estimated wait, display board,
  SMS/LINE placeholders, analytics.
- **P9 Procurement App**: suppliers + vendor rating, purchase requests with
  approval workflow → purchase orders (cost tracking) → goods receipts with
  goods.received inventory hook.
- **P10 Localization expansion**: internal languages config-driven;
  placeholder locales km/my/lo/vi/zh/ja/ko with English fallback.
- **P11 Help framework**: x-ui.help-button (?) → context articles, global
  topbar Help entry, tooltip/walkthrough hooks.
- **P12 Developer docs**: docs/dev/ — marketplace, SDK, events, webhooks,
  API, automation, localization, knowledge, queue, procurement, commerce.

## 2026-07-07 — BETA-008: Global Merchant Settings + Product Images

- feat(commerce): one main image per product (BETA-008A, DECISION-094) — upload with
  live preview, replace (old file deleted), remove; merchant-scoped storage
  `products/{merchant_id}` on the public disk; jpg/png/webp ≤ 2 MB; shown in product
  list, storefront, and order confirmation with `bi-image` placeholder. `ProductImageTest` (8).
- feat(settings): Localization tab (BETA-008B, DECISION-095) — country, primary +
  additional accepted currencies (display only; conversion = future work), timezone,
  internal language separate from ordered customer-facing languages. Customer surfaces
  (storefront/order/portal/join) resolve `?lang=` against the merchant's offered
  languages, defaulting to the first — never the browser. Storefront gains a customer
  language switcher. Allowed values documented in `config/localization.php`.
  Existing merchants unchanged (internal locale maps to customer default).
  `GlobalSettingsTest` (12) incl. the Cambodia KHR/USD + Khmer/English example.
- docs: Product Bible localization model, Global-Platform-Repositioning §8 addendum,
  DECISION-094/095.

## 2026-07-07 — BETA-007 Premium Experience Sprint (private-beta polish)

- fix(ux): session flash messages render exactly once — layouts are the single
  global renderer via `x-ui.flash` (new `:session` prop); double flashes removed
  from apps, commerce products/orders, settings; admin layout gains flash support (BETA-007A)
- refactor(ux): members/campaigns lists adopt `x-ui.empty-state`; first-run members
  empty state gains an Add Member CTA; dead "coming soon" row button replaced with a
  real View action; dashboard Record Purchase quick action deep-links to Counter Mode
  when enabled (BETA-007B)
- feat(i18n): all ten `App\Enums` labels localized via new `lang/{en,th}/enums.php` —
  Thai merchants no longer see English status badges/type labels; CSV exports
  unaffected (raw values) (BETA-007C)
- fix(a11y): onboarding finish-step progress bar was invisible (duplicate style
  attribute); ARIA labels/values on onboarding + dashboard progress bars; icon-only
  buttons labelled (counter, products); storefront qty inputs get per-product labels (BETA-007D)
- fix(mobile): responsive wrappers on commerce products, campaign analytics, admin
  go-live and trial-history tables; analytics reward status used raw enum value (BETA-007E)

Suite: 669 tests / 1,519 assertions green. `npm run build` clean.

## 2026-07-07 — MORNING-001: Fable Maximum Sprint (DEPLOY-001, BETA-001…005)

Private-beta readiness pass. One logical commit per sub-sprint; no new architecture.

- **DEPLOY-001** (`cd0145e`): ready-to-paste Forge deploy script (`docs/OMOS/06-Operations/forge-deploy-script.sh`) — correct cache-clear-then-rebuild order, FPM reload for opcache, post-deploy route/health verification that fails the deploy loudly. Code confirmed deploy-sound; remaining steps are server-side (founder).
- **BETA-001** (`b9674c1`): `FullBetaJourneyTest` — the complete founder-demo journey as ONE sequential HTTP test (register → verify → onboard+terms → campaign → reward → member → purchase → redeem → launch kit → counter → OneMember card → scan-to-join → Commerce install → product → storefront → order → accept → manual payment → admin). **No app bugs found** — the journey is clean end to end.
- **BETA-002** (audit, no changes): mobile audit at 375/390/430/768px — corporate, login, register, onboarding, plus the merchant/admin screens covered in OVERNIGHT-001 P4. Zero horizontal overflow, zero layout bugs. Nothing to fix.
- **BETA-003** (`1b003c7`): customer-facing polish — join landing shows merchant phone/city (trust), order confirmation greets the customer by name and adds a "save this page" live-status hint. TH/EN.
- **BETA-004** (audit, no changes): first-use audit — every listed surface already has a guided empty state (members/campaigns/rewards/products/orders via the design system), trial banner, and the dashboard launch checklist. A new merchant always knows the next step.
- **BETA-005** (`d6801ed`): `onemember:demo-seed` — one complete sample merchant (campaign, 3 rewards, 8 members with history, Commerce with 4 products + 3 orders across the state machine). Refuses production, idempotent, `--fresh` recreates, never auto-runs.

Suite: 697 → 701 tests / 1,648 assertions green. Build clean.

## 2026-07-07 — OVERNIGHT-001: Private Beta Stabilization & Bug Hunt

Stabilization pass before private beta. No new features; low-risk fixes + test guards.

- **P1 deploy verification** (`3e432f4`): confirmed the "admin route not visible after deploy" risk is operational (stale route cache / opcache / APP_DOMAIN), not code. Added `DeploymentIntegrityTest` (40+ critical routes exist, admin routes bind to app domain, route:cache succeeds) and an Operations Manual §1a deploy-troubleshooting section.
- **P2 smoke suite** (`387d21c`): `PrivateBetaSmokeTest` — 11 end-to-end happy paths (registration, verification, onboarding, admin surfaces, loyalty cycle, identity scan-to-join, commerce + storefront + order flow). Beta is walkable end to end.
- **P3 link audit** (`2dd9b49`): audited all 188 `route()` refs in views — **no broken links**. Added `NavigationLinkAuditTest` to fail CI on any dead route link.
- **P4 mobile** (`cd9886e`): fixed the commerce products table overflowing the viewport at 375px (`.table-responsive` wrap); same applied to admin go-live + control-room tables. CSS-only, verified no body overflow.
- **P5 error handling** (`e0d5cfa`): audited edge cases — all degrade safely (no bugs). `ErrorHandlingTest` (13 cases) pins no-merchant/no-app 403s, empty states, missing-logo fallback, and 404s for bad/suspended slugs.

Suite: 684 → 697 tests green. One bug fixed (products-table mobile overflow); no other defects found — the codebase entered the hunt clean.

## 2026-07-05 — FINAL engineering hardening (Fable close-out)

- perf: lazyById streaming in birthday/expiry commands (B-05)
- perf: five SCALE-001 indexes — members phone/activity/postal, transactions program/date + merchant/date (B-02/B-04)
- refactor: merchant branding via View Composer (TD-003)
- refactor: currency fallback centralised to app.default_currency (TD-005)
- cleanup: dead MerchantProfileController/view/PUT route removed (TD-004)
- prep: .env.example documents the Redis production switch (ADR-009); no infra change
- audit: full code review found no N+1s, no missing tenant scoping, no unescaped user output; login/OTP-style rate limits verified

Suite: 521 tests / 1,060 assertions green after every commit.

# Changelog

All notable changes to this project are documented here.

Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).  
This project adheres to [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### Added
- Laravel 13 project scaffold
- Bootstrap 5.3 as the frontend framework (replacing Tailwind)
- Bootstrap Icons 1.x
- Base layouts: `layouts/app.blade.php` (sidebar) and `layouts/guest.blade.php` (centred card)
- Placeholder dashboard welcome page
- `/docs` folder with initial documentation structure
- Git repository initialised

---

## [0.1.0] – 2026-06-27

- Initial project setup
