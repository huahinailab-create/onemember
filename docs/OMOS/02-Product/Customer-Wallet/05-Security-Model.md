# 05 — Security Model

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Identity & Session

- New `customer` auth guard; customers are **not** rows in `users` (merchant staff/admin identity stays fully separate — no privilege path from wallet to merchant app).
- Primary factor: phone possession via OTP (BD-02). OTP codes: 6 digits, bcrypt-hashed at rest, 5-minute TTL, 5 attempts, per-phone and per-IP rate limits, constant response shape (no phone enumeration).
- Web sessions: same hardened session config as app domain (database driver, secure, httpOnly, sameSite=lax). API: Sanctum tokens, revocable, per-device naming, 90-day idle expiry.
- Email verification (CEO-006) applies when a customer adds an email.

## 2. Authorization & Tenancy

- Every wallet query scopes by authenticated `customer_id`.
- Merchant-side reads of wallet data occur **only** through `customer_member_links` + current consent state — enforced in `WalletLinkService`/`ConsentService`, never ad-hoc in controllers.
- Existing merchant scoping (CTO-005) unchanged; `WalletTenantIsolationTest` extends `TenantIsolationTest` patterns to the customer dimension.
- Laravel Policies are adopted for the new wallet models (REF-001 begins here rather than retrofitting Phase 1).

## 3. Abuse & Fraud Controls

| Threat | Control |
|---|---|
| OTP SMS pumping (toll fraud) | Per-phone + per-IP limits, Thai number plan allow-list at launch, daily spend alarm on SMS provider |
| Fake joins farming welcome bonuses | Join rate-limit; birthday bonus idempotency already per-member/year; merchant-visible provenance (`linked_via`) |
| QR replay at counter | Member presentation QR embeds rotating token (existing regenerate mechanic) + 60s TOTP-style suffix for wallet-presented QRs; staff scan validates freshness |
| Universal join QR tampering | Signed payload (HMAC, `APP_KEY`-derived key): `{merchant_slug, v, sig}` — forged slugs rejected |
| Enumeration of member data via claim flow | Claim requires OTP-verified phone equality; response never reveals whether a phone exists at a merchant |
| Pass web-service scraping | Apple `authenticationToken` per pass (hashed at rest), compared constant-time |

## 4. QR Formats

| QR | Payload | Trust |
|---|---|---|
| Universal join (poster) | `https://wallet.onemember.co/join/{slug}?v=1&sig=…` | Public, signed, non-expiring; revocable by rotating slug |
| Wallet member QR (on-screen) | `OM1:{member_code}:{totp6}` | Short-lived suffix; Counter Mode validates |
| Printed member card QR (Phase 1) | unchanged | existing |

## 5. Data Protection

- PII encryption at rest for `customers.birthday` and `customers.email` (Laravel encrypted casts) — phone stays queryable-plaintext (lookup key) but is masked in logs (`08x-xxx-1234`).
- No PII in URLs (existing privacy rule); UUIDs only.
- Secrets (`APPLE_PASS_CERT_PATH`, `GOOGLE_WALLET_SA_JSON`, SMS keys) in `.env`/secret store only.
- Audit: consent changes, account deletion, claim events → append-only tables + `SecurityLogger`.

## 6. Headers & Transport

Wallet domain inherits the existing `SecurityHeaders` middleware (CSP, HSTS, nosniff). SEC-002 (`unsafe-inline`) remediation applies to new wallet views from day one: no inline styles/scripts in wallet Blade files.

## 7. Threat Model Summary (STRIDE)

| Category | Highest-value asset | Primary mitigation |
|---|---|---|
| Spoofing | Customer identity | OTP + rate limits |
| Tampering | Join QR / consent records | HMAC signing / append-only |
| Repudiation | Consent history | Versioned append-only + timestamps |
| Info disclosure | Cross-merchant profile data | Consent gate in single service write/read path |
| DoS | OTP SMS budget | Limits + spend alarms |
| Elevation | Wallet → merchant app | Separate guard, separate tables, no shared sessions |
