# 25 — Merchant Branding & White Label Lite

> **Sprint:** 6.3  
> **Last updated:** 2026-06-29  
> **Decision reference:** DECISION-057  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/12-SaaS-Architecture.md](12-SaaS-Architecture.md)

---

## 1. Overview

Merchant Branding (White Label Lite) allows each merchant to personalize the OneMember dashboard and emails with their own logo, brand colors, business tagline, and social media links.

**Key constraint — White Label Lite:** OneMember branding is **never hidden**. When a merchant logo is uploaded, the sidebar shows the merchant logo alongside the text "Powered by OneMember". This is intentional per DECISION-057.

---

## 2. Architecture

All branding logic is encapsulated in a single service. Views and templates consume the service — they never access the Merchant model directly for branding purposes.

```
View / Email Template
    │
    └─ MerchantBrandingService
            │
            ├─ logo()              → ?string (public URL or null)
            ├─ primaryColor()      → string (hex, fallback #2563EB)
            ├─ secondaryColor()    → string (hex, fallback #1E293B)
            ├─ displayName()       → string (merchant name or app name)
            ├─ tagline()           → ?string
            ├─ receiptFooter()     → ?string
            ├─ socialLinks()       → array{facebook, instagram, line, website}
            ├─ storeLogo(file)     → string (stored path)
            └─ deleteLogo()        → void
```

---

## 3. Database Fields

**Migration:** `2026_06_29_000001_add_branding_fields_to_merchants_table`

New fields added to `merchants` table:

| Column | Type | Default | Notes |
|---|---|---|---|
| `brand_color` | `varchar(7)` | `#2563EB` | Primary hex color |
| `secondary_color` | `varchar(7)` | `#1E293B` | Secondary hex color |
| `business_tagline` | `varchar(255)` | null | Max 100 chars in UI |
| `receipt_footer` | `text` | null | Max 500 chars in UI |
| `facebook_url` | `varchar(255)` | null | Social link |
| `instagram_url` | `varchar(255)` | null | Social link |
| `line_url` | `varchar(255)` | null | Social link |

**Pre-existing fields (not added by this migration):**

- `logo_path` — added in initial migration `2026_06_27_000001`
- `website` — added in `2026_06_28_100001`

All new fields are in `Merchant::$fillable`.

---

## 4. MerchantBrandingService

**File:** `app/Services/MerchantBrandingService.php`

Accepts a `?Merchant` in its constructor. All methods are null-safe — calling them with `null` merchant returns fallback values.

### Logo Storage

- **Disk:** `public` (Storage facade, serves via `storage/app/public/`)
- **Path:** `merchant-logos/{merchant_id}_{timestamp}.{ext}`
- **Security:** Merchant ID is in the filename — a merchant cannot accidentally overwrite another merchant's file.
- **On new upload:** Old file is deleted before storing the new one.
- **Accepted types:** JPEG, PNG, WEBP; max 2 MB.
- **Logo URL:** Only returned if the file actually exists on disk (`Storage::disk('public')->exists()`). Stale paths return `null`.

### Color Fallbacks

| Method | Fallback |
|---|---|
| `primaryColor()` | `#2563EB` (OneMember blue) |
| `secondaryColor()` | `#1E293B` (OneMember dark) |

---

## 5. Settings UI

Branding fields are in **Settings → Business Profile** tab, below the existing business information fields.

### Sections added:

1. **Branding** (with "White Label Lite" badge)
   - Logo upload with live preview (Alpine.js FileReader)
   - "Remove logo" button (marks `remove_logo = 1`)
   - Brand color + Secondary color pickers (color swatch + hex input)
   - Business tagline (max 100)
   - Receipt / Email footer (max 500)

2. **Online Presence** (within the Branding section)
   - Facebook Page URL
   - Instagram Profile URL
   - LINE Official Account URL

---

## 6. Sidebar Branding

**File:** `resources/views/layouts/app.blade.php`

- When merchant has a logo → shows logo image (height: 36px)
- Below logo → "Powered by OneMember" label (small, muted)
- When no logo → default hexagon icon + app name (unchanged appearance)

