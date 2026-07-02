# MVP-001 — Merchant Experience Polish

| Field | Value |
|---|---|
| **Sprint ID** | MVP-001 |
| **Title** | Merchant Experience Polish |
| **Type** | Feature + Bug Fix |
| **Priority** | High |
| **Status** | 🔲 Planning |
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Last Updated** | 2026-07-02 |
| **Estimated Effort** | 1 session |
| **Approved By** | Awaiting PO approval |
| **Related Documents** | [../Audits/AI-03-Application-Audit.md](../Audits/AI-03-Application-Audit.md), [../CTO-Decisions.md](../CTO-Decisions.md), [../12-ADR/ADR-005-Bootstrap-5-Standard.md](../12-ADR/ADR-005-Bootstrap-5-Standard.md) |

---

## Business Objective

OneMember's brand colours are not applied correctly in the application. Merchants see Bootstrap default blue (`#2563eb`) on buttons, the sidebar, and the browser tab — not OneMember Deep Navy (`#1A2E5A`). This creates a mismatch between the brand identity defined in OMOS and what merchants actually see.

Additionally, the `LoyaltyProgram` model has a nullable JSON column that uses the default `'array'` cast instead of the null-safe Attribute accessor pattern (CTO-008). If the `settings` column is NULL in the database, array access will raise an `ErrorException` — the same class of bug as BUG-002.

After this sprint: the application looks like OneMember (not Bootstrap), and a silent bug risk is eliminated.

---

## Background

**AI-03 Application Audit** identified two issues requiring immediate attention before any Phase 1 feature sprints:

**H-001 — Brand colour mismatch:**
- `app.css` defines `--om-sidebar-bg: #1e293b` (Slate-800) — not `#1A2E5A`
- `--bs-primary` and `--bs-primary-rgb` are never overridden — all Bootstrap buttons, badges, and utility classes use default blue
- `<meta name="theme-color" content="#1d4ed8">` — PWA tab colour is Bootstrap blue
- Body background uses `#f1f5f9` (Slate-50) — not `#F0F0F4` (OneMember Cloud)
- `#FF1585` (OneMember Hot Pink) is not defined as a CSS variable

**H-005 — LoyaltyProgram settings nullable JSON risk:**
- `LoyaltyProgram` model uses `'settings' => 'array'` in `$casts`
- The `Merchant` model was fixed in BUG-002 with a custom null-safe `Attribute` accessor
- `LoyaltyProgram` was not updated — same vulnerability remains

Both issues are low-effort, high-impact fixes. No new features. No migrations. No architectural decisions.

**Source:** [AI-03 Application Audit](../Audits/AI-03-Application-Audit.md) — H-001, H-005

---

## Scope

- [ ] **Task 1:** Fix `--bs-primary` and `--bs-primary-rgb` in `resources/css/app.css`
- [ ] **Task 2:** Fix `--om-sidebar-bg` in `resources/css/app.css`
- [ ] **Task 3:** Fix `<meta name="theme-color">` in the layout Blade file
- [ ] **Task 4:** Fix body background to use `#F0F0F4`
- [ ] **Task 5:** Add `--om-accent: #FF1585` as a CSS custom property
- [ ] **Task 6:** Apply CTO-008 null-safe Attribute pattern to `LoyaltyProgram::settings()`
- [ ] **Task 7:** Write regression tests for items 1–6

---

## Out of Scope

- Counter Mode UI — separate sprint (MVP-004)
- Birthday or expiry automation — separate sprint (MVP-002)
- Member notification emails — separate sprint (MVP-003)
- Pagination on member index — engineering backlog
- Welcome/marketing page redesign — separate sprint
- ADR-007 (naming decision) — can be done in the same or a follow-up sprint

---

## Requirements

### Requirement 1 — CSS Variable Overrides (app.css)

In `resources/css/app.css`, within the `:root` block, add or replace:

```css
:root {
    --bs-primary:        #1A2E5A;
    --bs-primary-rgb:    26,46,90;
    --om-sidebar-bg:     #1A2E5A;
    --om-sidebar-hover:  #1e3a6e;
    --om-sidebar-border: #243d6b;
    --om-accent:         #FF1585;
    --om-accent-rgb:     255,21,133;
    --om-cloud:          #F0F0F4;
    --om-ink:            #1A1A2E;
}
```

Verify that `--bs-primary-rgb` is used wherever Bootstrap needs the RGB split (e.g., `rgba(var(--bs-primary-rgb), 0.15)` for focus rings and soft backgrounds).

### Requirement 2 — Body Background

