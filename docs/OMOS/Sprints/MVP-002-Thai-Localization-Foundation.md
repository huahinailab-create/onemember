# Sprint Spec: MVP-002 — Thai Localization Foundation

| Field | Value |
|---|---|
| **Sprint ID** | MVP-002 |
| **Title** | Thai Localization Foundation |
| **Type** | Feature |
| **Priority** | 🟠 High |
| **Status** | ✅ Complete |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-03 |
| **Completed** | 2026-07-03 |
| **Sprint File** | `docs/OMOS/Sprints/MVP-002-Thai-Localization-Foundation.md` |

---

## Business Outcome

> A Thai merchant should feel that OneMember was built for Thailand, not translated into Thai.

From day one — before they change a single setting — the product should speak Thai, default to Thai Baht, default to Bangkok time, and create starter content in Thai. The member portal viewed by their customers should render in Thai. Every UI string should honour the merchant's locale, including dynamic Alpine.js previews.

---

## Background

The `lang/th/` translation files exist and have parity with `lang/en/` (18 files, equal key counts). The `SetLocale` middleware correctly reads the merchant's `settings['locale']` and calls `App::setLocale()`. However, four structural gaps prevent Thai merchants from experiencing Thai by default:

1. New merchants are created with no locale in settings — the middleware falls back to `APP_LOCALE` which defaults to `'en'`.
2. The onboarding flow does not save the locale selection from the business settings step.
3. The public member portal (`/member/{uuid}`) is unauthenticated and bypasses `SetLocale` — it always renders in `APP_LOCALE`.
4. Starter campaigns created at the end of onboarding use hardcoded English strings.
5. Alpine.js preview strings in `campaigns/show.blade.php` are hardcoded English, bypassing the translation system entirely.

---

## Tasks

### Task 1 — Default locale to 'th' for new merchants
**File:** `app/Http/Controllers/OnboardingController.php`

When `storeBusinessInfo()` creates a new `Merchant`, include `'locale' => 'th'` in the default settings array. This ensures every Thai merchant experiences the dashboard in Thai before they ever visit settings.

### Task 2 — Save locale during onboarding business settings step
**Files:**
- `app/Http/Requests/StoreOnboardingBusinessSettingsRequest.php`
- `app/Http/Controllers/OnboardingController.php`

Add `locale` validation rule to the request (`required`, `in:en,th`). Save `$validated['locale']` to `settings['locale']` in `storeBusinessSettings()`. This makes the language selector on the onboarding settings screen functional.

### Task 3 — Member portal renders in merchant's locale
**File:** `app/Http/Controllers/CustomerPortalController.php`

At the top of `show()` and `card()`, after loading `$member`, call `App::setLocale($member->merchant->settings['locale'] ?? 'th')`. This ensures the customer-facing portal renders in the merchant's chosen language.

### Task 4 — Starter campaigns created in merchant's locale
**Files:**
- `app/Http/Controllers/OnboardingController.php`
- `lang/en/onboarding.php`
- `lang/th/onboarding.php`

Add translation keys for starter campaign names and reward descriptions. Use `__()` in `createStarterCampaign()` so the content is created in whatever locale is active at request time (which will be the merchant's locale after Task 1/2).

### Task 5 — Alpine.js campaign preview strings localised
**Files:**
- `resources/views/campaigns/show.blade.php`
- `lang/en/campaigns.php`
- `lang/th/campaigns.php`

Add translation keys for all dynamic Alpine.js strings in the campaign rules tab preview. Pass them via `$campaignConfigData['i18n']` using `__()`. Refactor the Alpine.js `campaignConfig()` function to use `this.i18n.*` instead of hardcoded English.

### Task 6 — APP_LOCALE default to 'th'
**File:** `.env.example`

Change `APP_LOCALE=en` to `APP_LOCALE=th`.

---

## Acceptance Criteria

| # | Criterion | How to Verify |
|---|---|---|
| AC-1 | New merchant's `settings['locale']` is `'th'` after onboarding Step 1 | Unit test |
| AC-2 | Onboarding business-settings form saves locale to merchant settings | Unit test |
| AC-3 | `/member/{uuid}` renders `__('portal.points_balance')` in Thai when merchant locale is `'th'` | Feature test |
| AC-4 | Starter stamp campaign name is in Thai when locale is `'th'` | Feature test |
| AC-5 | Starter points campaign name is in Thai when locale is `'th'` | Feature test |
| AC-6 | `APP_LOCALE=th` in `.env.example` | Code review |
| AC-7 | Alpine.js preview in campaign rules tab uses `i18n.*` — no hardcoded English strings | Code review |
| AC-8 | All 324+ tests pass | `php artisan test` |

---

## Definition of Done

- [ ] All 6 tasks implemented
- [ ] All acceptance criteria met
- [ ] `php artisan test` — zero failures
- [ ] `lang/th/` key parity maintained (validation.php gap corrected if needed)
- [ ] No hardcoded English strings in Alpine.js campaign preview
- [ ] Committed with defined commit message
- [ ] OMOS governance updated

---

## Commit Message

```
Sprint MVP-002 — Thai Localization Foundation

- Default locale 'th' for new merchants on creation
- Onboarding business-settings step now saves locale
- Member portal (/member/{uuid}) sets locale from merchant settings
- Starter campaigns created with locale-aware names via __()
- Alpine.js campaign preview i18n: all hardcoded English replaced
- APP_LOCALE=th in .env.example

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

---

## Related Documents

- [EXECUTE.md](../EXECUTE.md)
- [Product-State.md](../Product-State.md)
- [ADR-005: Bootstrap 5 Only](../CTO-Decisions.md)
- [CTO-003: Event-Driven Email](../CTO-Decisions.md)
