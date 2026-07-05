# 06 — Privacy & Consent Model (PDPA)

| Field | Value |
|---|---|
| **Status** | Review — requires legal review (BD-07) |
| **Last Updated** | 2026-07-05 |
| **Parent** | [README.md](./README.md) |

---

## 1. Roles under Thailand PDPA

| Party | Role |
|---|---|
| OneMember | Data controller for wallet account data; data processor for merchant Member data |
| Merchant | Data controller for their Member records (unchanged from Phase 1) |
| Customer | Data subject |

The wallet introduces a second controller relationship — this is why consent is **per merchant**, not global.

## 2. Lawful Bases

| Data | Basis |
|---|---|
| Wallet account (phone, name) | Contract (providing the wallet service) |
| Sharing profile/birthday with a merchant | **Consent** (explicit, per data type) |
| Marketing messages from a merchant | **Consent** (separate checkbox, never pre-ticked) |
| Transaction records at a merchant | Merchant's own basis (contract) — exists in Phase 1 regardless of wallet |
| Anonymised network analytics | Legitimate interest, aggregate-only (Roadmap: "Privacy analytics (anonymised)") |

## 3. Consent Data Types

| `data_type` | What the merchant gets when granted |
|---|---|
| `profile` | Customer name/nickname sync into their Member record |
| `birthday` | Birthday sync (enables birthday bonus for wallet-joined members) |
| `marketing` | Win-back, birthday greeting, campaign emails (MVP-006/008 streams) |
| `analytics` | Inclusion in merchant-visible engagement analytics beyond raw transactions |

Rules:
- Join flow asks for the four toggles; `profile` on by default (needed to create the Member record — disclosed), `birthday`/`marketing`/`analytics` **off by default**, never pre-ticked.
- Consent copy is versioned (`consent_version`); republishing text requires re-consent only for materially changed types (legal call, BD-07).
- Withdrawal is one tap in the Privacy Centre, effective ≤ 24 h (suppression job), confirmed by email.

## 4. Consent Enforcement Points (single write/read path)

- `ConsentService::current($customer, $merchant)` is the **only** API for reading consent state.
- `MemberEmailSubscriber` / `EmailEventSubscriber` (winback) check `marketing` consent for wallet-linked members before queuing.
- Member profile sync jobs check `profile`/`birthday`.
- Merchant analytics queries exclude non-consenting linked members from `analytics`-gated views (raw Phase 1 transaction data is unaffected — it is the merchant's own record).

## 5. Data Subject Rights

| Right | Implementation |
|---|---|
| Access / portability | `/settings → Export my data`: queued job builds JSON (account, links, consents, per-merchant balances) → time-limited signed download link by email |
| Rectification | Editable profile; merchant-side Member data corrected via merchant (controller) |
| Erasure | Account deletion: wallet data hard-deleted after 7-day cooling-off; links severed; Member records remain with merchants (their controllership) — disclosed clearly in the deletion screen |
| Withdraw consent | Privacy Centre, per merchant per type, immediate append + ≤24 h propagation |
| Object | Support channel (`privacy@onemember.co`, existing config key) |

## 6. Export Format

Single JSON file, schema versioned, human-readable keys, Thai/English labels — satisfies portability without building a UI. CSV per-merchant balances included for convenience.

## 7. Retention (pending BD-10 confirmation)

| Data | Proposed retention |
|---|---|
| Inactive customer account (no login, no transaction on any link) | 24 months → anonymise (name/phone/email hashed, links severed) |
| OTP challenges | 24 hours |
| Consent audit rows | 5 years after account closure (legal defence) |
| Pass registrations | Deleted on pass revocation |

## 8. What We Never Do (Bible: Non-Roadmap + trust model)

- Sell or share customer data across merchants without consent.
- Cross-merchant profiling visible to merchants ("this customer also shops at…") — **never**, even with analytics consent; only OneMember-internal anonymised aggregates.
- Ad targeting of any kind (excluded roadmap item).
