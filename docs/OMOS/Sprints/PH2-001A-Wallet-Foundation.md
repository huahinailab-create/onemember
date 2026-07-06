# PH2-001A — Wallet Foundation: Domain, Identity, OTP Auth

| Field | Value |
|---|---|
| **Status** | ✅ Implemented 2026-07-06 as the **OneMember Identity Platform** (PO-directed re-scope: identity + card + scan-to-join delivered; wallet domain + OTP auth deferred — see delivery note below) |
| **Classification** | Type B |
| **Complexity** | Large (auth surface) |
| **Dependencies** | ADR-008 Approved; SCALE-001 (rate-limiter + Redis infra); SMS provider contract (BD-09) |

## Objective
Stand up `wallet.onemember.co` behind `FEATURE_WALLET`, with customer registration/login via phone OTP — no memberships yet.

## Files Expected to Change
- `config/domains.php`, `routes/web.php` (wallet domain group), `config/features.php` (new)
- Models: `Customer`, `CustomerOtp`; `config/auth.php` (customer guard/provider)
- `app/Services/OtpService.php` + `app/Contracts/SmsProviderInterface.php` + provider adapter + `log` fake driver for dev
- Controllers: `Wallet/AuthController`, `Wallet/SettingsController` (profile basics)
- Views: `resources/views/wallet/` (layout `x-wallet-layout`, auth screens W1–W2, settings) — no inline styles (SEC-002-clean)
- `lang/en|th/wallet.php` (new)
- Policies for wallet models (REF-001 starts here)

## Database Impact
Migrations 1–2 of design Doc 03: `customers`, `customer_otps`. Additive only.

## Test Plan
- OTP request/verify/expiry/rate-limit/attempt-cap tests (fake SMS driver)
- Guard separation: customer session cannot reach `app.` routes and vice versa
- Feature-flag off → wallet routes 404
- Locale default th; translation completeness auto-guarded

## Acceptance Criteria
1. Customer can register + sign in on wallet domain with OTP in ≤ 3 screens.
2. No `users`-table involvement; guards fully isolated (test-proven).
3. `FEATURE_WALLET=false` hides the entire surface.
4. OTP abuse limits per Design Doc 04 §4 enforced and tested.

---

## Delivery Note (2026-07-06)

Implemented per the Product Owner's PH2-001A directive as the **Identity Platform** rather than the wallet-domain/OTP scope above:

**Delivered:** `customers` (global identity, one phone = one identity, OM-XXXX-XXXX OneMember ID), `customer_member_links`, append-only `consents` ledger, `IdentityService` (find-or-create on member registration, HMAC-signed token-only QR, consent-gated scan-to-join with duplicate-membership prevention and existing-member connect), public OneMember Card (`/omid/{uuid}`), merchant "Add Existing OneMember Member" workflow with customer consent screen (field-level approval), audit logging on every identity event, TH/EN localization, `FEATURE_IDENTITY` flag, 23 tests. Commit: see CHANGELOG / git log (PH2-001A).

**Deferred (unchanged blockers):** wallet domain group + customer OTP auth/session (BD-09 SMS vendor), wallet dashboard (PH2-001D), passes (PH2-001E — BD-04 budget). Consent screen currently runs on the merchant device with the customer present (counter reality); it moves to the customer's own device when wallet auth ships.
