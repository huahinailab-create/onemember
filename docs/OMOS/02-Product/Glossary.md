# Glossary

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [02-Product/Product-Terminology.md](./Product-Terminology.md), [02-Product/Product-Bible.md](./Product-Bible.md), [00-Executive/Vision.md](../00-Executive/Vision.md) |

---

## Purpose

This Glossary defines the official terms used across OneMember — in code, documentation, product copy, and team communication. When two people use the same word to mean different things, decisions go wrong. When the same thing is named differently in the UI and in the database, bugs are created.

All OneMember team members, including the AI development team, must use these terms consistently.

---

## Core Entities

### Merchant
A business that uses OneMember to run a loyalty programme. A Merchant is the primary customer of OneMember's SaaS platform. A Merchant may have one or more Branches (locations). In the system, `Merchant` is represented by the `merchants` table and the `Merchant` Eloquent model.

### Member
A customer who has joined a Merchant's loyalty programme. A Member is associated with a specific Merchant. The same person can be a Member of multiple Merchant programmes — in Phase 1, these are separate records. In Phase 2 (Customer Wallet), a single consumer identity may link to multiple Merchant memberships.

**Important:** "Member" in OneMember's context always refers to a loyalty programme member (consumer), not a merchant account holder. Do not use "member" to describe Merchant account users.

### Customer
In general communication, "customer" refers to the consumer who shops at a merchant. In technical contexts, use `Member` when referring to a record in the `members` table. In product copy and marketing, "customer" is preferred for readability.

### Staff
A person who works for a Merchant and uses OneMember on behalf of the Merchant (e.g., to record a sale, scan a member QR code, or issue a stamp). Staff are not Merchants (they do not own the account) and not Members (they do not earn loyalty points). Future: Staff accounts with role-based permissions.

---

## Loyalty Programme

### Loyalty Programme
The configuration a Merchant sets up to define how customers earn and redeem rewards. A Merchant has one active Loyalty Programme at a time. In Phase 1, a Loyalty Programme is either a Points Programme or a Stamp Card Programme.

### Points Programme
A Loyalty Programme in which customers earn Points based on spend. Points are accumulated and redeemed for Rewards.

### Stamp Card Programme
A Loyalty Programme in which customers receive one Stamp per visit or qualifying purchase. When the required number of Stamps is collected, the customer earns a Reward automatically.

### Points
The unit of loyalty value in a Points Programme. Points are earned by Members and redeemed for Rewards. Points are always associated with a specific Merchant — they are not universal currency.

### Stamp
The unit of loyalty value in a Stamp Card Programme. One Stamp is awarded per qualifying action (visit, purchase, or manual award). Stamps are not monetary.

### Balance
The current total of unredeemed Points a Member holds. Stored on the `members` table as `points_balance`.

### Transaction
A record of a Points or Stamps earning or redemption event. Stored in the `loyalty_transactions` table. Every point or stamp movement must produce a Transaction record.

---

## Campaigns and Rewards

### Campaign
A time-bounded programme that defines how a Merchant rewards Members. A Campaign contains the rules for earning Points or Stamps and the Rewards available for redemption. In Phase 1, a Merchant has one Campaign active at a time. In Phase 2+, Merchants may run multiple simultaneous Campaigns.

### Campaign Type
The classification of a Campaign's core mechanic. Current types: `points` (spend-based earning) and `stamps` (visit-based earning).

### Campaign Status
The lifecycle state of a Campaign: `draft`, `active`, `paused`, `archived`.

### Reward
A benefit a Member can claim by redeeming Points or completing a Stamp Card. Examples: "Free coffee," "10% discount on next purchase," "Free birthday treat." A Reward has a name, a description, and (if applicable) a monetary value or discount percentage.

### Redemption
The act of a Member claiming a Reward. A Redemption produces a Transaction record and reduces the Member's Points balance (for Points Programmes) or resets their Stamp count (for Stamp Programmes).

### Birthday Bonus
An automatic Points or Stamp award given to a Member on or near their birthday. Configured per Campaign. A Birthday Bonus is a type of automated Campaign rule, not a separate Campaign.

### Point Expiry
The date after which unused Points are no longer valid. Configured per Campaign (never, after X days from earn date, or on a fixed calendar date). Expiry is enforced by a scheduled job.

