# ADR-003 — Hybrid Revenue Model

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-02 |
| **Author** | Product Owner |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [CEO-Decisions.md](../CEO-Decisions.md#ceo-002), [03-Business/Revenue-Model.md](../03-Business/Revenue-Model.md), [03-Business/Pricing-Philosophy.md](../03-Business/Pricing-Philosophy.md) |

---

## Context

A SaaS platform for merchants has multiple potential revenue models: pure subscription, freemium, transaction-based, advertising, or a hybrid of several. The choice of revenue model affects pricing, product design, merchant relationships, and long-term defensibility.

## Decision

**OneMember operates a hybrid revenue model with four streams:**
1. Merchant subscription (primary, Phase 1)
2. Enterprise integration fees (Phase 2+)
3. Commerce transaction fees (Phase 3+)
4. Privacy-preserving aggregate analytics (Phase 4+, requires explicit merchant opt-in)

**The Customer Wallet is permanently free for consumers.**

## Options Considered

### Option A — Pure Subscription
Charge merchants a monthly fee. No transaction fees. No enterprise tier.

**Pros:** Simple. Predictable. Easy to understand.  
**Cons:** Revenue ceiling constrained by merchant count. Does not scale with merchant success. No incentive alignment between OneMember and merchant revenue.

### Option B — Transaction-Based Only
Charge a percentage of every loyalty transaction or points redemption.

**Pros:** Scales with merchant success. Zero cost to try.  
**Cons:** Unpredictable revenue. Merchants penalised for successful loyalty programmes (paying more as more transactions occur). Metering complexity.

### Option C — Hybrid (chosen)
Subscription provides stable baseline. Commerce transaction fees scale with commerce adoption. Enterprise fees reflect enterprise value. Analytics (future) monetises aggregate data responsibly.

**Pros:** Multiple independent revenue streams create resilience. Aligns OneMember's success with merchant success on commerce. Provides different pricing tiers for different market segments.  
**Cons:** More complex to explain. Requires building multiple platform modules to unlock all revenue streams.

### Option D — Advertising
Sell advertising to brands targeting OneMember's consumer base.

**Not chosen.** This model requires treating customer data as a product, which conflicts with CEO-005 (merchant data belongs to the merchant) and Core Values. Rejected permanently.

## Rationale

The hybrid model aligns OneMember's incentives with merchant success: we earn more when merchants process more commerce. It also creates resilience — if one revenue stream is impacted (e.g., a merchant segment reduces subscription spend), the other streams buffer the impact.

The decision to make the consumer wallet free is strategic: consumer adoption is what makes the merchant subscription valuable. Charging consumers would reduce adoption, which would reduce merchant value, which would increase merchant churn.

## Consequences

### Positive
- Multiple revenue streams reduce concentration risk
- Commerce transaction fees create direct incentive to make Commerce successful for merchants
- Enterprise pricing can reflect the genuine cost of enterprise support and integration

### Negative
- Full revenue model requires building Commerce and Enterprise Bridge, which are Phase 3 and Phase 2 respectively
- Analytics stream (Phase 4) requires a consent framework and privacy review before any implementation begins

### Risks
- Commerce transaction fee rate must be set correctly — too high and merchants bypass OneMember's commerce. Too low and the stream is immaterial. This requires market validation when Commerce is designed.

## Validation

Revenue model is working when all three primary streams (subscription, enterprise, commerce) are contributing meaningful revenue within 24 months of Commerce launch.
