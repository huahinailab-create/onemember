# ADR-001 — Customer Wallet Is the Core Network Asset

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-02 |
| **Author** | ChatGPT CTO |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [CEO-Decisions.md](../CEO-Decisions.md), [ADR-002](./ADR-002-Merchant-Growth-Platform.md), [00-Executive/Vision.md](../00-Executive/Vision.md) |

---

## Context

OneMember launched as a merchant-facing loyalty platform. The immediate product (Phase 1) is a SaaS tool for merchants to run points and stamp loyalty programmes. However, the long-term value of the business depends on a consumer-facing wallet that aggregates all loyalty memberships across merchants.

The architectural decision required here: is the Customer Wallet a separate product or an integral module of the same platform? And what does that decision mean for Phase 1 architecture choices?

## Decision

**The Customer Wallet is an integral module of the OneMember platform, not a separate product.** The Phase 1 merchant SaaS is architected to accommodate the wallet from day one, even though the wallet does not launch until Phase 2.

Specifically:
- The `members` table schema is designed to eventually support a linked consumer identity (a future `users.wallet_id` foreign key)
- QR join flows generate URLs that can be redirected through a future wallet app without changing the merchant-side QR code
- The `merchant_id` scoping that isolates member data per merchant is also the foundation of the consent model (a member's relationship to a merchant is always explicit)

## Options Considered

### Option A — Wallet as separate product (not chosen)
Build Phase 1 entirely for merchants. Build the wallet as a completely separate product in Phase 2 with a separate database and a separate identity system.

**Pros:** Simpler Phase 1. No cross-concern thinking required.  
**Cons:** Phase 2 requires migrating member identity — a complex, risky migration. Creates a seam in the system that is expensive to bridge.

### Option B — Wallet built in Phase 1 (not chosen)
Build both merchant SaaS and consumer wallet simultaneously in Phase 1.

**Pros:** No Phase 2 migration required.  
**Cons:** Doubles Phase 1 scope. Splits focus before product-market fit is established in either segment.

### Option C — Phase 1 merchant-only, but wallet-ready architecture (chosen)
Build Phase 1 for merchants. Design the data model and join flows so the wallet can be added in Phase 2 without breaking changes.

**Pros:** Phase 1 focus maintained. No Phase 2 data migration for the join flow. Wallet architecture is considered from day one.  
**Cons:** Slightly more upfront thinking required in schema design.

## Rationale

Option C is the only choice that respects the phase sequence (merchant density first, then wallet) while avoiding the technical debt of a separate-product approach. The cost of thinking about the wallet in Phase 1 is low. The cost of not thinking about it would be a major migration in Phase 2.

## Consequences

### Positive
- Phase 2 wallet can reuse Phase 1 member records with minimal migration
- Merchant QR codes remain valid when the wallet launches
- The consent model (each member-merchant relationship is explicit) is already built

### Negative
- Phase 1 architecture decisions (schema, join flows) must be reviewed against wallet requirements before finalisation
- The wallet adds constraints to Phase 1 that a pure merchant tool would not have

### Risks
- If Phase 2 requirements significantly differ from current assumptions, some Phase 1 architectural choices may need revision. Mitigated by: keeping Phase 1 architecture simple and avoiding premature optimisation.

## Validation

This decision is validated when Phase 2 wallet development begins and the Phase 1 member data model requires no destructive migration to support consumer identity linking.
