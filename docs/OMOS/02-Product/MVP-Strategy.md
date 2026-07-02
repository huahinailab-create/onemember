# MVP Strategy

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Product-Bible.md](./Product-Bible.md), [Parking-Lot.md](./Parking-Lot.md), [09-Roadmap/Long-term-Roadmap.md](../09-Roadmap/Long-term-Roadmap.md), [00-Executive/Decision-Framework.md](../00-Executive/Decision-Framework.md) |

---

## Purpose

This document defines how OneMember thinks about minimum viable product decisions — how to determine what is essential for a feature to launch, and what can be deferred without compromising the core value proposition.

---

## MVP Philosophy

OneMember's MVP philosophy is: **make one thing work perfectly before adding the second thing**.

The alternative — launching ten features that each work partially — creates:
- Confused merchants who do not know how to use the platform
- Support burden from half-finished flows
- Technical debt from parallel incomplete implementations
- A reputation for being unreliable

The Phase 1 MVP is the loyalty programme. It does not need commerce, POS, or inventory. It needs to:
1. Let a merchant set up a loyalty programme in an afternoon
2. Let a customer join in under 30 seconds
3. Award points or stamps reliably on every qualifying transaction
4. Notify the merchant and customer when a reward is earned

Everything else is Phase 2 or later.

---

## The MVP Test

Before a feature goes into the backlog, answer:

**"Would a merchant with 500 members be significantly worse off without this feature?"**

If yes → consider for current phase  
If no → Parking Lot  
If depends → define the condition under which it becomes yes

---

## Phase 1 MVP Definition (Current)

These features are the minimum for Phase 1 to be viable. They are either already live or in progress:

| Feature | Status | Priority |
|---|---|---|
| Merchant registration and onboarding | ✅ Live | Critical |
| Email verification | ✅ Live | Critical |
| Points loyalty programme | ✅ Live | Critical |
| Stamp card programme | ✅ Live | Critical |
| Member QR join flow | ✅ Live | Critical |
| Member management | ✅ Live | Critical |
| Campaign management | ✅ Live | Critical |
| Reward configuration | ✅ Live | Critical |
| Dashboard with basic metrics | ✅ Live | Critical |
| Merchant Intelligence (AI insights) | ✅ Live | High |
| Settings and branding | ✅ Live | High |
| Subscription billing | ✅ Live | Critical |
| Birthday bonus | 🔄 Planned | High |
| Point expiry | 🔄 Planned | High |
| Win-back campaigns | 🔄 Planned | High |
| Member notifications (SMS/email) | 🔄 Planned | High |

---

## What Is NOT in Phase 1 MVP

These have been explicitly deferred:

| Feature | Reason for Deferral |
|---|---|
| Customer Wallet app | Phase 2 — requires consumer identity architecture |
| Commerce module | Phase 3 — requires product/inventory architecture |
| POS Lite | Phase 3 — requires POS architecture |
| Multi-location management | Phase 3 — requires branching architecture |
| Enterprise Bridge API | Phase 2 — requires enterprise contracts and security review |
| Native mobile app | Phase 2+ — web-first until wallet launches |
| Tier-based loyalty (Bronze/Silver/Gold) | Phase 2 — requires member history and tier calculation engine |

---

## The Scope Conversation

When the Product Owner or a stakeholder requests a feature that is out of scope for the current phase, the response is:

1. Acknowledge the request
2. Confirm it is in the Parking Lot (or add it if new)
3. Explain which phase it is targeted for and why
4. Return to the current phase priorities

This is not a "no forever." It is a "yes, in the right sequence."

No scope expansion happens without:
- A product decision recorded in `docs/08-Product-Decisions.md`
- An updated sprint specification
- Product Owner approval
