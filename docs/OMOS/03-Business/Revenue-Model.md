# Revenue Model

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Pricing-Philosophy.md](./Pricing-Philosophy.md), [Market-Opportunity.md](./Market-Opportunity.md), [02-Product/Product-Bible.md](../02-Product/Product-Bible.md), [00-Executive/Vision.md](../00-Executive/Vision.md) |

---

## Purpose

This document defines OneMember's revenue model — how the business generates revenue, from whom, and on what basis. This is a CEO-approved decision.

---

## Revenue Model: Hybrid

OneMember operates a hybrid revenue model combining four revenue streams that collectively scale with platform growth.

---

## Stream 1 — Merchant Subscription (Primary Revenue, Phase 1)

**What it is:** Recurring monthly subscription paid by merchants to access OneMember's platform.

**Who pays:** Every merchant using OneMember's Merchant SaaS.

**Pricing basis:** Subscription tier (plan-based), with limits on members, campaigns, and features per tier.

**Current tiers (subject to change):**

| Plan | Target Merchant | Monthly Price |
|---|---|---|
| Starter | Solo merchant, < 500 members | ~500 THB |
| Growth | Growing SME, < 2,000 members | ~1,500 THB |
| Professional | Multi-location, < 10,000 members | ~3,000 THB |
| Enterprise | Custom | Negotiated |

**Billing:** Via Stripe. Monthly or annual (annual discount). Trial period available for new merchants.

**Revenue characteristic:** Predictable, recurring. Grows with merchant count. Does not require transaction volume.

---

## Stream 2 — Enterprise Integration Fees (Phase 2+)

**What it is:** One-time setup fees and recurring API access fees for Enterprise clients who integrate their existing systems with OneMember's Enterprise Bridge.

**Who pays:** Large merchants (20+ locations) or corporations integrating loyalty with existing POS, CRM, or ERP systems.

**Pricing basis:** Custom negotiation. Includes: integration project fee, monthly API access fee, and optionally a revenue share on loyalty-attributed transactions.

**Revenue characteristic:** Lumpy (project-based setup fees) + recurring (API access). High revenue per client. Low volume.

---

## Stream 3 — Commerce Transaction Fees (Phase 3+)

**What it is:** A percentage fee on transactions processed through OneMember Commerce (merchant product sales and order processing).

**Who pays:** Merchants who use the Commerce module and customers who place orders.

**Pricing basis:** Percentage of gross merchandise value (GMV) processed. Rate to be defined in Commerce sprint specification.

**Revenue characteristic:** Variable, scales with commerce adoption. Zero until Commerce launches.

**Principle:** OneMember's transaction fee must always be lower than what the merchant would pay using a third-party commerce platform (Shopee, Grab, Foodpanda). The value proposition is: sell directly through OneMember and keep more margin, while your customers earn loyalty points.

---

## Stream 4 — Analytics and Data Products (Phase 4+)

**What it is:** Aggregated, anonymised market insights sold to brands, suppliers, or market research firms. This is NOT individual customer data — it is privacy-preserving aggregate trend data (e.g., "spending at Thai cafes in Bangkok increased 15% in Q2").

**Who pays:** Consumer brands, F&B industry bodies, market research firms.

**Revenue characteristic:** Low priority, requires significant data volume and legal/privacy framework before launching.

**Hard constraints:**
- Never sold without explicit merchant opt-in
- Never contains individual customer data
- Never sold to competitors of OneMember's merchants
- Requires a dedicated privacy review and RFC before development begins

---

## What Is Free

**The Customer Wallet (Phase 2) is free to consumers.** There is no freemium tier that upsells customers. The consumer app generates no direct revenue. It generates indirect revenue by making the merchant subscription more valuable (a merchant whose customers actively use the wallet is more likely to retain their subscription).

**Referral programme (future):** Merchants who refer other merchants may receive credits or discounts. This is a cost of acquisition, not a revenue stream.

---

## Revenue Model Principles

1. **Align with merchant success.** When merchants do well, they stay subscribed and upgrade plans. OneMember's success is structurally linked to merchant success.

2. **Do not monetise customer data.** Individual customer data belongs to the merchant who collected it. It is never sold. Aggregate analytics (Stream 4) require explicit merchant consent and privacy review.

3. **Keep the customer experience free.** Charging consumers to participate in a merchant's loyalty programme creates friction and damages the programme's effectiveness. The consumer experience must always be free.

4. **Commerce fees must deliver value.** OneMember only earns a transaction fee when a commerce transaction occurs. This means OneMember is incentivised to make commerce successful for merchants — not just to sign them up.

5. **Enterprise fees reflect enterprise value.** Enterprise clients require more support, more integration work, and more compliance. Enterprise pricing must reflect this reality.
