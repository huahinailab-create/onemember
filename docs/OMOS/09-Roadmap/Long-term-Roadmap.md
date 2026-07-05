# Long-Term Roadmap

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Roadmap.md](./Roadmap.md), [02-Product/Product-Bible.md](../02-Product/Product-Bible.md), [00-Executive/Vision.md](../00-Executive/Vision.md), [03-Business/Market-Opportunity.md](../03-Business/Market-Opportunity.md) |

---

> **2026-07-05:** The detailed year-by-year strategy through 2030, including AI, integrations, enterprise, mobile, revenue, and expansion plans, is in [Version-2.0-Vision-and-Master-Roadmap-2026-2030.md](./Version-2.0-Vision-and-Master-Roadmap-2026-2030.md). This document remains the phase narrative; the Master Roadmap operationalises it.

## Purpose

This document describes OneMember's long-term platform evolution — the phases, the modules, and the strategic logic connecting them. It is the narrative version of the product roadmap.

For the operational roadmap with sprint history and near-term priorities, see `Roadmap.md`.

---

## Phase 1 — Merchant Foundation (Current)

**Goal:** Establish OneMember as the leading loyalty platform for SMEs in Thailand.

**The bet:** If we make it easy enough for any merchant to run a professional loyalty programme, and fast enough for any customer to join, the network will form organically.

**Core deliverables:**
- Loyalty programme engine (Points + Stamps)
- Member management and import
- Campaign management and analytics
- Merchant Intelligence (AI health score)
- Subscription billing
- Merchant onboarding

**Exit criteria for Phase 1:**
- 1,000+ active paying merchants
- 50,000+ active members across the network
- Monthly join rate growing > 10% MoM
- Merchant 12-month retention > 60%

**Platform modules live at end of Phase 1:**
- ✅ Merchant SaaS
- 🔄 Analytics & AI (partial — Merchant Intelligence live)

---

## Phase 2 — Customer Wallet

**Goal:** Launch a consumer-facing wallet that unifies loyalty across all OneMember merchants, creating network effects and making the platform a two-sided marketplace.

**The bet:** When customers can manage all their loyalty in one app, they will naturally gravitate toward OneMember-powered merchants over non-OneMember merchants. This increases the value of being an OneMember merchant, which drives merchant acquisition.

**Core deliverables:**
- Consumer account creation (separate from merchant accounts)
- Universal QR scan → wallet join flow
- Cross-merchant loyalty dashboard
- Privacy and consent management per merchant
- Push notifications (points earned, rewards available, birthday)
- Enterprise Bridge API (see below — launched together with Wallet to enable enterprise adoption)

**Who drives Phase 2:**
- Consumers who are tired of fragmented loyalty programmes
- Enterprise merchants who want wallet distribution without building their own app
- OneMember merchants who want to reach customers already in the wallet

**Platform modules live at end of Phase 2:**
- ✅ Merchant SaaS
- ✅ Customer Wallet
- ✅ Enterprise Bridge (v1)
- 🔄 Analytics & AI (expanded with cross-merchant insights)

---

## Phase 3 — Regional Commerce Network

**Goal:** Add commerce, POS, and inventory modules that make OneMember the operating platform for merchant-customer relationships — not just the loyalty layer.

**The bet:** When a customer can browse, order, and pay through OneMember — and earn loyalty points on every transaction — the platform becomes indispensable for both sides. Loyalty drives commerce. Commerce drives loyalty. The two flywheels become one.

**Core deliverables:**
- Commerce: product/menu listing, ordering, delivery management
- POS Lite: staff-facing sale recording for merchants without a POS
- Inventory: basic stock management integrated with Commerce and POS
- PromptPay integration (Thailand) for commerce checkout
- Loyalty-on-commerce: every Commerce order earns loyalty points automatically
- Multi-location management for growing merchants

**Platform modules live at end of Phase 3:**
- ✅ Merchant SaaS
- ✅ Customer Wallet
- ✅ Enterprise Bridge (v2 — enhanced)
- ✅ Commerce
- ✅ POS Lite
- ✅ Inventory
- 🔄 Analytics & AI (commerce + loyalty cross-analysis)

---

## Phase 4 — Regional Operating System

**Goal:** Expand OneMember beyond Thailand to become the merchant growth platform for Southeast Asia, adding accounting, procurement, and full regional payment support.

**The bet:** The platform network effects, data assets, and merchant trust built in Thailand translate to other markets with local payment and language adaptation. OneMember becomes a regional infrastructure provider.

**Core deliverables:**
- Thailand Accounting: VAT-compliant bookkeeping integrated with Commerce and POS
- Procurement: streamlined supplier ordering for inventory-heavy merchants
- Malaysia localisation: Malay language, DuitNow, Malaysia PDPA
- Vietnam localisation: Vietnamese language, VNPay, local accounting
- Singapore localisation: English-first, PayNow
- Native iOS + Android apps (wallet and merchant apps)
- Advanced AI: demand forecasting, churn prediction, pricing recommendations

**Platform modules live at end of Phase 4:**
- ✅ All Phase 3 modules
- ✅ Procurement
- ✅ Thailand Accounting
- ✅ Regional: Malaysia, Vietnam, Singapore
- ✅ Analytics & AI (full platform intelligence)

---

## The Flywheel

The long-term OneMember flywheel connects all four phases:

```
More merchants join
        ↓
More customers scan QR → join wallet
        ↓
Wallet becomes more valuable to consumers
        ↓
Consumers choose OneMember merchants
        ↓
Merchants see higher repeat visit rates
        ↓
Merchants recommend OneMember to other merchants
        ↓
More merchants join
```

Each phase adds a new force to this flywheel:
- Phase 1: starts the flywheel (merchant + member sign-ups)
- Phase 2: accelerates it (wallet makes consumer side self-reinforcing)
- Phase 3: deepens it (commerce creates economic lock-in for both sides)
- Phase 4: expands it (regional network multiplies the effects)

---

## What Is Explicitly Not on the Roadmap

These items have been considered and explicitly excluded:

| Item | Why Excluded |
|---|---|
| Social features (reviews, followers) | Not a social platform; distraction from loyalty-commerce thesis |
| Advertising marketplace | Would require selling customer attention, violating trust model |
| Financial services (credit, loans) | Outside core competency; high regulatory risk |
| Cryptocurrency payments | Not relevant to target market; adds complexity for no benefit |
| General-purpose CRM (Salesforce-style) | Scope creep; OneMember is vertical, not horizontal |
| Multi-vendor marketplace (Shopee-style) | Different business model; would compete with our merchants |