The `MerchantBrandingService` is instantiated in the layout's `@php` block via FQCN (`new \App\Services\MerchantBrandingService`).

---

## 7. Email Branding

Merchant-specific email templates (`trial-started`, `subscription-purchased`, `subscription-renewed`, `subscription-cancelled`, `payment-failed`, `trial-ending-reminder`) include:

1. **Merchant logo** — rendered as Markdown image if `logo_path` is set
2. **Merchant name** — bold, followed by "Powered by OneMember"
3. **Horizontal rule** to separate merchant header from email body
4. **Receipt footer** — appended above the sender name on billing emails

---

## 8. Validation

**Request:** `UpdateMerchantProfileRequest` (extended from existing)

| Field | Validation |
|---|---|
| `logo` | `image\|mimes:jpg,jpeg,png,webp\|max:2048` |
| `remove_logo` | `boolean` |
| `brand_color` | `regex:/^#[0-9A-Fa-f]{6}$/` |
| `secondary_color` | `regex:/^#[0-9A-Fa-f]{6}$/` |
| `business_tagline` | `max:100` |
| `receipt_footer` | `max:500` |
| `website` / `facebook_url` / `instagram_url` / `line_url` | `url\|max:255` |

Validation happens in Laravel — MIME check is performed server-side, not just by extension.

---

## 9. Security

- **Cross-tenant isolation:** Logo filenames include `merchant_id`. A merchant can only see/write their own files.
- **File type validation:** Laravel's `mimes` rule checks MIME type, not just extension.
- **No raw file paths from user:** The path is constructed by the service, not passed from user input.
- **Storage facade only:** Never serve files via raw filesystem paths.
- **Logo URL safety:** `MerchantBrandingService::logo()` checks `Storage::exists()` before returning a URL — no dangling references.

---

## 10. Localization

Keys added to:
- `lang/en/settings.php` — 24 new keys
- `lang/th/settings.php` — 24 new keys (identical key set)
- `lang/en/navigation.php` — `powered_by` key
- `lang/th/navigation.php` — `powered_by` key
- `lang/en/email.php` — `powered_by` key
- `lang/th/email.php` — `powered_by` key

---

## 11. Analytics

Events tracked via `AnalyticsService`:

| Event | When |
|---|---|
| `merchant_logo_uploaded` | New logo file uploaded |
| `merchant_branding_updated` | Brand color, tagline, social links, or footer changed |

---

## 12. Testing

**File:** `tests/Feature/MerchantBrandingTest.php` (21 tests)

| Test | What it verifies |
|---|---|
| `test_branding_service_returns_fallback_when_no_logo` | null logo → null URL |
| `test_branding_service_returns_primary_color_default` | fallback color |
| `test_branding_service_returns_secondary_color_default` | fallback color |
| `test_branding_service_returns_merchant_colors` | custom colors |
| `test_branding_service_display_name_falls_back_to_app_name` | null merchant |
| `test_branding_service_social_links` | social links array |
| `test_branding_service_returns_logo_url_when_file_exists` | Storage::fake |
| `test_branding_service_returns_null_when_logo_file_missing` | stale path |
| `test_logo_upload_stores_file_under_merchant_id` | namespaced path |
| `test_logo_upload_replaces_old_logo` | old file deleted |
| `test_invalid_logo_type_rejected` | validation |
| `test_logo_too_large_rejected` | max:2048 |
| `test_remove_logo_clears_logo_path` | remove_logo flag |
| `test_invalid_hex_color_rejected` | regex validation |
| `test_valid_branding_fields_saved` | all branding fields |
| `test_tagline_max_length_rejected` | max:100 |
| `test_invalid_url_rejected` | url validation |
| `test_merchant_cannot_overwrite_another_merchants_logo` | cross-tenant |
| `test_guest_cannot_update_profile` | auth |
| `test_logo_upload_dispatches_analytics_event` | no exception |
| `test_profile_edit_page_renders_for_authenticated_user` | page renders |

---

*Last updated: Sprint 6.3 — 2026-06-29*
