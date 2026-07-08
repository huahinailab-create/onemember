# ADR-015 — Store Identity & Public URL Foundation

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-08) |
| **Date** | 2026-07-08 |
| **Author** | Claude Fable 5 (sprint OMEGA-001E) |
| **Related Documents** | [DECISION-098](../../08-Product-Decisions.md), [ADR-013](./ADR-013-Unified-Media-Foundation.md) (Unified Media Foundation — same centralization pattern), [ADR-014](./ADR-014-Reusable-Media-Upload-UI.md) |

---

## Context

`merchants.name` (Business Name) and `merchants.slug` (Store URL) have existed as separate columns since the earliest migration, but the platform never treated them as separate *concepts* with separate rules. Slug generation logic lived inline in `Merchant::booted()`; nothing enforced reserved words; merchants could not edit their own Store URL; and merchant-facing UI never referred to it by a name a small-business owner would understand ("slug" is developer jargon).

This sprint formalizes the distinction and gives merchants control over their Store URL, while keeping Business Name's editing behavior completely unconstrained (per OMEGA-001D, `displayName()` only affects *presentation*, never the stored value).

## Decision (approved)

**Two identities, one merchant:**

| | Business Name (`merchants.name`) | Store URL (`merchants.slug`) |
|---|---|---|
| Purpose | Brand, shown to customers | Path segment for the public storefront (`/store/{slug}`) and future modules |
| Editable | Freely, no format constraint | Freely, but format-constrained (lowercase, hyphen-separated, ASCII) |
| Case/punctuation | Exactly as typed, always | Normalized on generation and edit |
| UI label | "Business Name" | **"Store URL"** — never "slug" in merchant-facing copy |

**`App\Services\StoreIdentity\StoreIdentityService`** is the single place that:
- `sanitize()` — the exact transformation (`Str::slug()`) applied to free text to produce a candidate Store URL
- `isReserved()` / `reservedWords()` — reads `config/store_identity.php`, documented in one place per the spec's Part 8
- `isAvailable()` — not reserved, not blank, not already taken (checks `withTrashed()`, since the DB unique index isn't soft-delete-aware)
- `uniqueSlugFor()` — generation with collision suffixing (`-2`, `-3`, ...) and an empty/reserved-word fallback to `merchant`
- `publicStoreUrl()` — the full public URL, resolved through the real `storefront.show` route rather than string concatenation

`Merchant::booted()`'s creating hook now calls `app(StoreIdentityService::class)->uniqueSlugFor()` instead of the private static method that used to hold this logic — same algorithm, same output for every existing test case, just relocated so there's one home for it (Part 6: "Future modules must use it instead of directly reading `Merchant::slug`").

### Editing (Settings → Business Profile)

The `slug` field is optional on the profile form: blank means "leave unchanged," never "clear it." Server-side validation (`UpdateMerchantProfileRequest`) enforces format (`^[a-z0-9]+(-[a-z0-9]+)*$`), uniqueness (`Rule::unique('merchants','slug')->ignore($merchantId)`), and reserved-word rejection — the authoritative check. Client-side JS mirrors the same sanitize rule as-you-type (so what the merchant sees matches what the server will accept), debounces a call to a new read-only `GET /settings/store-url/availability` endpoint for a live available/taken/reserved indicator, and live-updates a copyable public-URL preview. On submit, if the value actually changed, a plain `window.confirm()` warns that existing marketing links and printed QR codes pointing at the old URL will stop working — **the user can cancel**; there is no redirect, alias, or history mechanism. Building that is explicitly out of scope for this sprint.

### Reserved Words

Centralized in `config/store_identity.php`: the exact list named in the OMEGA-001E spec (`admin`, `api`, `login`, `register`, `dashboard`, `store`, `queue`, `booking`, `commerce`, `settings`, `support`, `help`, `docs`, `privacy`, `terms`) plus every other real top-level segment in `routes/web.php` today, reserved defensively in case a future scheme ever serves a Store URL directly under the app root instead of under `/store/{slug}`.

## Backward Compatibility (Part 5)

No migration — `merchants.slug` is reused as-is. No existing route changed. Existing merchants' stored slugs are never touched by this sprint (the service is only invoked at *creation* time, or when a merchant explicitly submits a new value). Storefront, Join, Launch Kit, Commerce, and Identity all continue reading `$merchant->slug` exactly as before — this sprint does not migrate their call sites to the new service (Part 6 says "no behavioural changes today, only centralize" for *future* modules; retrofitting every existing reader was judged out of scope and unnecessary risk for a Type B sprint).

## Rationale

Separating "what the algorithm does" (generate/validate a Store URL) from "where it's called from" (model hook, form request, live-check endpoint, and eventually Storefront/Queue/Booking/Procurement/Customer Portal/Identity/Launch Kit/future APIs) means the collision-safety and reserved-word rules are enforced identically everywhere, and adding a ninth caller later requires zero new logic — the same discipline ADR-013 established for media uploads.

## Consequences

- **Positive:** merchants can now self-serve a cleaner Store URL without a support ticket; reserved words are enforced consistently at both generation and edit time from one config file; any future module needing a merchant's public path has a ready-made, tested service instead of reinventing slug logic.
- **Negative / accepted:** changing a Store URL has no redirect — old links 404 after a change. This is a deliberate, spec-mandated scope cut ("Do NOT implement redirects. Only warn.") and is the clearest follow-up candidate once Merchant Readiness work begins.
- **Reversibility:** high. `StoreIdentityService` and the Settings edit UI can be removed with no data-shape impact; `merchants.slug` never changed meaning or column type.

## Future Recommendations

1. **Slug history / redirect table** — if merchants change their Store URL in practice, a lightweight `merchant_slug_history` (old_slug, merchant_id, redirected_until) would let `/store/{slug}` 301 old links for a grace period. Deferred per this sprint's explicit "warn, don't redirect" instruction.
2. **Migrate existing readers to `StoreIdentityService`** — `StorefrontController`, `JoinLandingController`, Launch Kit, Identity, and Commerce controllers currently read `$merchant->slug` directly; migrating them to `StoreIdentityService::storeUrl()`/`publicStoreUrl()` would complete the centralization with no behavior change, at low risk, whenever one of them is next touched.
3. **Rate-limit the availability-check endpoint** if abuse becomes a concern before it's used elsewhere (currently auth-protected and merchant-scoped, which is sufficient for its single caller today).
