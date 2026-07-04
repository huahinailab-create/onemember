# RELEASE-2A — Corporate Website

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-2A |
| **Title** | Corporate Website |
| **Type** | Type B — CTO Review Required |
| **Status** | ⏳ Awaiting CTO Review |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-04 |
| **Target Completion** | 2026-07-04 |
| **Actual Completion** | 2026-07-04 |
| **Decision** | [DECISION-064](../../08-Product-Decisions.md#decision-064) |

---

## Business Objective

Deliver the complete public marketing website for `onemember.co`. The corporate website is the primary customer acquisition channel for OneMember. It must be professional, fast, fully bilingual (Thai default / English), SEO-optimised, and legally compliant with Thailand's PDPA.

---

## Scope

### Pages In Scope

| Route | Page | Status |
|---|---|---|
| `/` | Home | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/features` | Features | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/industries` | Industries | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/pricing` | Pricing | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/about` | About | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/faq` | FAQ | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/contact` | Contact | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/privacy` | Privacy Policy | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/terms` | Terms of Service | ✅ Built (RELEASE-1B) + Localised (RELEASE-1C) |
| `/pdpa` | PDPA Privacy Notice | ✅ **Built (RELEASE-2A)** |

### Out of Scope

- Blog (content placeholder only, not populated with real articles)
- Resources library (placeholder)
- Demo booking (placeholder — no real calendar integration)
- Partner portal
- Careers listings (placeholder)

---

## Definition of Done

| # | Criterion | Status |
|---|---|---|
| 1 | All 10 pages render without errors in both Thai and English | ✅ |
| 2 | PDPA Notice page exists at `/pdpa` with full PDPA-compliant content | ✅ |
| 3 | PDPA link appears in the footer Legal column | ✅ |
| 4 | No hardcoded user-facing strings in any corporate Blade template | ✅ |
| 5 | Language switcher present in corporate nav on every page | ✅ (inherited from RELEASE-1C) |
| 6 | Open Graph, Twitter Card, and canonical tags present on all pages | ✅ (inherited from RELEASE-1B) |
| 7 | DECISION-064 recorded before implementation | ✅ |
| 8 | All 398 existing tests pass | ✅ |

---

## Files Changed

### New Files
- `resources/views/corporate/pdpa.blade.php`

### Modified Files
- `lang/en/corporate.php` — added `pdpa_meta_title`, `pdpa_meta_desc`, `pdpa_h1`, `pdpa_eyebrow`, `pdpa_intro`, `pdpa_last_updated`, `footer_pdpa_notice`, `nav_pdpa`, `pdpa_full_sections` (14 sections)
- `lang/th/corporate.php` — same keys in Thai
- `app/Http/Controllers/CorporateController.php` — added `pdpa()` method
- `routes/web.php` — added `Route::get('/pdpa', ...)` → `corporate.pdpa`
- `resources/views/layouts/corporate.blade.php` — added PDPA Notice link in footer Legal column
- `docs/08-Product-Decisions.md` — DECISION-064 recorded
- `docs/OMOS/Sprints/RELEASE-2A-Corporate-Website.md` — this file
- `docs/OMOS/CurrentSprint.md` — updated to RELEASE-2A

### OMOS Documents
- `docs/OMOS/CurrentSprint.md`
- `docs/OMOS/Sprints/RELEASE-2A-Corporate-Website.md`

---

## Technical Notes

### PDPA Page Architecture

The PDPA page follows the same architecture as `privacy.blade.php` and `terms.blade.php`:

```blade
@foreach(trans('corporate.pdpa_full_sections') as $section)
<div>
    <h2>{{ $section[0] }}</h2>         {{-- Section heading --}}
    @foreach($section[1] as $para)     {{-- Paragraphs --}}
        <p>{{ $para }}</p>
    @endforeach
    @foreach($section[2] as $sub)      {{-- Subsections with bullets --}}
        <h6>{{ $sub[0] }}</h6>
        <ul>@foreach($sub[1] as $point)<li>{{ $point }}</li>@endforeach</ul>
    @endforeach
</div>
@endforeach
```

### PDPA Content Coverage

14 sections covering all PDPA Chapter 3 requirements:
1. Data Controller
2. Categories of Personal Data
3. Legal Basis for Processing
4. Purpose of Processing
5. Data Sharing and Disclosure
6. Data Retention
7. Data Subject Rights (PDPA Chapter 3)
8. Exercising Rights (response within 30 days)
9. Cookies and Tracking
10. Data Security
11. International Data Transfers
12. Children's Data
13. Changes to This Notice
14. Contact & DPO

---

## Completion Report

**Developer:** Claude Sonnet 4.6  
**Completion Date:** 2026-07-04  
**Tests:** 398 / 398 passing  
**Commit:** See CurrentSprint.md

### Summary

RELEASE-2A completes the corporate website by adding the PDPA Privacy Notice page — the only page required by the Product Owner brief that was not delivered in RELEASE-1B. All nine other pages (Home, Features, Industries, Pricing, About, FAQ, Contact, Privacy Policy, Terms of Service) were built in RELEASE-1B and localised in RELEASE-1C. The PDPA page contains legally-complete content under Thailand's PDPA B.E. 2562, covering all 14 required disclosure areas, in both Thai (default) and English. The footer Legal column now links to all four legal pages.

**No merchant application code was modified.** No tests were removed. All 398 tests pass.
