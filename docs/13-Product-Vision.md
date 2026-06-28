# 13 — Product Vision

> **Last updated:** 2026-06-28 (Planning Sprint P2)  
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

---

## Brand Messaging

### Primary Tagline

> **Reward loyalty. Grow your business.**

### Core Promise

> Keep customers coming back.

### Primary Value Proposition

OneMember is the easiest and most affordable marketing tool for retaining customers and increasing repeat business.

### Expanded Value Proposition

Most businesses spend significant money attracting new customers but very little keeping the ones they already have.

OneMember helps merchants build stronger customer relationships through simple loyalty programs that encourage repeat visits, increase customer lifetime value, and reduce the need for expensive marketing.

---

## Elevator Pitch

### For Investors

> OneMember is an affordable, self-service loyalty SaaS for independent merchants — the cafés, salons, and local retailers that enterprise platforms ignore. Merchants launch a loyalty programme in minutes, retain more customers, and get measurable insight into repeat business. We charge a simple monthly subscription with no per-transaction fees. Our target is the millions of independent merchants across Southeast Asia who are still running paper stamp cards.

### For Merchants

> OneMember lets you reward your regulars and keep them coming back — without complicated software or expensive consultants. Set up your loyalty programme in under 10 minutes, add members at the counter, and start rewarding repeat visits the same day. It costs less than a cup of coffee a day and replaces the paper cards your customers keep losing.

### For Partners

> OneMember is a white-label-ready loyalty platform built for independent and small-chain merchants. It handles the full loyalty lifecycle — member enrolment, points and stamps, rewards, and redemption — so your merchants get a professional loyalty programme without your team building one from scratch. We offer clean APIs and a partner programme for resellers and POS integrators.

---

## Brand Positioning

### OneMember IS

- A **customer retention platform** — built to increase repeat visits and reduce churn.
- A **loyalty platform** — supporting points-based and stamp-based programmes out of the box.
- A **repeat-business engine** — every feature exists to bring customers back more often.
- An **affordable SaaS for SMEs** — priced for independent merchants, not enterprise budgets.
- **Easy to use** — any merchant can operate it without training or technical staff.

### OneMember IS NOT

- A **POS system** — merchants use their existing POS; OneMember records loyalty events only.
- An **accounting package** — no invoicing, no payroll, no financial reporting.
- A **CRM replacement** — member notes are supported, but OneMember is not a full customer relationship tool.
- A **complex enterprise platform** — no custom workflows, no developer integrations required, no lengthy onboarding.
- A **marketing agency** — OneMember gives merchants the tools; the merchant runs their own programme.

---

## Merchant Problems We Solve

| Problem | How OneMember Solves It |
|---------|------------------------|
| **Customers never return** | Loyalty programmes give customers a tangible reason to come back — points to earn, a stamp card to complete, a reward waiting for them. |
| **Expensive advertising** | Retaining an existing customer costs far less than acquiring a new one. OneMember shifts spend from acquisition to retention. |
| **Paper loyalty cards get lost** | Digital member profiles mean the record is always in the system — no card required, no lost progress. |
| **Staff cannot track rewards** | Every redemption is logged instantly. Staff check the member's balance on-screen and record it in seconds. |
| **No customer history** | Every purchase and redemption is recorded on the member's profile. Merchants can see who their best customers are and how often they visit. |
| **No measurable loyalty** | The dashboard shows Active Members, Points Issued Today, Rewards Redeemed Today, and Top Members — giving merchants a real measure of their loyalty programme's impact. |

---

## Customer Benefits

### Benefits for Merchants

