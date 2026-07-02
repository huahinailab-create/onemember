# Product-Memory.md — Permanent Product Knowledge

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [00-Executive/Vision.md](./00-Executive/Vision.md), [00-Executive/Mission.md](./00-Executive/Mission.md), [03-Business/Problem-We-Solve.md](./03-Business/Problem-We-Solve.md), [CEO-Decisions.md](./CEO-Decisions.md), [09-Roadmap/Long-term-Roadmap.md](./09-Roadmap/Long-term-Roadmap.md) |

---

## Purpose

Product-Memory captures the permanent, settled knowledge about OneMember that should influence every future decision — from product features to architecture choices to marketing language.

This is not the roadmap (that describes what we will build). This is the foundation — the beliefs, principles, and insights that make OneMember what it is and ensure it never drifts from its core purpose.

**Every person and AI working on OneMember should read this before making decisions.**

---

## Why OneMember Exists

Thailand has millions of small merchants — cafes, restaurants, beauty salons, retail shops, street food vendors — who operate without modern business infrastructure. They know their customers by face, not by name. They have no way to reach a customer who stopped coming. They cannot afford the enterprise loyalty platforms that large chains use.

At the same time, Thai consumers are members of 8–12 paper-based loyalty programmes, most of which they forget within weeks. The experience of being a loyal customer is fragmented, inconvenient, and ultimately unrewarding.

**OneMember exists to close this gap.** We give small merchants the same tools that large chains use — at a price that respects their reality — and we give customers a single, simple identity that works everywhere.

---

## Merchant Pain Points

These are the four problems we solve for merchants. Every feature decision should map to at least one of them.

**1. Invisible customers.** Merchants serve hundreds of people but know almost none of them by name, visit frequency, or spending behaviour. Every marketing effort is a guess.

**2. No affordable loyalty infrastructure.** Enterprise platforms cost 50,000–200,000 THB/month. Small merchants spend less on their entire marketing budget. DIY alternatives (paper cards, LINE OA) generate no useful data.

**3. No way to reach churning customers.** When a regular stops coming, the merchant has no way to know it happened or to reach them. By the time they notice, the relationship is over.

**4. Fragmented data.** Customers exist in paper records, LINE followers, Instagram followers, and delivery platform databases — none of which connect, none of which the merchant owns.

---

## Customer Pain Points

**1. Too many cards, too little value.** The average Thai consumer carries 5–8 paper stamp cards for merchants. Most are lost, damaged, or forgotten. Most programmes are abandoned before the first reward is earned.

**2. Signing up is too hard.** Most loyalty programmes require more information than a customer is willing to provide while standing at a counter. Long forms = low adoption.

**3. No central view.** A customer who is a member of 8 programmes has no way to see their combined balance, find their nearest reward, or know which merchant they should visit next.

---

## Merchant-First Philosophy

OneMember is a merchant tool first. The customer experience must be excellent, but merchant value is the foundation of the business model.

The correct sequence is:
1. Merchants join because the tool makes their business better
2. Customers join because the experience is easy and the rewards are real
3. The network grows because both sides benefit

We do not sacrifice merchant capability for customer convenience, or vice versa. When they conflict, we look for the solution that serves both. If we cannot find one, merchants come first.

**Why:** Merchants pay the subscription. Merchants choose to use OneMember. Without merchant adoption, there is no customer network.

---

## Customer Privacy

Customer data collected through OneMember belongs to the merchant, but it is held in trust for the customer.

- Customers consent to their data being collected when they join a programme
- Customers can see what data is held about them (Phase 2 wallet)
- Merchants cannot sell customer data to third parties
- OneMember does not use customer data to build advertising profiles
- PDPA compliance is non-negotiable (Thailand Personal Data Protection Act)

**Why:** Trust is the foundation of the customer relationship. If customers do not trust that their data is safe, they will not join loyalty programmes. If they do not join, there is no network. The privacy model is not just an ethical requirement — it is a competitive advantage.

---

## The One Scan Concept

The single most important customer experience principle in OneMember is **One Scan**.

A customer walks into a merchant. They scan the QR code once. They are a member. They earn points. Done.

No app to download. No form to fill. No email verification at the counter. No waiting. The join flow must complete in under 30 seconds.

**This is the North Star Metric.** (See `00-Executive/North-Star-Metric.md`)

Every friction point we add to the join flow costs us members. Every second we add costs us merchant adoption. The simpler the join, the faster the network grows.

---

## Customer Wallet Vision

The Customer Wallet is the Phase 2 product that transforms OneMember from a merchant SaaS tool into a two-sided marketplace.

**The vision:** A consumer opens the OneMember app. They can see all their loyalty memberships across every OneMember merchant. They know exactly how many points they have, which rewards they can redeem, and which merchant they are closest to earning a free item from.

**The strategic importance:**
- The wallet creates network effects: more merchants = more wallet value = more consumer adoption = more merchant adoption
- The wallet creates switching costs for merchants: leaving OneMember means losing access to wallet customers
- The wallet enables a flywheel: consumers gravitate toward OneMember merchants because their loyalty is tracked; merchants see higher repeat visits

