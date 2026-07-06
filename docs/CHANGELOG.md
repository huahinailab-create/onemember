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
