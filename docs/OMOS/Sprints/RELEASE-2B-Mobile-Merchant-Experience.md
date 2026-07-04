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

- [ ] Sidebar opens/closes correctly on all mobile widths (375 px, 390 px, 430 px)
- [ ] Close button visible inside sidebar on mobile
- [ ] ESC key closes sidebar
- [ ] Body scroll is locked when sidebar is open on mobile
- [ ] No horizontal scroll on any page at 375 px
- [ ] Language switcher fits in topbar on mobile (icon only at < 576 px)
- [ ] FAB does not obscure the last row of page content on mobile
- [ ] All existing 406+ tests pass
- [ ] `npm run build` passes with no errors
- [ ] New mobile nav tests pass

---

## Files Changed

| File | Change |
|---|---|
| `resources/views/layouts/app.blade.php` | Close button, ESC handler, scroll-lock Alpine effect |
| `resources/css/app.css` | Body scroll lock, FAB padding, lang switcher icon-only |
| `tests/Feature/MobileNavTest.php` | New mobile nav tests |
| `docs/OMOS/CurrentSprint.md` | Updated to RELEASE-2B |
| `docs/08-Product-Decisions.md` | DECISION-066 recorded |
| `docs/OMOS/Sprints/RELEASE-2B-Mobile-Merchant-Experience.md` | This file |