The wallet will be **free for customers permanently** (CEO-003). Monetisation comes from merchants, not consumers.

---

## Enterprise Bridge Vision

Enterprise Bridge is the API layer that allows large merchant chains (hotels, retail chains, franchise networks) to plug their existing CRM or POS into the OneMember network.

**The vision:** A hotel chain with 50 locations can plug into OneMember and offer their guests wallet-compatible loyalty without rebuilding their loyalty stack. Guests accumulate points that work both in the hotel chain's native programme and in the OneMember wallet.

**Why it matters:**
- Enterprise chains bring thousands of members into the network immediately
- Enterprise API fees create a high-margin revenue stream
- Enterprise adoption validates the platform for SMEs who want to compete with chains

Enterprise Bridge is planned for Phase 2, launched together with the Customer Wallet so enterprise merchants have a distribution channel immediately.

---

## Commerce Vision

Commerce is the Phase 3 product that adds ordering, delivery, and payment to the loyalty layer.

**The vision:** A customer opens OneMember, finds a merchant, browses their menu, orders, and pays — earning loyalty points automatically on every transaction.

**The strategic importance:**
- Commerce drives loyalty transactions at much higher frequency than in-person visits
- Every commerce transaction creates a loyalty event — the flywheel accelerates
- Loyalty + commerce creates deep merchant lock-in (switching means losing the commerce channel)

Commerce uses PromptPay for Thailand payments. It is not a marketplace — merchants are not competing against each other within the product.

---

## POS Vision

POS Lite is the Phase 3 product for merchants who do not have a cash register or POS system.

**The vision:** A merchant opens OneMember on a tablet. A customer orders. The merchant records the sale, points are awarded, and inventory is decremented — all in one action.

**The strategic importance:**
- Most Thai SMEs do not have a proper POS
- A POS that is also a loyalty manager removes the need for receipt OCR (Parking Lot PL-006) and manual point recording
- POS data creates merchant intelligence that drives retention

POS Lite is the entry point for Procurement (Phase 4).

---

## Thailand-First Strategy

OneMember is a Thai product built for Thai merchants and Thai customers first. (ADR-006)

**What this means:**
- Thai language localisation is a first-class requirement
- Thai payment methods (PromptPay) are the primary commerce payment method
- PDPA (Thailand PDPA) is the compliance baseline
- The product is designed for Thai merchant behaviour (small shops, high mobile use, LINE as communication)
- All business metrics and exit criteria are measured against the Thai market first

**What it does NOT mean:**
- The architecture must not limit international expansion
- Data models must support multi-currency and multi-language from day one
- When a Thailand-specific assumption is made, it must be documented

Thailand is the market. Southeast Asia is the vision. Regional expansion begins in Phase 4.

---

## Future Regional Expansion

OneMember's long-term ambition is to be the merchant growth platform for Southeast Asia.

The regional expansion strategy (Phase 4, ADR-006):
- **Malaysia** — Malay localisation, DuitNow payments, Malaysia PDPA
- **Vietnam** — Vietnamese localisation, VNPay, local accounting requirements
- **Singapore** — English-first, PayNow

Regional expansion is possible because:
- The platform architecture supports multi-language from day one
- The data model supports multi-currency
- The business model (subscription + commerce fees) applies in all markets
- The flywheel (merchants → wallet → commerce) is market-agnostic

Regional expansion is NOT:
- A reason to build multi-language UI before Phase 1 is profitable
- A reason to delay Thailand-specific features in favour of global features
- A guarantee — expansion depends on Phase 1 and Phase 2 exit criteria being met

---

## Principles That Should Influence Every Decision

These principles have been set by the Product Owner and are non-negotiable (CEO-Decisions.md):

1. **OneMember is a Merchant Growth Platform, not a loyalty app.** (CEO-001) Do not describe it as a loyalty app. Do not build only loyalty features. The roadmap extends far beyond loyalty.

2. **The Customer Wallet is free for customers, forever.** (CEO-003) We do not monetise customers. We monetise merchants. Never propose a consumer subscription model.

3. **Merchant data belongs to the merchant.** (CEO-005) We hold merchant data in trust. We do not use it to build advertising profiles, sell to third parties, or create a data marketplace.

4. **Security is non-negotiable.** (CEO-006) No feature justifies weakening security. Email verification stays on. DevTools stay gated. Secrets stay in `.env`. No exceptions.

5. **Never deploy without Product Owner approval.** (CEO-007) Claude Developer cannot deploy. The AI CTO cannot deploy. Only the Product Owner can authorise a production deployment.

---

## What OneMember Is Not

These are items the Product Owner has explicitly decided OneMember will never be:

- A social platform (no reviews, no follower counts, no social feeds)
- An advertising marketplace (we do not sell customer attention)
- A financial services provider (no loans, no credit, no insurance)
- A cryptocurrency platform
- A general-purpose CRM (we are vertical — loyalty and merchant operations only)
- A multi-vendor marketplace (we are not competing with our merchants)
