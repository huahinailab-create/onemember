# 02 — Technical Specification

| Field | Value |
|---|---|
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Stack Position

No new architecture (ADR-004/005, CTO-003/004/005 all apply):

| Concern | Choice |
|---|---|
| Runtime | Same Laravel 13 monolith, PHP 8.3+ |
| Domain | `wallet.onemember.co` — third domain group in `config/domains.php` + `routes/web.php` (same pattern as corporate/app split, DECISION-066) |
| UI | Blade + Bootstrap 5, mobile-first (wallet is a phone product) |
| DB | Same single database; new tables prefixed logically (`customers`, `customer_*`, `consents`) |
| Auth | New `customer` guard (session) + Laravel Sanctum for the wallet API (Sanctum ships with Laravel; not a new architectural dependency) |
| Queue / email | `database` queue, event-driven mailables (CTO-003) |
| Locale | Same SetLocale chain; wallet default Thai (DECISION-067 logic, customer-level `locale` preference wins) |
| PWA | Wallet views reuse the existing manifest/PWA groundwork from the mobile sprint; installable home-screen wallet |

## 2. Route Groups

```
wallet.onemember.co
├── /                      wallet dashboard (auth:customer)
├── /join/{merchantSlug}   universal QR landing (guest ok)
├── /auth/*                register / OTP / login / logout
├── /m/{link}              membership detail (auth:customer)
├── /privacy               consent centre (auth:customer)
├── /settings              profile, locale, export, delete
└── /api/wallet/v1/*       Sanctum-guarded JSON API (Doc 04)
```

Existing domains are untouched. The Phase 1 public portal (`/portal/{uuid}`) remains for non-wallet members indefinitely.

## 3. New Application Components

| Component | Type | Responsibility |
|---|---|---|
| `Customer` | Model | Wallet identity (Doc 03) |
| `CustomerMemberLink` | Model | Customer ↔ Member join with consent snapshot |
| `Consent` | Model | Versioned consent records |
| `WalletPass` | Model | Issued Apple/Google passes + update state |
| `WalletAuthController` | Controller | Register, OTP request/verify, login, logout |
| `WalletDashboardController` | Controller | Card list, membership detail |
| `WalletJoinController` | Controller | Universal QR landing, join, claim-existing flows |
| `ConsentController` | Controller | Grant/withdraw/view consents |
| `WalletSettingsController` | Controller | Profile, export, account deletion |
| `WalletLinkService` | Service | Linking/claiming logic incl. BD-05 dedup rules |
| `ConsentService` | Service | Single write-path for consent (versioned, audited) |
| `OtpService` | Service | OTP issue/verify, rate limiting, provider adapter (BD-09) |
| `PassKitService` / `GoogleWalletService` | Services | Pass generation/update (Doc 07) |
| `WalletQrService` | Service | Signing + resolving universal QRs (Doc 05 §4) |

## 4. Events & Listeners (extends MVP-006 pattern)

| Event | Fired by | Listener action |
|---|---|---|
| `CustomerRegistered` | WalletAuthController | Welcome email |
| `MembershipLinked` | WalletLinkService | Confirmation email; notify merchant dashboard counter |
| `ConsentChanged` | ConsentService | Audit log write; propagate withdrawal to merchant email suppression |
| `WalletBalanceChanged` | existing `MemberPointsEarned` / `MemberRewardRedeemed` listeners re-dispatch when member is linked | Queue pass update (Apple push / Google API), wallet notification |

**Email dedup rule (W-40):** when a Member is wallet-linked and the customer has notification consent, the MVP-006 member email is suppressed in favour of the wallet notification — one email per event, decided in `MemberEmailSubscriber` by checking `customer_member_links`.

## 5. Scheduled Jobs

| Command | Schedule | Purpose |
|---|---|---|
| `wallet:expire-otps` | every 5 min | Purge expired/consumed OTPs |
| `wallet:refresh-passes` | hourly | Retry failed pass updates (idempotent) |
| `wallet:anonymise-inactive` | daily | Retention policy (Doc 06 §7, pending BD-10) |

## 6. Feature Flags

Single config flag `features.wallet` (env `FEATURE_WALLET`) gates all wallet routes so the code can merge dark and launch by config — consistent with "production-safe configuration" constraint. Per-merchant `wallet_visible` setting (BD-06) gates discovery.

## 7. Testing Strategy

- Feature tests per controller (auth, join, consent, settings) — same conventions as `CrudCoverageTest`
- `WalletTenantIsolationTest` — customer A can never read customer B; merchant scoping preserved through links
- Consent propagation tests (withdrawal suppresses merchant marketing email within the job window)
- Pass generation unit tests with fixture certificates (never real keys in repo)
- Translation completeness already guarded by `TranslationCompletenessTest`
