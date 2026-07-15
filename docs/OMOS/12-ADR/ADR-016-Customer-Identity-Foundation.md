# ADR-016 — Customer Identity Foundation (CUSTOMER-001A)

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-15) |
| **Date** | 2026-07-15 |
| **Author** | Claude Fable 5 (sprint CUSTOMER-001A) |
| **Related Documents** | [ADR-010](./ADR-010-Custodian-Identity-Consent.md) (custodian identity), [ADR-008](./ADR-008-Phase-2-Customer-Wallet-Architecture.md) (wallet architecture), DECISION-100 |

---

## Context

PH2-001A (ADR-010) created the global Customer record: one verified phone = one identity, consent-gated links to merchant Members, a public card. But that identity could not *sign in* — it had no credentials, no guard, no profile self-service. The future OneMember Wallet needs customers who authenticate once and carry their identity across every merchant. CUSTOMER-001A builds that authentication and profile foundation — nothing more (no payments, no wallet UI, no delivery, no GPS).

## Decision (approved)

### One identity, extended — not a parallel one

The existing `customers` table/model becomes authenticatable. A customer who joined a shop via scan-to-join and a customer who registers an account are **the same row** — no migration of people, no duplicate identities, all ADR-010 machinery (public_uuid, onemember_id, consent links, card) untouched. Two deliberate relaxations, documented in the migration:

1. **`phone` is now nullable** — the charter requires registration with phone *or* email, the customer chooses. Phone remains the unique identity anchor when present.
2. **`email` gains a unique index** — it is now a login identifier. Production deploys must verify no duplicate customer emails exist before migrating.

### Separate guard, untouched merchant auth

New `customer` session guard + `customers` eloquent provider in `config/auth.php`. Merchant auth (`web` guard, `/login`, `/register`, Breeze) is not modified in any way. Bootstrap-level redirect callbacks split by route name: `customer.*` guests go to the customer login; authenticated customers on guest pages go to their profile, never the merchant dashboard.

### Two login methods, one form

The customer chooses at login: **Continue with Password** or **Continue with OTP** (same form, `formaction` branch — no JS dependency). Identifier is a single field, auto-detected: digits → phone (normalized to E.164), `@` → email (lowercased). Passwords are optional at registration; accounts without one are OTP-only and can add a password later in settings.

### OTP architecture

`OtpService` owns the whole lifecycle: 6-digit codes, **stored bcrypt-hashed** (never plaintext), 5-minute expiry, 5-attempt kill, single-use, one-valid-code-at-a-time supersession, 60-second resend cooldown + 5/hour cap per destination (RateLimiter). Delivery is seam-based:

- **SMS** — `Contracts\SmsProvider` with a config-driven binding (`customer_identity.sms_provider`). The only implementation is `LogSmsProvider` (writes to the log). **There is no production SMS integration and no fake production sending** — a real Thai/Myanmar gateway is one new class + one config value.
- **Email** — `CustomerOtpMail` markdown mailable, sent synchronously (the customer is waiting on the verify screen).

Contact changes re-verify by construction: the OTP `destination` **is** the pending new email/phone; verifying the code applies it. No pending-change columns exist.

### No account-existence leak

Every login-side path (OTP request, password failure, password-reset request) returns the identical generic response whether or not an account exists, and sends/stores nothing for unknown identifiers. The one deliberate exception: **registration** validates identifier uniqueness (industry-standard, rate-limited). Documented, accepted.

### Phone normalization

`PhoneNumberService` — pragmatic E.164 (not libphonenumber): country dial codes + national-length rules in `config/customer_identity.php` (TH +66, MM +95 today). National input, dial-code-without-plus, and `+international` all normalize; new countries are config entries.

### Future identity providers — architecture only

`Contracts\IdentityProvider` (`key()` / `redirectUrl()` / `resolveCustomer()`) is the seam Apple, Google, LINE, Facebook, and Enterprise SSO plug into: one implementation per provider registered in config, one shared callback controller, one button per provider on the login screen. The `customer_identities` link table (customer_id, provider, provider_user_id) is **deliberately not migrated** until the first real provider ships, so its schema can follow that provider's actual claims. Zero implementations today, per charter.

## Security summary

Rate limiting (login lockout per identifier+IP from config; OTP request/verify and registration per IP; per-destination cooldown + hourly cap inside the service) · bcrypt password hashing via the `hashed` cast, `Password::defaults()` (12+ mixed/numbers/symbols) · session regeneration on every authentication · CSRF on all forms · normalization before validation (E.164 / lowercase) · generic auth failures · suspended-status gate on every path · SecurityLogger now identity-aware (a phone-only customer login previously crashed the global Login listener — fixed, merchant logging unchanged).

## Consequences

- **Positive:** the wallet foundation exists without disturbing a single merchant flow; guest checkout remains fully possible (signing in is optional and additive); every future auth surface (wallet UI, saved addresses, order history) hangs off one guard and one identity.
- **Negative / accepted:** phone-only accounts can't recover via email (documented); no `customer_identities` table yet (intentional); LogSmsProvider means no real SMS until a gateway sprint.
- **Reversibility:** high — dropping the guard, routes, and additive columns restores PH2-001A exactly; `customers` rows never change meaning.
