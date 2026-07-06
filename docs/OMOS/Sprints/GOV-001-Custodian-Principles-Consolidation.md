# GOV-001 — Foundational Principles Consolidation (Custodian Model, Identity, Commerce)

| Field | Value |
|---|---|
| **Sprint ID** | GOV-001 |
| **Status** | ✅ Complete (documentation only — records Product Owner decisions of 2026-07-06) |
| **Sprint Type** | Governance / Documentation |
| **Developer** | Claude Fable 5 (as Chief Product Architect) |
| **Completed** | 2026-07-06 |

## What Happened

The Product Owner issued foundational, approved decisions: the custodian principle (OneMember does not own customers), the one-line platform principle, the identity model (one phone = one identity, consent-gated linking, token-only QR, scan-to-join), the privacy/access model (subscription-gated merchant access, no automatic sharing), the commerce principles (Phase 4, merchant of record, no money through OneMember, no commissions, merchant-controlled fulfillment, restaurant delivery radius), and the phase positioning (P1 platform / P2 wallet / P3 growth tools / P4 storefront).

## Recorded In

- **Product Bible v1.0.0** (Active) — foundational principle, identity/privacy/commerce sections, roadmap positioning, custodian test added to Product Rules
- **ADR-010** (Approved) — custodian identity/consent/access model; resolves BD-02, BD-05; supersedes the auto-link recommendation
- **ADR-011** (Approved) — commerce principles + Phase 3/4 re-sequencing; re-scopes PromptPay to QR payment display
- **Commerce.md v1.0.0** — full Merchant Storefront principles + Phase 4 open items
- **Customer-Wallet package** — README BD register updated (BD-02/04/05 decided); Doc 08: consent-first claim, new §4b scan-to-join flow, §6 subscription-gated access
- **Glossary** — Custodian, OneMember Identity, OneMember Card/ID, Scan-to-Join, Consent, Merchant Storefront
- **DECISION-076 / DECISION-077** in docs/08-Product-Decisions.md
- **Roadmap docs** — supersession banners + Master Roadmap v1.1.0 phase table fix + DR-31

## Still Open (not invented)

BD-01 (Phase 2 start vs exit criteria), BD-03 (wallet pricing), BD-04 budget sign-off (accounts/certs), BD-06, BD-07, BD-08, BD-09, BD-10, DR-31 (regional expansion placement), Phase 4 planning items in Commerce.md, and the pre-existing DR-01…DR-30 register.

## Constraint Compliance

No application code, migrations, or route changes. Nothing pushed or deployed. One documentation commit.