Replace any hardcoded `#f1f5f9` body background with `var(--om-cloud)` or `#F0F0F4`.

### Requirement 3 — FAB Shadow

Replace the hardcoded `rgba(37, 99, 235, 0.4)` FAB box-shadow with `rgba(var(--bs-primary-rgb), 0.4)`.

### Requirement 4 — PWA theme-color Meta Tag

In `resources/views/layouts/app.blade.php`, update:

```html
<meta name="theme-color" content="#1A2E5A">
```

### Requirement 5 — LoyaltyProgram null-safe settings

In `app/Models/LoyaltyProgram.php`:

Remove `'settings' => 'array'` from `$casts`.

Add a null-safe Attribute accessor:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function settings(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value ? json_decode($value, true) : [],
        set: fn ($value) => json_encode($value),
    );
}
```

This matches the pattern applied to `Merchant::settings()` in BUG-002.

---

## Acceptance Criteria

- [ ] All Bootstrap `.btn-primary` buttons render as `#1A2E5A` (Deep Navy), not Bootstrap default blue
- [ ] The sidebar background is `#1A2E5A`
- [ ] Browser/PWA tab colour (`theme-color`) is `#1A2E5A` on mobile
- [ ] Body background is `#F0F0F4` (Cloud), not Slate-50
- [ ] `--om-accent` CSS variable is defined as `#FF1585`
- [ ] `LoyaltyProgram->settings` returns `[]` when the DB column is NULL (not ErrorException)
- [ ] `LoyaltyProgram->settings` returns the decoded array when the DB column has a value
- [ ] `php artisan test` passes with zero failures
- [ ] No hardcoded hex colour codes for `#2563eb`, `#1d4ed8`, `#1e293b`, or `#f1f5f9` remain in CSS after this sprint

---

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| CSS variable override breaks existing custom merchant branding | Low | `MerchantBrandingService` injects merchant colours directly — not affected by CSS variables |
| LoyaltyProgram accessor change causes type regression | Low | Existing `'array'` cast and new accessor produce same output for non-null values; test both |
| Bootstrap component styles require additional variable overrides | Medium | Run smoke test across all pages post-deploy to catch missed overrides |

---

## Dependencies

| Dependency | Type | Status |
|---|---|---|
| AI-03 Application Audit | Audit report | ✅ Complete (`f8d6ac8`) |
| ADR-005 — Bootstrap 5 Standard | Architecture Decision | ✅ Approved |
| CTO-008 — Nullable JSON pattern | CTO Decision | ✅ Approved |

---

## Testing Requirements

- [ ] **Brand colour test:** Assert that `--bs-primary` resolves to `#1A2E5A` (via rendered CSS check or snapshot)
- [ ] **LoyaltyProgram null-safe test:** Create a `LoyaltyProgram` with `settings = null`; assert `$program->settings` returns `[]`
- [ ] **LoyaltyProgram value test:** Create a `LoyaltyProgram` with `settings = ['key' => 'value']`; assert `$program->settings` returns the array
- [ ] **Regression:** Run full `php artisan test` — all 324 existing tests must still pass

---

## Definition of Done

- [ ] All 7 Scope tasks completed
- [ ] All Acceptance Criteria met
- [ ] `php artisan test` passes with zero failures
- [ ] New regression tests written for `LoyaltyProgram::settings()` null-safe behaviour
- [ ] No hardcoded Bootstrap default colours remain in `app.css`
- [ ] Code committed with the commit message below
- [ ] `CurrentSprint.md` updated — status `⏳ Awaiting CTO Review`
- [ ] `Product-State.md` updated — health score, last sprint
- [ ] Completion report returned to Product Owner
- [ ] Claude Developer has stopped

---

## Commit Message

```
Sprint MVP-001 — Merchant Experience Polish

Fix brand colour mismatch: --bs-primary now #1A2E5A, sidebar #1A2E5A,
theme-color #1A2E5A, body background #F0F0F4, --om-accent #FF1585.
Apply CTO-008 null-safe pattern to LoyaltyProgram::settings().

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

---

## Expected Deliverables

1. Updated `resources/css/app.css` — brand-compliant CSS variables
2. Updated `resources/views/layouts/app.blade.php` — correct `theme-color` meta
3. Updated `app/Models/LoyaltyProgram.php` — null-safe `settings()` Attribute
4. New test(s) for `LoyaltyProgram::settings()` null-safe behaviour
5. `php artisan test` output — 324+ tests, zero failures
6. Completion report confirming brand compliance and bug elimination
