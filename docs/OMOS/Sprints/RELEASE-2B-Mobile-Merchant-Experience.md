# RELEASE-2B — Mobile Merchant Experience

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-2B |
| **Title** | Mobile Merchant Experience |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | UX / Technical |
| **Classification** | Type B — CTO Review Required |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-05 |
| **Target Completion** | 2026-07-05 |
| **Decision** | [DECISION-066](../../08-Product-Decisions.md) |

---

## Business Objective

Make the authenticated merchant application at `app.onemember.co` fully mobile-first and production-ready. Merchants use the app on-counter on mobile devices; an unusable mobile UX directly undermines the product's primary use case.

---

## Scope

Focus only on `app.onemember.co`. No changes to corporate website or customer portal.

---

## Acceptance Criteria

- [x] Sidebar opens/closes correctly on all mobile widths (375 px, 390 px, 430 px)
- [x] Close button visible inside sidebar on mobile
- [x] ESC key closes sidebar
- [x] Body scroll is locked when sidebar is open on mobile
- [x] No horizontal scroll on any page at 375 px
- [x] Language switcher fits in topbar on mobile (icon only at < 576 px)
- [x] FAB does not obscure the last row of page content on mobile
- [x] Dashboard stat cards stack 2-up on sm, 1-up on xs; quick actions 2-up on xs
- [x] Dashboard tables: Campaign and DateTime columns hidden on xs (Recent Activity); Type and Rewards hidden on xs (Active Campaigns)
- [x] Members index: mobile card list (name, phone, points, status) on xs; full table on sm+
- [x] Members show: action buttons wrap on xs; activity filter scrolls horizontally
- [x] Campaigns index: mobile card list (icon, name, type, status, date) on xs; full table on sm+
- [x] Campaigns show: action buttons wrap; rewards toolbar stacks on xs with scrollable filter; rewards table hides Type/Points/Quantity on xs
- [x] Rewards show: action buttons wrap on xs
- [x] Rewards create / Member create / Campaign create: single-column form layouts already responsive via col-12 col-lg-*
- [x] Settings: nav-tabs scroll horizontally on xs; all form fields stack via col-md-*
- [x] Subscription: plan comparison table horizontally scrollable; current plan and usage cards stack on xs
- [x] Onboarding wizard: wizard layout already constrained to max-width:640px with px-3
- [x] All 413 tests pass
- [x] `npm run build` passes clean

---

## Commits

| Commit | Description |
|---|---|
| `ea64eda` | Phase 1: Sidebar close button, ESC, scroll lock, lang switcher, FAB, mobile nav tests |
| `c17ef63` | Phase 2: Full page-by-page mobile audit — dashboard, members, campaigns, rewards |

---

## Files Changed

| File | Change |
|---|---|
| `resources/views/layouts/app.blade.php` | Close button, ESC handler, scroll-lock Alpine effect |
| `resources/css/app.css` | Body scroll lock, FAB padding, lang switcher, nav-tabs scrollable, filter-scroll utility, page-header-actions wrap, sidebar overlay, touch targets |
| `resources/views/dashboard.blade.php` | Column hiding on xs for both activity and campaigns tables |
| `resources/views/members/index.blade.php` | Dual markup: mobile card list + desktop table |
| `resources/views/members/show.blade.php` | Action buttons flex-wrap; activity filter scroll |
| `resources/views/campaigns/index.blade.php` | Dual markup: mobile card list + desktop table |
| `resources/views/campaigns/show.blade.php` | Action buttons wrap; rewards toolbar stacked; rewards table column hiding |
| `resources/views/rewards/show.blade.php` | Action buttons flex-wrap |
| `tests/Feature/MobileNavTest.php` | New mobile nav tests |
| `docs/OMOS/CurrentSprint.md` | Updated to RELEASE-2B |
| `docs/08-Product-Decisions.md` | DECISION-066 recorded |