| Benefit | Detail |
|---------|--------|
| **More repeat customers** | Members with points or stamps in progress are statistically more likely to return before switching to a competitor. |
| **Increased customer lifetime value** | Repeat customers spend more over time. Rewarding loyalty increases the total revenue per member. |
| **Better customer relationships** | Knowing a customer's name, history, and preferences — visible on their member profile — enables more personal service. |
| **Easy reward management** | Create rewards once, set a points threshold, and let the system handle the rest. No manual tracking. |
| **Faster staff training** | The interface is designed for non-technical users. New staff can learn to add a member and record a purchase in minutes. |
| **Reduced marketing costs** | A loyalty programme that retains existing customers reduces the budget needed for paid advertising to replace lost ones. |
| **Better business insights** | The dashboard surfaces which customers are most valuable, which campaigns are active, and how many rewards are being redeemed — without a data analyst. |

---

## Brand Personality

OneMember presents itself as:

| Trait | What It Means in Practice |
|-------|--------------------------|
| **Professional** | Clean, consistent UI. Reliable data. A product merchants are proud to use with their customers. |
| **Friendly** | Plain English throughout. No technical jargon. Onboarding celebrates progress ("You're ready!"). |
| **Simple** | Every screen has one primary action. Complexity is hidden behind sensible defaults. |
| **Trustworthy** | Immutable transaction history. Transparent member balances. No surprises in billing. |
| **Affordable** | Priced for owner-operators, not IT departments. The value is obvious from the first week. |
| **Modern** | Clean Bootstrap 5 design. Fast load times. Works on any device the merchant has on their counter. |

OneMember is **never** intimidating. **Never** overly technical. If a feature requires a manual to use, it is not finished.

---

## Writing Style

All marketing copy, UI text, email content, and documentation should follow these principles:

**Simple.** Use short sentences. Use common words. If a simpler word exists, use it.

**Conversational.** Write as if explaining to a merchant over a coffee. Not as a press release.

**Benefit-focused.** Lead with what the merchant gains, not what the feature does.

**Outcome over feature.** Talk about what happens as a result, not the mechanism that causes it.

### Examples

| Instead of... | Prefer... |
|--------------|----------|
| "Create reward campaigns." | "Keep customers coming back." |
| "Configure your loyalty programme settings." | "Set up your rewards in minutes." |
| "Record a purchase transaction." | "Give your customer their points." |
| "View the member activity log." | "See everything your customer has earned and redeemed." |
| "Onboarding wizard step 3 of 6." | "You're halfway there." |

---

## Decision Framework

Before any new feature is approved for a sprint, it must pass this test:

1. **Does it make OneMember more likely to succeed as a business?**

2. **Will at least 20% of merchants use it?**  
   If not, it is probably a niche feature and should wait.

3. **Does it help merchants do at least one of the following?**
   - Acquire customers
   - Retain customers
   - Increase revenue
   - Save time
   - Improve customer loyalty

4. **Can the feature be explained in one sentence?**  
   If it takes a paragraph to explain, it is too complex for V1.0.

5. **Does it belong in Version 1.0?**  
   If not, move it to Version 1.1 or Version 2.0 (see [docs/14-Version-2.0-Ideas.md](14-Version-2.0-Ideas.md)).

Features that do not pass this framework must not be added — regardless of how useful they seem in isolation.

---

## Long-Term Vision

> **OneMember aims to become the operating system for customer loyalty for small and medium-sized businesses.**

Version 1.0 focuses entirely on loyalty: points, stamps, rewards, and member management. It does one thing and does it well.

Future versions may expand — without compromising simplicity — into:

- **Marketing automation** — automated re-engagement emails, birthday triggers, lapsed-member campaigns
- **Customer engagement** — push notifications, in-app messages, milestone celebrations
- **Digital memberships** — subscription-based membership tiers (monthly/annual member clubs)
- **Gift cards** — digital gift cards redeemable in-store
- **Referrals** — member-get-member programmes with bonus rewards
- **Mobile apps** — customer-facing iOS and Android apps for checking balances and redeeming rewards
- **AI-powered recommendations** — smart campaign suggestions based on member behaviour and industry benchmarks

Each expansion will be evaluated against the Decision Framework above. Complexity will never be added for its own sake.
