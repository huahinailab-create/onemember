# ADR-002 — OneMember Is a Merchant Growth Platform, Not a Loyalty App

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-02 |
| **Author** | Product Owner |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [CEO-Decisions.md](../CEO-Decisions.md#ceo-001), [00-Executive/Vision.md](../00-Executive/Vision.md), [02-Product/Product-Bible.md](../02-Product/Product-Bible.md) |

---

## Context

A loyalty app and a merchant growth platform are both product categories that OneMember could occupy. They share features (points, stamps, member management) but they differ fundamentally in scope, defensibility, and long-term value.

A loyalty app positions OneMember against other loyalty apps and is evaluated on loyalty features alone. A merchant growth platform positions OneMember as infrastructure for the entire merchant-customer relationship — with loyalty as one of many layers.

This decision defines OneMember's identity and shapes every product decision that follows.

## Decision

**OneMember is a Merchant Growth Platform.** Loyalty is the foundation and the Phase 1 focus, but the platform includes Commerce, POS, Inventory, Procurement, Accounting, Analytics, and AI. Every sprint and every feature is evaluated against the platform thesis, not just the loyalty features.

## Options Considered

### Option A — Pure Loyalty App
Build the best loyalty app in Southeast Asia. Compete on loyalty features, campaigns, and analytics. Stay focused on one problem.

**Pros:** Faster to market. Simpler product. Clear competitive positioning.  
**Cons:** Low defensibility — loyalty apps are easily copied. Limited revenue ceiling. Does not create the network effects required for long-term value.

### Option B — Merchant Growth Platform (chosen)
Build loyalty first, then expand to cover the full merchant-customer relationship lifecycle through multiple integrated modules.

**Pros:** Network effects across modules. Higher revenue ceiling. Stronger merchant lock-in (through value, not contracts). Unique positioning.  
**Cons:** Longer to full value. More complex architecture. Requires discipline to avoid scope creep in Phase 1.

### Option C — Horizontal SaaS (CRM / Marketing Automation)
Build a general-purpose merchant CRM with loyalty as one feature.

**Pros:** Larger total addressable market.  
**Cons:** Competes with established players (HubSpot, Klaviyo) who have far more resources. Does not leverage OneMember's specific expertise in loyalty and the merchant-customer relationship.

## Rationale

The platform thesis creates network effects that a loyalty app cannot. When Commerce connects to loyalty, merchants can measure the ROI of loyalty directly. When POS connects to loyalty, every in-store transaction automatically awards points. When the customer wallet aggregates all merchants, a new merchant joining the network gets access to consumers who are already wallet users. These compounding advantages are only available on a platform.

Option A (loyalty app) is a good business. Option B (merchant growth platform) is a defensible long-term business. The additional complexity of Option B is justified by the additional defensibility.

## Consequences

### Positive
- Product roadmap has a clear multi-phase structure that makes sense to investors, merchants, and team members
- Every feature can be evaluated against the platform thesis
- Network effects create compounding value over time

### Negative
- Phase 1 must be disciplined about not building Phase 2 or Phase 3 features prematurely
- The platform positioning requires more explanation than "we are a loyalty app"
- Architecture must accommodate multiple modules from the beginning

### Risks
- If OneMember never successfully builds beyond Phase 1 loyalty, the platform positioning becomes a promise we could not keep. Mitigated by: making Phase 1 commercially successful before investing in Phase 2.

## Validation

This decision is validated when: (a) at least two platform modules beyond loyalty are live and generating value, and (b) merchants cite the platform integration as a reason for staying on OneMember rather than switching to a pure loyalty app.
