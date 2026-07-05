# RELEASE-2C — Thai Corporate Website Localization

**Sprint:** RELEASE-2C  
**Status:** Complete  
**Date:** 2026-07-05  
**Decision:** DECISION-067

---

## Objective

Make `onemember.co` Thai-first. Every public corporate page defaults to Thai with no browser sniffing. Users switch to English via the language switcher in the header. The merchant app at `app.onemember.co` is unaffected.

---

## Requirements Delivered

| # | Requirement | Status |
|---|---|---|
| 1 | Thai default on `onemember.co` | ✅ |
| 2 | Language switcher: `🌐 ภาษาไทย` / `🌐 English` | ✅ |
| 3 | All 17 corporate pages translated (no hardcoded text) | ✅ |
| 4 | `lang/en/corporate.php` and `lang/th/corporate.php` (880+ keys each) | ✅ |
| 5 | CTA links: Register → `https://app.onemember.co/register`, Sign In → `/login` | ✅ |
| 6 | Thai text doesn't break mobile layout | ✅ |
| 7 | SEO: localized title, meta description, OG title/description | ✅ |
| 8 | 21 new tests; 436 total passing | ✅ |
| 9 | DECISION-067 documented | ✅ |
| 10 | `npm run build` succeeds | ✅ |

---

## Root Cause / Key Design Decision

Browser Accept-Language detection was intentionally **removed** from `SetLocale` middleware. The original middleware would detect `en-US,en;q=0.5` from an English-language OS/browser and serve English — defeating the Thai-first requirement. Thai SME merchants typically run English-language macOS/iOS devices.

The locale priority chain is now:
1. Authenticated merchant's saved locale (DB settings)
2. Session (explicit `/locale` switch)
3. Hard default: `'th'`

---

## Files Changed

| File | Change |
|---|---|
| `app/Http/Middleware/SetLocale.php` | Removed browser Accept-Language detection; added hard Thai default |
| `app/Http/Controllers/LocaleController.php` | Redirect URL validation now whitelists both app and corporate domains |
| `resources/views/components/language-switcher.blade.php` | Globe emoji (`🌐`) prefix instead of Bootstrap icon |
| `tests/Feature/CorporateLocalizationTest.php` | **New** — 21 tests covering Thai default, switcher, page translations, SEO |
| `tests/Feature/MerchantAcquisitionTest.php` | Explicit `withSession(['locale' => 'en'])` for English-content assertions |
| `tests/Feature/DataImportExportTest.php` | Explicit locale for English validation message and export header tests |
| `docs/08-Product-Decisions.md` | DECISION-067 appended |
| `phpunit.xml` | `APP_LOCALE=th` env var added |
| `public/build/` | Rebuilt assets committed |

---

## Test Results

```
436 tests, 436 passed, 881 assertions
```

- 21 new tests in `CorporateLocalizationTest`
- All 415 pre-existing tests continue to pass

---

## Commit

`feat(corporate): add Thai-first corporate localization`

---

## Risks

- **Browser language detection removed:** Users arriving with a Thai browser who had previously relied on automatic Thai detection are unaffected (Thai is now the hard default). Users arriving with a non-English/Thai browser who expected English will now see Thai — expected behaviour for a Thai-first site.
- **English export/import tests:** Two tests now require explicit `locale=en` in session. Any future locale-sensitive test should explicitly set locale rather than relying on environment defaults.
