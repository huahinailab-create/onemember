# Sprint Spec: MVP-003 — Merchant Acquisition Experience

| Field | Value |
|---|---|
| **Sprint ID** | MVP-003 |
| **Title** | Merchant Acquisition Experience |
| **Type** | Feature + UX |
| **Priority** | 🔴 Critical |
| **Status** | ✅ Complete |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-03 |
| **Completed** | 2026-07-03 |

---

## Business Outcome

> A merchant who visits OneMember for the first time immediately understands its value, completes onboarding confidently, and is ready to begin a 30-day trial.

---

## Background

The current first-run experience has four significant gaps:

1. **Landing page** (`welcome.blade.php`, 28 lines) is a placeholder. No value proposition, no brand colours, no trial offer. A merchant who lands here has no reason to register.
2. **Guest and wizard layouts** use a Bootstrap hexagon icon + plain "OneMember" text instead of the branded text logo.
3. **Registration page** has no trial messaging. A merchant does not know they are starting a free trial.
4. **Onboarding finish** does not confirm that the 30-day Professional trial has started. The `TrialStarted` event fires but the merchant sees no acknowledgement.

---

## Tasks

### Task 1 — Landing page
**File:** `resources/views/welcome.blade.php`

Rebuild as a full brand-compliant acquisition page:
- Navy (`#1A2E5A`) hero with "one"(pink)+"member"(white) text logo
- Thai-first headline and subheading (uses `__()`)
- Three benefit cards (Members, Revenue, Automation)
- Single pink CTA button → `route('register')`
- "No credit card required" badge
- Responsive (Bootstrap 5 grid)

### Task 2 — Guest layout brand logo
**File:** `resources/views/layouts/guest.blade.php`

Replace the `bi-hexagon-fill` icon + "OneMember" text with inline "one"(pink)+"member"(dark) styled `<span>` elements. Preserves the existing card/slot structure.

### Task 3 — Wizard layout brand logo
**File:** `resources/views/layouts/wizard.blade.php`

Same logo fix in the wizard header. Inline `<span>` pattern with correct brand colours.

### Task 4 — Registration trial messaging
**File:** `resources/views/auth/register.blade.php`

Add a `<div>` trial value strip above the form:
- "30-day Professional trial" badge
- Three tick-list items: Professional features, No credit card, Cancel anytime
- Uses `__()` for all strings

### Task 5 — Onboarding welcome trial badge
**File:** `resources/views/onboarding/welcome.blade.php`

Add a trial confidence badge ("30 days free — no credit card required") between the step indicators and the CTA button. Remove friction by de-emphasising "Skip for now" (move to small link below the card).

### Task 6 — Onboarding finish trial confirmation
**File:** `resources/views/onboarding/finish.blade.php`

Add a trial-started panel below the success icon:
- "Your 30-day Professional trial has started"
- Trial end date displayed (`now()->addDays(30)->format(...)`)
- "What happens at the end of your trial" one-liner

### Task 7 — Language keys (EN + TH)
**Files:** `lang/en/auth.php`, `lang/th/auth.php`, `lang/en/onboarding.php`, `lang/th/onboarding.php`

Add all new strings used by Tasks 1–6. Thai translations for every new key.

---

## Acceptance Criteria

| # | Criterion |
|---|---|
| AC-1 | Landing page renders with navy hero, brand logo, Thai headline, pink CTA |
| AC-2 | Guest and wizard layouts show "one"(pink) + "member" text logo |
| AC-3 | Register page shows "30-day free trial" strip |
| AC-4 | Onboarding finish shows trial started confirmation with end date |
| AC-5 | All new strings use `__()` — no hardcoded English in views |
| AC-6 | All new keys exist in both `lang/en/` and `lang/th/` |
| AC-7 | `php artisan test` — zero failures |

---

## Commit Message

```
Sprint MVP-003 — Merchant Acquisition Experience

- Landing page rebuilt: brand hero, value prop, trial CTA
- Guest + wizard layouts: brand text logo replaces hexagon icon
- Register page: 30-day trial strip with tick-list
- Onboarding welcome: trial confidence badge
- Onboarding finish: trial-started confirmation + end date
- EN + TH lang keys for all new strings

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

---

## Related Documents

- [EXECUTE.md](../EXECUTE.md)
- [Product-State.md](../Product-State.md)
- Brand palette: NAVY `#1A2E5A`, PINK `#FF1585`, WHITE `#FFFFFF`
