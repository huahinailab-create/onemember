# 28 — Customer Self-Service Portal

> **Sprint:** 6.6  
> **Last updated:** 2026-06-30  
> **Decision reference:** DECISION-060  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/25-Merchant-Branding.md](25-Merchant-Branding.md), [docs/13-Product-Vision.md](13-Product-Vision.md)

---

## 1. Overview

Sprint 6.6 adds a lightweight, public-facing customer self-service portal. Customers can view their loyalty balance, available rewards, stamp progress, and birthday rewards — without creating an account or installing an app.

Access is via a unique, secure URL shared by the merchant as a QR code, printed card, or link.

---

## 2. What's NOT included (by design)

- No customer login or registration
- No purchase history details (amounts, dates, staff names)
- No internal notes, merchant settings, or analytics
- No automatic redemption — merchants still perform all redemptions
- No native iOS/Android app (deferred per DECISION-060)

---

## 3. Architecture

```
CustomerPortalController          ← Thin. Resolves member by public_uuid.
    │                               No business logic.
    │
    ├─ CustomerPortalService      ← All portal logic
    │    ├─ buildPortalData()     ← Safe data DTO (no IDs, no PII)
    │    ├─ qrCodeSvg()           ← Deterministic QR via simplesoftwareio/simple-qrcode
    │    ├─ barcodeSvg()          ← Code128 via picqer/php-barcode-generator
    │    ├─ isPortalEnabled()     ← member.portal_enabled && !trashed
    │    └─ prepare*Email()       ← Future email stubs (not yet sent)
    │
    └─ MerchantBrandingService    ← Brand colours, logo, name — same as merchant UI
```

### Packages added

| Package | Version | Purpose |
|---|---|---|
| `simplesoftwareio/simple-qrcode` | ^4.2 | Server-side SVG QR code generation |
| `picqer/php-barcode-generator` | ^3.2 | Server-side SVG Code128 barcode generation |

Both packages are pure PHP with no GD/Imagick requirements for SVG output.

---

## 4. Database Changes

**Migration:** `2026_06_30_000001_add_portal_fields_to_members_table`

| Column | Type | Default | Notes |
|---|---|---|---|
| `public_uuid` | `uuid` | auto-generated | Never changes unless merchant regenerates |
| `portal_enabled` | `boolean` | `true` | Merchant can disable per member |

`public_uuid` is generated in `Member::booted()` on creation. Existing members get one backfilled by the migration.

---

## 5. Portal Routes

### Public (no auth)

| Method | URL | Route Name | Description |
|---|---|---|---|
| `GET` | `/member/{public_uuid}` | `portal.show` | Main customer portal |
| `GET` | `/member/{public_uuid}/card` | `portal.card` | Digital member card (QR + barcode) |
| `GET` | `/member/{public_uuid}/qr.svg` | `portal.qr` | QR code as raw SVG (cacheable) |

### Merchant controls (authenticated)

| Method | URL | Route Name | Description |
|---|---|---|---|
| `PUT` | `/members/{member}/portal/toggle` | `members.portal.toggle` | Enable / disable portal |
| `POST` | `/members/{member}/portal/regenerate` | `members.portal.regenerate` | Issue new `public_uuid` (old QR invalid) |

---

## 6. Portal Views

| View | File | Description |
|---|---|---|
| Portal layout | `resources/views/components/portal-layout.blade.php` | Anonymous component; injects brand CSS vars |
| Portal home | `resources/views/portal/show.blade.php` | Points/stamps, rewards, birthday banner |
| Member card | `resources/views/portal/card.blade.php` | Branded card with QR + Code128 barcode |
| Disabled | `resources/views/portal/disabled.blade.php` | Shown when `portal_enabled = false` |

The portal uses **a separate layout** (`x-portal-layout`) with no sidebar, no topbar, and no merchant session. Merchant branding (primary colour, secondary colour, logo) is injected as CSS custom properties via a `<style>` block in `<head>`:

```css
:root {
    --portal-primary:   #2563eb; /* merchant brand_color */
    --portal-secondary: #1e293b; /* merchant secondary_color */
}
```

---

## 7. Security Model

### What the portal exposes

| Field | Exposed? | Notes |
|---|---|---|
| Member name | ✅ | Full name |
| Member code | ✅ | Merchant-visible alphanumeric code |
| Points / stamp balance | ✅ | Current `total_points` |
| Campaign name & type | ✅ | Active campaigns only |
| Reward name & description | ✅ | Active, available rewards |
| Redemption history | ✅ | Reward name + date only |
| Birthday banner | ✅ | If birthday reward is active and eligible |
| Member since / last visit | ✅ | Dates only |

### What the portal NEVER exposes

| Field | Reason |
|---|---|
| Member database ID | Use public_uuid only |
| Merchant ID | Internal |
| Phone number | PII |
| Email address | PII |
| Internal notes (`notes` column) | Merchant-only |
| Purchase amounts | Financial detail |
| Staff names (`created_by`, `used_by`) | Internal |
| Redemption codes | Staff use only |
| Analytics data | Internal |
| Audit logs | Internal |
| Merchant settings | Internal |

### Access model

- No authentication required for the public portal routes
- Member is resolved only by `public_uuid` (no database ID in URL)
- `public_uuid` is a UUIDv4 — 2¹²² possible values, effectively unguessable
- Merchant controls (toggle, regenerate) use the standard auth middleware + `abort_unless($member->merchant_id === $request->user()->merchant?->id, 403)` tenant check
- Disabled portals return the `portal.disabled` view (not a 404), so merchants can show a friendly message to confused customers

