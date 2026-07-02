# ADR-006 — Thailand-First Market Entry Strategy

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-02 |
| **Author** | Product Owner |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [CEO-Decisions.md](../CEO-Decisions.md#ceo-004), [Known-Constraints.md](../Known-Constraints.md), [09-Roadmap/Long-term-Roadmap.md](../09-Roadmap/Long-term-Roadmap.md), [Assumptions.md](../Assumptions.md) |

---

## Context

OneMember is building a regional platform for Southeast Asia. The question is: do we launch in multiple markets simultaneously, or focus on one market first?

This decision affects localisation investment, regulatory compliance, payment integration, and team focus.

## Decision

**OneMember launches in Thailand first.** Regional expansion (Malaysia, Vietnam, Singapore) is planned for Phase 4, after Phase 1 is stable with 1,000+ active paying merchants and Phase 2 (Customer Wallet) is live.

**Architecture must support regional expansion from day one**, even though only Thailand is served initially:
- All user-visible strings are abstracted via `__()` translation helpers (not hardcoded)
- Currency is merchant-configurable (not hardcoded as THB)
- Timezone is merchant-configurable (not hardcoded as Asia/Bangkok)
- Date formats are merchant-configurable
- Payment methods are modular (PromptPay is a plugin, not hardcoded)

## Options Considered

### Option A — Single Market First (chosen)
Launch in Thailand. Build the product for Thailand. Validate the model. Expand when stable.

**Pros:** Focused team. Faster to product-market fit. Regulatory complexity limited to one jurisdiction. Deep market understanding before expanding.  
**Cons:** Slower to regional scale. Competitors may enter other markets first.

### Option B — Multi-Market Launch
Launch in Thailand, Malaysia, and Vietnam simultaneously.

**Pros:** Faster regional presence. First-mover advantage in multiple markets.  
**Cons:** Triples localisation effort. Requires PDPA (Thailand), Malaysia PDPA, and Vietnamese data protection compliance simultaneously. Payment integration for three markets simultaneously. Product-market fit validation is harder when results are mixed across markets.

### Option C — English-First Global
Build an English-language product for all markets simultaneously.

**Pros:** Single codebase. No localisation investment required initially.  
**Cons:** Southeast Asian SMEs primarily operate in their local language. An English-only platform in Thailand or Vietnam has limited addressable market among the small business segment.

## Rationale

Finding product-market fit in one market before expanding is a fundamental startup principle. OneMember's market is hyperlocal — a cafe in Bangkok has no relevance to a cafe in Kuala Lumpur, even if the product features are the same. Merchant acquisition requires local sales and marketing in each market. Splitting focus across three markets simultaneously would produce poor results in all three.

Thailand is the right starting market because: (a) the founding team has Thailand market knowledge, (b) PromptPay adoption makes the commerce module viable, (c) Thailand PDPA provides a compliance baseline that is broadly applicable to other ASEAN markets.

## Consequences

### Positive
- Complete focus on Thailand product-market fit in Phase 1
- Architecture decisions (currency abstraction, language files, timezone configuration) are done once and correctly, benefiting all future markets

### Negative
- Competitors may enter Malaysia or Vietnam before OneMember
- Thailand market size limits Phase 1 revenue ceiling

### Risks
- If Thailand market proves too small or too price-sensitive to support the business model, regional expansion timeline must be accelerated. Mitigated by: monitoring merchant acquisition rate and conversion metrics monthly from launch.

## Validation

This decision is correct when OneMember reaches 1,000 active paying merchants in Thailand before any regional expansion sprint begins. If the 1,000-merchant threshold is not reached within 18 months of Phase 1 launch, the strategy must be reviewed.
