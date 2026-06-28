# 13 — Product Vision

> **Last updated:** 2026-06-28  
> **Owner:** Product Owner (Huahin)  
> **Status:** Living document — update as the product evolves

---

## Mission

> *Empower independent merchants to build lasting customer loyalty — without the complexity or cost of enterprise software.*

---

## Vision

OneMember will become the go-to loyalty platform for independent and small-chain merchants across Southeast Asia and beyond. Every merchant, regardless of technical sophistication, will be able to launch a professional loyalty programme in minutes, retain more customers, and grow repeat business — all from a single, affordable SaaS product.

---

## The Problem We Solve

Independent merchants face a loyalty gap:

| Pain Point | Reality Today |
|-----------|--------------|
| Enterprise loyalty platforms (Salesforce, Oracle) | Too expensive, too complex — built for large retail chains |
| Paper stamp cards | No data, easy to forge, easily lost |
| Generic app platforms (Shopify plugins) | Only for e-commerce; don't serve cafés, salons, gyms, and local retail |
| Building custom solutions | Requires technical staff and ongoing maintenance |

OneMember fills the gap: **self-service loyalty software designed for the merchant sitting behind a counter**, not for an enterprise IT team.

---

## Target Market

### Primary: Independent Merchants

| Segment | Examples |
|---------|---------|
| Food & Beverage | Cafés, bubble tea shops, restaurants, bakeries |
| Retail | Boutiques, bookshops, hardware stores, pharmacies |
| Health & Wellness | Gyms, yoga studios, spas, salons, barbershops |
| Services | Dry cleaners, car washes, tutoring centres |

**Merchant profile:**
- 1–5 locations
- 1–10 staff
- Non-technical owner-operator
- Currently using paper cards or no loyalty programme
- Wants to recognise regulars and increase visit frequency

### Secondary: Small Chains

- 5–20 locations
- Regional brand identity
- Needs centralised loyalty management

---

## Product Positioning

| Dimension | OneMember | Paper Stamp Cards | Enterprise Platform |
|-----------|----------|------------------|-------------------|
| Setup time | Minutes | Immediate | Weeks–months |
| Cost | Low monthly SaaS | Near-zero | High enterprise licensing |
| Data & insights | Yes | No | Yes |
| Member management | Yes | No | Yes |
| Technical expertise required | None | None | High |
| Customisation | Moderate | None | Extensive |
| Multi-location | V1.x roadmap | Yes (manual) | Yes |

**Positioning statement:**  
OneMember is the loyalty platform for merchants who want the power of enterprise loyalty without the enterprise price or complexity.

---

## Core Value Propositions

### 1. Launch in Minutes
The 6-step onboarding wizard (Sprint 4.2) lets a new merchant go from sign-up to a running loyalty campaign — including a starter campaign with rewards — in under 10 minutes. No developer required.

### 2. Two Proven Loyalty Models
- **Points** — customers earn points per spend; redeem for rewards at any threshold.
- **Stamps** — customers collect stamps per visit; complete a card to claim a reward.

Both are familiar to customers and proven to drive repeat visits.

### 3. Full Member Visibility
Every member has a profile with their full activity history, current balance, and stamp progress. The merchant always knows who their best customers are (see: Top Members on the Dashboard).

### 4. Merchant-First Design
Every decision in the product uses merchant-native terminology: **Campaigns** (not programs), **Members** (not users), **Rewards** (not redemptions), **Activity** (not transactions) — DECISION-028, DECISION-034. The UI is designed for a merchant who checks it between serving customers, not an analyst running reports.

### 5. Affordable SaaS
Subscription-based pricing with a 30-day free trial. No per-transaction fees. No setup costs.

---

## Product Principles

These principles guide every product decision:

| Principle | Meaning |
|-----------|---------|
| **Merchant-first** | The merchant is the primary user. Every screen, label, and flow is designed for them — not for a developer or analyst. |
| **Simple over complete** | A feature that works simply beats a feature that handles every edge case. KISS always. |
| **Real data only** | Never show empty charts or placeholder analytics. Show actionable data or show a helpful empty state. |
| **Distraction-free onboarding** | Wizard layout removes sidebar and chrome so the merchant focuses on setup — not navigation. |
| **Sensible defaults** | Starter campaigns, default currency, default timezone — reduce the number of choices the merchant must make on day one. |
| **Trust through transparency** | Transaction history is immutable. Members can see their balance. Merchants can see every event. |

---

## Brand Voice

| Attribute | Description |
|-----------|-------------|
| **Confident** | We know loyalty. Our defaults are good. Our wizard is smooth. |
| **Friendly** | We talk to merchants like a business partner, not a vendor. |
| **Practical** | No jargon. No filler. Every sentence in the UI earns its place. |
| **Encouraging** | "You're ready!" — celebrate milestones. Positive language throughout onboarding. |

---

## Success Metrics — Version 1.0

| Metric | Target | Notes |
|--------|--------|-------|
| Onboarding completion rate | > 70% | % of signups who complete wizard |
| Time to first member | < 30 min after signup | Speed measure of value delivery |
| 30-day trial → paid conversion | > 20% | Commercial health |
| Monthly Active Merchants | Growth curve | Core engagement metric |
| Churn rate (monthly) | < 5% | Retention signal |
| Average members per merchant | > 50 by month 3 | Merchant adoption signal |

---

## What OneMember Is NOT

To stay focused, OneMember explicitly does not aim to be:

- An **e-commerce platform** (no product catalogue, no shopping cart)
- A **POS system** (merchants use their existing POS; OneMember records loyalty events)
- A **CRM** (member notes are supported, but not a full customer relationship tool)
- A **marketing automation platform** (email campaigns, SMS blasts — Version 2.0 territory)
- A **mobile app** for customers (the merchant portal is web-based; customer-facing mobile is a future product)
