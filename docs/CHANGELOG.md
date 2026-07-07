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
