# PH2-001A — Wallet Foundation: Domain, Identity, OTP Auth

| Field | Value |
|---|---|
| **Status** | 🔲 Planning — blocked by BD-01, BD-02, BD-09; design: [Package Docs 02/03/05](../02-Product/Customer-Wallet/README.md) |
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
