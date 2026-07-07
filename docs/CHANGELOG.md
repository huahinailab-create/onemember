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