### QR Regeneration

When a merchant regenerates the QR code:
- A new `public_uuid` is written to the member record
- The old UUID immediately stops working (portal returns 404)
- Any printed QR codes based on the old UUID are invalidated
- Merchants must reprint/reshare the new QR

---

## 8. QR Code Strategy

QR codes encode the portal URL: `https://app.example.com/member/{public_uuid}`

**Determinism:** The same `public_uuid` always produces the same QR SVG (same input → same output). QR codes can be safely printed, cached, or embedded in PDFs.

**Format:** SVG (inline in HTML, or served via `/member/{uuid}/qr.svg`). No PNG/image generation required — no GD extension dependency.

**Error correction:** Level M (15% recovery) — good balance between data density and damage tolerance for printed QRs.

**Size:** 220×220 px in the portal. The QR SVG is vector — it scales to any print size without quality loss.

---

## 9. Digital Member Card

`GET /member/{public_uuid}/card` renders a full-page card view with:

1. **Merchant branding** (logo or name, primary/secondary colour gradient)
2. **Member name** + member code
3. **QR code** (inline SVG, 220×220)
4. **Code128 barcode** (inline SVG, member code encoded)

The card uses `window.print()` (triggered by a "Print" button) for printing. Bootstrap's `@media print` + `.d-print-none` classes hide navigation and footer.

**Dark vs light backgrounds:** The card gradient (`portal-member-card`) uses the merchant's primary and secondary colours. Text is forced white (`text-white`). The QR and barcode are wrapped in a white `div` so they remain readable regardless of background darkness.

---

## 10. Merchant Controls (in Member Show)

The existing "QR Code — Coming Soon" card in `/members/{member}` is replaced with:

- **Live QR code** (loaded via `<img src="/member/{uuid}/qr.svg">` with lazy loading)
- **"Open portal"** link (opens portal in new tab)
- **"View card"** link (opens digital member card in new tab)
- **"Disable/Enable portal"** button (PUT form)
- **"Regenerate QR"** button (POST form with Alpine.js `confirm()` guard)

The portal status badge (`enabled`/`disabled`) is shown in the card header.

---

## 11. Branding

The portal inherits branding via `MerchantBrandingService`:

| Element | Source |
|---|---|
| Primary colour | `merchant.brand_color` (fallback `#2563EB`) |
| Secondary colour | `merchant.secondary_color` (fallback `#1E293B`) |
| Logo | `merchant.logo_path` (via Storage public disk) |
| Business name | `merchant.name` |
| Tagline | `merchant.business_tagline` |

"Powered by OneMember" is shown in the portal footer per DECISION-057 (White Label Lite). The portal is public-facing and not behind the merchant's domain — customers see clearly that the platform is OneMember.

---

## 12. Email — Future Capability

`CustomerPortalService` includes three stub methods that return data arrays. These are documented for future email implementation but do **not** send any emails in V1:

| Method | Future use |
|---|---|
| `prepareMemberCardEmail(Member)` | Attach/link the digital member card |
| `prepareQrEmail(Member)` | Share QR code with a new member at signup |
| `prepareWelcomeEmail(Member)` | Send a welcome message with portal link |

When email is implemented (Sprint 7.x), these stubs provide the data contract.

---

## 13. Analytics Events

| Event | When |
|---|---|
| `portal_viewed` | Customer opens the portal page |
| `member_card_downloaded` | Customer opens the card/print page |
| `qr_scanned` | QR SVG endpoint is called |

Events are tracked via `AnalyticsService::track()`. No PII is included in event properties.

---

## 14. Localization

| File | Keys |
|---|---|
| `lang/en/portal.php` | 37 keys |
| `lang/th/portal.php` | 37 keys (identical count) |
| `lang/en/members.php` | +14 portal control keys |
| `lang/th/members.php` | +14 portal control keys |

---

## 15. Testing

**File:** `tests/Feature/CustomerPortalTest.php` (37 tests)

| Category | Count |
|---|---|
| `public_uuid` assignment and uniqueness | 3 |
| Portal access (valid UUID, 404, member name, merchant name, member code) | 5 |
| Security — PII not exposed (email, phone, notes) | 3 |
| Disabled portal (shows message, hides member data) | 2 |
| Cross-tenant isolation | 2 |
| QR endpoint (200, SVG header, deterministic, 404) | 3 |
| Digital card (200, name, QR SVG) | 3 |
| Merchant controls (toggle on/off, auth, cross-tenant, regenerate) | 5 |
| Analytics tracking (portal view, card download) | 2 |
| Member show page (portal card shown, portal link) | 2 |
| Email stubs (prepareMemberCardEmail, prepareQrEmail, prepareWelcomeEmail) | 3 |
| Branding (CSS var injection) | 1 |
| Reward display (available, locked) | 2 |

---

## 16. Future Customer App Roadmap

Per DECISION-060, native iOS/Android apps are deferred. The roadmap:

| Phase | Feature | Notes |
|---|---|---|
| V1 (current) | Responsive web portal | No account, QR access |
| V1.1 | Email: send card, QR, welcome | Uses stubs in CustomerPortalService |
| V1.2 | PWA "Add to Home Screen" | Groundwork in Sprint 6.5 manifest |
| V2.0 | Native iOS app | When merchant base demonstrates demand |
| V2.0 | Native Android app | When merchant base demonstrates demand |
| V2.x | Push notifications | Requires native app or Web Push API |
| V2.x | In-app reward redemption QR | Merchant scans customer's in-app QR |

---

*Last updated: Sprint 6.6 — 2026-06-30*
