# ADR-008 — Phase 2 Customer Wallet Architecture

| Field | Value |
|---|---|
| **Status** | **Accepted as foundation** (Product Owner, 2026-07-05) — business decisions BD-01…BD-10 remain individually open and gate implementation |
| **Date** | 2026-07-05 |
| **Author** | Claude Fable 5 (design sprint PH2-000) |
| **Supersedes** | None |
| **Superseded by** | None — linking/consent semantics refined by [ADR-010](./ADR-010-Custodian-Identity-Consent.md) (2026-07-06): consent-gated claim supersedes auto-link, scan-to-join added, subscription-gated merchant access |
| **Related Documents** | [Customer Wallet Design Package](../02-Product/Customer-Wallet/README.md), [Long-term-Roadmap.md](../../09-Roadmap/Long-term-Roadmap.md), [ADR-004](./ADR-004-Laravel-Architecture.md), [ADR-005](./ADR-005-Bootstrap-5-Standard.md) |

---

## Context

The Long-term Roadmap requires the Customer Wallet to launch Phase 2, and Roadmap.md states: "Major phase changes (e.g., starting Phase 2) require an explicit decision recorded in docs/OMOS/12-ADR/." This ADR is that decision instrument, plus the binding architectural choices for the wallet.

Phase 1 exit criteria (1,000+ paying merchants, 50,000+ members) are **not yet certified as met** — approving this ADR includes explicitly waiving or revising those criteria (BD-01).

## Decision (proposed)

1. **Same monolith, third domain.** The wallet is a domain group (`wallet.onemember.co`) inside the existing Laravel application — not a separate service. Rationale: team size, shared models, proven domain-routing pattern (DECISION-066). A future extraction seam is documented (Design Doc 11 §2.5) but not built.
2. **Separate identity plane.** Customers live in a new `customers` table with their own auth guard — never rows in `users`. No shared sessions or privilege paths between wallet and merchant app.
3. **Link, don't merge.** Wallet ↔ Phase 1 integration is exclusively via `customer_member_links`. The `members` table is not altered; merchants remain data controllers of their Member records.
4. **Consent is append-only and centrally enforced.** All consent reads/writes go through `ConsentService`; consent state gates every cross-boundary data flow (PDPA).
5. **Phone-first identity with SMS OTP** (subject to BD-02/BD-09).
6. **Wallet API v1 via Sanctum** serves web PWA and future native clients; the Enterprise Bridge is a separate, later API (PH2-003).
7. **Native passes (Apple PKPass, Google Wallet)** are architected as optional, queue-driven pass services — implementable independently (BD-04) without touching wallet core. *Note: passes are not currently in the Product Bible; approval of this ADR with BD-04=yes constitutes the Bible amendment trigger.*
8. **Feature-flagged rollout** (`FEATURE_WALLET`), dark-merged, staging-first.

## Options Considered

### Option A — Wallet inside the monolith (proposed)
**Pros:** Reuses models, auth infra, queue, branding, localization; one deploy; fastest to market; extraction seam preserved.
**Cons:** Shared blast radius; consumer traffic on merchant infrastructure (mitigated: rate limits, monitoring, split path documented).

### Option B — Separate wallet microservice + API to core
**Pros:** Independent scaling, isolation.
**Cons:** Team of effectively one developer; duplicated auth/branding/i18n; distributed-data consent enforcement is far harder to get right; contradicts KISS and current OMOS standards.

### Option C — Extend the Phase 1 public portal (no accounts)
**Pros:** Minimal work.
**Cons:** No identity → no cross-merchant wallet, no consent centre, no network effect. Fails the Bible's Phase 2 definition outright.

## Consequences

- Six new tables (Design Doc 03); zero Phase 1 schema changes.
- New OMOS standards implications: wallet views are the first SEC-002-clean (no inline styles) surface; Policies (REF-001) start with wallet models.
- Operational additions: SMS provider dependency, Apple/Google certificates lifecycle (if BD-04 approved), consumer support channel (R-10).
- Ten business decisions (BD-01…BD-10, package README) must be recorded in `docs/08-Product-Decisions.md` / `CEO-Decisions.md` before implementation begins.

## Approval

| Gate | Owner | Status |
|---|---|---|
| BD-01 phase start | Product Owner (CEO) | ⬜ Pending |
| BD-02…BD-10 | Product Owner (+ CTO where technical) | ⬜ Pending |
| ADR-008 status → Approved | Product Owner | ⬜ Pending |
