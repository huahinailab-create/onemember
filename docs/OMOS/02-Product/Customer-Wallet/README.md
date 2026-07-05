# Customer Wallet — Phase 2 Design Package

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Review — awaiting Product Owner approval |
| **Last Updated** | 2026-07-05 |
| **Author** | Claude Fable 5 (design sprint PH2-000) |
| **Related Documents** | [Product-Bible.md](../Product-Bible.md), [ADR-008](../../12-ADR/ADR-008-Phase-2-Customer-Wallet-Architecture.md), [Long-term-Roadmap.md](../../09-Roadmap/Long-term-Roadmap.md) |

---

## What This Package Is

The complete, production-ready design for Phase 2 — Customer Wallet. **No application code exists yet.** Implementation may not begin until (a) the Open Business Decisions below are resolved by the Product Owner and (b) ADR-008 is moved from `Proposed` to `Approved`.

## Package Contents

| # | Document | Covers |
|---|---|---|
| 1 | [01-Functional-Specification.md](./01-Functional-Specification.md) | Features, user stories, edge cases, success metrics |
| 2 | [02-Technical-Specification.md](./02-Technical-Specification.md) | Stack, domains, services, jobs, notifications |
| 3 | [03-Database-Design.md](./03-Database-Design.md) | New tables, relations, migration plan |
| 4 | [04-API-Design.md](./04-API-Design.md) | Wallet REST API v1, auth, versioning, errors |
| 5 | [05-Security-Model.md](./05-Security-Model.md) | Auth guards, tokens, QR signing, threat model |
| 6 | [06-Privacy-Consent-Model.md](./06-Privacy-Consent-Model.md) | PDPA consent per merchant/data-type, withdrawal, export |
| 7 | [07-Wallet-Pass-Integrations.md](./07-Wallet-Pass-Integrations.md) | Apple Wallet (PKPass) + Google Wallet designs |
| 8 | [08-Identity-and-Flows.md](./08-Identity-and-Flows.md) | Universal identity, QR join/share, member onboarding |
| 9 | [09-Diagrams.md](./09-Diagrams.md) | Customer journeys, sequence diagrams, architecture |
| 10 | [10-UI-Wireframes.md](./10-UI-Wireframes.md) | Wallet screens (mobile-first Bootstrap 5) |
| 11 | [11-Risks-and-Scalability.md](./11-Risks-and-Scalability.md) | Risk register, mitigation, scale path |

Plus: [ADR-008 — Phase 2 Customer Wallet Architecture](../../12-ADR/ADR-008-Phase-2-Customer-Wallet-Architecture.md) (status: **Proposed**).

---

## ⛔ Open Business Decisions (must be resolved before implementation)

Per EXECUTE.md, none of these are assumed. Each needs a Product Owner decision recorded in `docs/08-Product-Decisions.md` (and CEO-Decisions.md where marked).

| ID | Decision Needed | Options / Recommendation | Blocking |
|---|---|---|---|
| **BD-01** | **Start Phase 2 before Phase 1 exit criteria are met?** Long-term-Roadmap requires 1,000+ paying merchants and 50,000+ members. Current numbers are below this. | (a) Wait for criteria; (b) approve early start with revised criteria. CEO decision. | Everything |
| **BD-02** | **Customer authentication method.** | (a) Phone + SMS OTP (recommended — matches Thai consumer behaviour, no password support burden; requires SMS provider budget ~0.3–0.6 THB/OTP); (b) email + password; (c) LINE Login only. | Identity, DB, API |
| **BD-03** | **Wallet pricing.** Is the wallet free for consumers and included in all merchant plans, or a paid merchant add-on? | Recommendation: free for consumers, included for all plans (network effects are the point). CEO decision — pricing. | GTM, feature gates |
| **BD-04** | **Apple/Google Wallet are not in the Product Bible.** Amend the Bible to include native wallet passes as a Phase 2 deliverable? Requires Apple Developer Program (USD 99/yr), pass-type certificate, and a Google Wallet API issuer account. | Approve Bible amendment + budget, or defer passes to Phase 2.1. | Doc 07 |
| **BD-05** | **Member deduplication rule.** When a wallet customer's phone matches an existing Member's phone at a merchant, auto-link, or require merchant/customer confirmation? | Recommendation: auto-link after customer verifies that phone via OTP (they proved ownership); notify merchant. | Identity flow |
| **BD-06** | **Merchant opt-out.** Can a merchant opt out of wallet discovery (their programme invisible in the wallet directory) while still using Phase 1 features? | Recommendation: yes — `wallet_visible` merchant setting, default on. | Consent model |
| **BD-07** | **Consent copy + PDPA legal review.** Consent text (Thai + English) must be reviewed by Thai counsel before launch. | Engage counsel; budget item. CEO decision. | Launch gate |
| **BD-08** | **Notification channel priority.** Web push + email at launch, or hold for LINE OA integration (PH2-002)? | Recommendation: email at launch (infrastructure exists), web push Phase 2.1, LINE when PH2-002 is specced. | Scope |
| **BD-09** | **SMS OTP provider.** Thai delivery quality matters. | Candidate shortlist needed (e.g., local aggregator vs global CPaaS); cost & PDPA data-processing agreement review. CEO decision — vendor contract. | BD-02 |
| **BD-10** | **Data retention windows.** How long do we keep inactive customer accounts and withdrawn-consent audit trails? PDPA requires defined retention. | Proposal in Doc 06 §7 (24 months inactive → anonymise; consent audit 5 years). Needs legal confirmation. | Privacy model |

---

## Design Principles Applied

1. **No new architecture where existing architecture serves.** Same Laravel monolith, same single database with strict scoping, Bootstrap 5, database queues, event-driven email (CTO-003/004/005, ADR-004/005).
2. **Phase 1 is untouched.** Members, campaigns, transactions keep their schema. The wallet links to Member records; it never replaces them.
3. **Consent before data.** No merchant sees wallet-side data without explicit, per-merchant, per-data-type consent (PDPA).
4. **Everything additive.** Every migration is reversible; no existing column is modified.
