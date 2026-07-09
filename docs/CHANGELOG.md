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