---

## Platform Layers (added 2026-07-06, ADR-012)

### OneMember Core
The lightweight, global layer every merchant receives: merchant management, customer identity, membership, loyalty, rewards, campaigns, analytics, notifications, authentication, APIs, AI foundation. Country-specific code is kept out of Core.

### OneMember App
An optional, installable extension a merchant adds per need (Commerce, POS, Inventory, Accounting, Restaurant, Hotel PMS, etc.). Commerce — including the Merchant Storefront — is an App, not Core. The ecosystem name "OneStore" is retired.

### Country Extension
Optional country-specific capability offered based on the merchant's country (e.g., PromptPay QR display for Thailand, DuitNow for Malaysia). Payment extensions only display the merchant's own payment identity — money never touches OneMember.

---

## Identity & Custodianship (added 2026-07-06, ADR-010)

### Custodian
OneMember's role with respect to customer data: it holds identity, consent, and loyalty access **in trust**. OneMember does not own customers; customers control their identity, merchants own their business relationships.

### OneMember Identity
The single global customer identity, anchored to **one verified mobile phone number** (one phone = one identity; duplicates prohibited). Implemented in Phase 2 as the `customers` record.

### OneMember Card / OneMember ID
The customer's portable membership credential shown at any merchant. Its QR encodes **only a secure token or OneMember ID — never raw personal data**.

### Scan-to-Join
The Phase 2 flow in which a merchant scans a customer's OneMember Card to enrol them as a Member without re-entering information, **subject to the customer's explicit consent** at that moment.

### Consent (per merchant, per data type)
The customer's explicit, optional, clearly-worded permission for a specific merchant to receive a specific category of their data (profile, birthday, marketing, analytics). Never assumed, never pre-ticked, never merchant-to-merchant.

### Merchant Storefront
The Phase 4 commerce capability: merchants list products/services and take orders inside OneMember while remaining the **seller of record** — payment flows directly customer→merchant, OneMember takes no commission and never holds funds.

---

## Platform Modules

### Customer Wallet
The consumer-facing application (Phase 2+) in which a single consumer can see and manage all their OneMember loyalty memberships in one place. The Wallet is the key enabler of cross-merchant network effects.

### Enterprise Bridge
A set of APIs and integration tools (Phase 2+) that allow large organisations to connect their existing CRM, POS, or membership system to OneMember's customer wallet and loyalty infrastructure.

### Commerce
The module (Phase 3+) through which Merchants list products and services, and Customers can browse, order, and pay — earning loyalty points on every purchase.

### POS (Point of Sale Lite)
The staff-facing interface (Phase 3+) for recording sales, awarding points, and issuing receipts at the point of sale. Not a full POS system — a lightweight tool for merchants who have no POS today.

### Merchant Intelligence
The AI-powered analytics module (currently live, Phase 1) that provides Merchants with a health score, trend insights, and actionable recommendations based on their campaign and member data.

---

## Business Entities

### Subscription
The recurring billing relationship between a Merchant and OneMember. Managed via Stripe. Subscription tier determines which features are available.

### Plan
The subscription tier a Merchant is on (e.g., Starter, Growth, Professional, Enterprise). Each Plan has defined feature limits and a monthly price.

### Trial Period
A time-limited period in which a new Merchant can use OneMember without a Subscription. Duration is defined by Plan configuration.

### Merchant Settings
The merchant-level configuration stored in the `settings` column of the `merchants` table (JSON). Includes: timezone, currency, date format, branding colours, notification preferences, and loyalty programme defaults.

---

## Technical Terms

### Multi-Tenancy
OneMember's data model in which all Merchants share a single database, with every record scoped to a `merchant_id`. No Merchant can access another Merchant's data.

### Event-Driven Architecture
OneMember's approach to cross-cutting concerns (email, audit, analytics) in which controllers fire Events and Listeners handle the side effects. Controllers never send email directly.

### Queue
The background job processing system. In production: `QUEUE_CONNECTION=database`. Jobs (including email sending) are processed asynchronously.

### Audit Log
A record of significant system actions written to the `developer_actions` table (developer actions) or a future `audit_logs` table (merchant actions). Every destructive or sensitive action must produce an audit record.
