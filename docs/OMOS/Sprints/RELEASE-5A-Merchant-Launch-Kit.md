# RELEASE-5A — Merchant Launch Kit & Onboarding Assets

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-5A |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | Feature |
| **Classification** | Type B — new public route + merchant-facing capability |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Started / Completed** | 2026-07-06 |
| **Related Documents** | [08-Product-Decisions DECISION-075](../../08-Product-Decisions.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md) |

---

## Business Objective

A merchant signs up, completes onboarding, and immediately has everything needed to start collecting members at the counter: QR poster, counter sign, staff guide, talking script, and campaign copy.

## Scope Delivered

1. **Launch Kit page** (`/launch-kit`, auth + merchant-scoped): join QR + copyable link, offer picker, campaign copy preview, talking script, print-asset launcher.
2. **Public join landing** (`/join/{slug}`, app domain, guest): information-only page the poster QR points to — merchant brand, offer, "show this to staff" steps. **No data collection, no self-enrolment, no wallet architecture** — enrolment stays with staff via existing flows (Phase 2 wallet join remains out of scope by design).
3. **QR assets** via the existing `CustomerPortalService::qrCodeSvg()` (simple-qrcode, no new provider): join-page QR (poster/counter card) and counter-screen QR (staff guide).
4. **Printables** (Blade + print CSS, browser print, no PDF package): A4 poster, A6 counter card, A4 staff quick-start guide — one asset per printed page, print toolbar hidden via `d-print-none`.
5. **Configurable offer copy**: "Join the OneMember Family." + per-offer line (coffee default / dessert / discount / gift) via validated `?offer=` parameter and per-offer lang keys — new offers only add translations.
6. **Staff guide content**: find member, add member, record purchase, redeem reward, Counter Mode, what-to-say scripts.
7. **Onboarding + navigation**: "Open Launch Kit" primary button on the onboarding finish screen; Launch Kit item in the merchant sidebar.
8. **Localization**: Thai default, English complete (60+ keys in `lang/{en,th}/launch.php`, guarded by `TranslationCompletenessTest`).

## Out of Scope (explicit)

Customer self-enrolment form, wallet join flow, native wallet passes, PDF generation, external services.

## Tests

`tests/Feature/LaunchKitTest.php` — 18 tests: guest redirects (kit + printables), merchant access, no-merchant 403, merchant-scoped QR/link (own vs other merchant), offer variants + invalid-offer fallback, all three printables render, landing renders for guest / 404 unknown slug / 404 suspended merchant, Thai default + English, sidebar link, onboarding finish link. Full suite: **541 passed / 1,110 assertions**.

## Incidental fix

`layouts/app.blade.php` logo `alt` referenced the removed `$__branding` variable (latent from FINAL-003, only triggered with a merchant logo) — corrected to `$merchantBranding`.

## Commit

`feat(launch): add merchant launch kit assets`
