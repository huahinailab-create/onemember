# Product Bible

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 2.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-06 |
| **Related Documents** | [Global-Platform-Repositioning.md](../00-Executive/Global-Platform-Repositioning.md), [ADR-012](../12-ADR/ADR-012-Modular-Platform-Core-Apps-Extensions.md), [Vision.md](../00-Executive/Vision.md), [Mission.md](../00-Executive/Mission.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md), [Version-2.0-Vision-and-Master-Roadmap-2026-2030.md](../09-Roadmap/Version-2.0-Vision-and-Master-Roadmap-2026-2030.md), [Glossary.md](./Glossary.md), [ADR-010](../12-ADR/ADR-010-Custodian-Identity-Consent.md), [ADR-011](../12-ADR/ADR-011-Commerce-Principles-Phase-4.md) |

---

## Purpose

The Product Bible is the master reference for what OneMember is, who it serves, and how every product area works. It is the most important product document in OMOS.

Every sprint spec, architecture decision, and brand guideline must be consistent with the Product Bible. When there is a conflict, the Product Bible wins — unless the Product Owner explicitly approves an exception and updates this document.

---

## Foundational Product Principle (approved by Product Owner, 2026-07-06)

> **OneMember does NOT own customers.**
>
> OneMember is the **trusted custodian** of customer identity, consent, loyalty access, and engagement tools.
>
> **Customers** control their identity and what information they share.
>
> **Merchants** own their business relationship with their customers — their loyalty rules, campaigns, rewards, products, pricing, payments, fulfillment, and customer service.
>
> **OneMember** supplies the technology layer that connects merchants and customers.

Every feature, data model, and business decision must be checkable against this principle. If a proposal makes OneMember the owner of the customer relationship, the seller of the merchant's goods, or the holder of anyone's money — it is wrong.

## One-Line Platform Principle (approved)

> OneMember is a merchant-first **Membership, Loyalty, Customer Identity and Engagement Platform**. Merchants remain completely independent — they retain full control over pricing, inventory, fulfillment, customer service, and payments. Customers control their identity. OneMember provides the technology.
>
> OneMember is **NOT** a marketplace, a payment processor, a logistics company, a bank, an accounting system, a POS company, or an ERP.

---

## Introduction

OneMember is a **global merchant membership platform** (repositioned 2026-07-06 from Thailand-focused loyalty application — see [Global-Platform-Repositioning](../00-Executive/Global-Platform-Repositioning.md)).

It is not a loyalty app. It is not a CRM. It is not a marketing tool. It is not a POS system. It is not a marketplace. It is a platform that brings these capabilities together in service of one goal: **helping merchants build lasting, profitable relationships with their customers**.

The platform is modular (ADR-012): a lightweight, global **OneMember Core** every merchant gets; optional **OneMember Apps** merchants install per need (Commerce is an App, not Core); and **Country Extensions** for local capabilities like payment-QR display formats. The Core must never become a monolithic application. Merchants can come from any country; OneMember provides software, never local operations.

The platform is built in phases (see Roadmap Positioning below). Every module connects. Loyalty drives repeat visits. Repeat visits drive commerce. Commerce drives merchant results. Results drive analytics. Analytics drives AI. AI drives merchant decisions. Merchant decisions drive loyalty. This is the flywheel.

---

## Who OneMember Serves

### Primary User: The Merchant
A small or medium business owner — launch market Thailand, platform global — who wants to grow their business by building better relationships with their customers. They are not technical. They do not have a marketing team. They need results, not complexity. **The merchant owns the business relationship with their customers.**

### Secondary User: The Customer (Member)
A customer who shops at OneMember-powered merchants. They want to earn rewards effortlessly and not be buried in apps and paper cards. **They own and control their identity and what they share with each merchant.** OneMember holds it in trust.

### Tertiary User: The Enterprise (Phase 2+)
A large organisation that wants to connect their existing CRM, POS, or membership system to OneMember's customer wallet infrastructure.

---

## Identity Model (approved by Product Owner, 2026-07-06 — see ADR-010)

1. **One mobile phone number = one global OneMember identity.** Duplicate customer accounts must not exist.
2. If multiple merchants already hold Member records for the same person, those records may be connected to the customer's OneMember identity **only with explicit customer consent**. Consent is optional and must be clear — never assumed, never pre-ticked, never automatic.
3. Existing merchant loyalty data (balance, history) may be surfaced in the customer's OneMember Wallet **only with customer approval**.
4. **Each merchant keeps its own loyalty rules.** OneMember never combines, converts, or merges points between different merchants unless a future, explicitly approved business rule allows it.
5. **The OneMember Card / OneMember ID:** the customer's portable proof of identity. Its QR contains **only a secure token or OneMember ID — never raw personal data** (no name, phone, or birthday in the payload).
6. **Scan-to-join:** a customer can show their OneMember Card at any merchant; the merchant scans it to add the customer as a member **without re-entering information — subject to the customer's consent** at the moment of joining.

## Privacy & Access Model (approved by Product Owner, 2026-07-06 — see ADR-010)

1. OneMember is a **custodian** of identity and consent, not the owner of customers.
2. Merchants can access customer/member data inside OneMember **only while their OneMember account and subscription are active**. If a subscription expires, is suspended, or access is lost, the merchant's access to that data is **disabled until restored**. (The customer's own view of their memberships is unaffected.)
3. Customers control the sharing of their profile information with each merchant, per data type.
4. **Merchant-to-merchant data sharing never happens automatically.** Any cross-merchant membership join must be customer-approved.

## Commerce Principles (approved by Product Owner, 2026-07-06 — see ADR-011)

**Commerce is Phase 4, not Phase 2.** The identity, consent, and engagement layers must be trusted and proven before commerce is added on top of them.

When commerce is introduced:

1. OneMember provides **Merchant Storefront** capability: merchants can list products or services inside OneMember.
2. **Customers order from the merchant, not from OneMember.** The merchant is always the seller / merchant of record and issues the invoice or receipt.
3. **Payment goes directly from customer to merchant.** OneMember never receives, holds, settles, transfers, or escrows customer money.
4. **OneMember charges no GP, commission, marketplace fee, or transaction percentage.** OneMember earns from merchant subscription tiers and future approved platform services. OneMember helps merchants keep **100% of each sale**.
5. **The merchant handles refunds, customer service, tax obligations, pickup, delivery, shipping, and fulfillment.**
6. Fulfillment is merchant-controlled and depends on the product type: pickup, merchant delivery, shipping, appointment booking, or (future) digital fulfillment. **Restaurants define their own delivery radius and delivery rules.**
7. OneMember provides the tools around the sale: ordering, identity, loyalty, communication, and analytics.

## What OneMember Must Never Become (approved)

- **Grab, Lazada, Shopee, or any commission marketplace** — OneMember never takes a cut of a merchant's sale.
- **The owner of customers** — customer relationships belong to merchants; identity belongs to customers.
- **A money handler** — no receiving, holding, settling, transferring, or escrowing customer funds.
- **A data broker or advertising network** — customer data is never resold; attention is never monetised.
- **A competitor to its own merchants** — OneMember is infrastructure, never a seller.

(The full exclusion list, including social features and financial services, lives in the [Master Roadmap §16](../09-Roadmap/Version-2.0-Vision-and-Master-Roadmap-2026-2030.md) and [Long-term-Roadmap](../09-Roadmap/Long-term-Roadmap.md).)

---

## Roadmap Positioning (approved by Product Owner, 2026-07-06)

| Phase | Content |
|---|---|
| **Phase 1** | Merchant platform: loyalty campaigns, members, rewards, transactions, admin dashboard, Counter Mode, analytics. *(Live.)* |
| **Phase 2** | Customer Wallet: Universal Identity, OneMember Card / OneMember ID, Customer QR, cross-merchant join flow, consent, Apple Wallet, Google Wallet. |
| **Phase 3** | Merchant growth tools: AI marketing, advanced CRM, referrals, campaign automation. |
| **Phase 4** | Merchant Storefront / commerce: product catalogues, ordering, pickup, merchant delivery, shipping, QR payment display, merchant-controlled fulfillment. |

**Global requirements (2026-07-06):** language/country/currency/timezone are merchant-configured (onboarding + settings), never browser-derived; onboarding collects country, language, plan, and versioned terms acceptance; Apps are recommended by business type. Pricing direction: free ≤ 100 members, paid tiers scale by members/features/automation/API/apps (detail = DR-32).

**Localization model (2026-07-07, DECISION-095):** a merchant's **internal language** (dashboard UI) is separate from its **customer-facing languages** (storefront, portal, join, order pages — ordered list, first = default, customer may switch among them). A merchant declares a **primary currency** plus optional **additional accepted currencies** (display only — OneMember never converts money; automatic conversion is future work). Example: a Cambodian merchant may operate internally in English, offer customers Khmer + English, and accept KHR + USD. Allowed values are simple documented config lists, not a country/currency service.

This positioning **supersedes** earlier phase orderings that placed commerce in Phase 3. Rationale: growth tools deepen merchant value on the proven loyalty base before commerce raises the stakes; commerce requires the trust, identity, and consent rails of Phases 2–3 to be mature. Regional expansion timing is not fixed by this positioning (open decision — see decision register).

---

## Platform Structure (ADR-012 — Core / Apps / Extensions)

| Layer | Contents | Documents |
|---|---|---|
| **OneMember Core** (every merchant, global) | Merchant management, Customer Identity (live, PH2-001A), Membership, Loyalty, Rewards, Campaigns, Analytics, Notifications, Authentication, APIs, AI Foundation; Customer Wallet UX (Phase 2); Enterprise Bridge (Phase 2+); growth-tool basics | [Merchant-Platform.md](./Merchant-Platform.md), [Customer-Wallet package](./Customer-Wallet/README.md), [Enterprise-Bridge.md](./Enterprise-Bridge.md), [Analytics.md](./Analytics.md) |
| **OneMember Apps** (optional installs) | **Commerce / Merchant Storefront** (Phase 4 target), POS, Inventory, Accounting, Procurement, Restaurant (QR menu, table ordering), Hotel PMS, Appointment Booking, CRM, AI Marketing, Shipping, Gift Cards*, Coupons*, Staff Management, Payroll, ERP Connectors (*still DR-gated) | [Commerce.md](./Commerce.md), [POS.md](./POS.md), [Inventory.md](./Inventory.md), [Accounting.md](./Accounting.md), [Procurement.md](./Procurement.md) |
| **Country Extensions** (per country) | TH PromptPay, MY DuitNow, MM KBZ Pay, IN UPI, VN VNPay, global Stripe/PayPal — payment-QR display and local capabilities only; never money handling | [Global-Platform-Repositioning §3](../00-Executive/Global-Platform-Repositioning.md) |

---

## Product Rules

These rules apply to every product decision made about OneMember.

1. Every feature must create merchant value.
2. Every feature must reduce customer friction — or at minimum not increase it.
3. Every feature must fit the long-term architecture as documented in `docs/OMOS/12-ADR/`.
4. Every feature must be testable and maintainable by a future developer.
5. Every feature must have a defined success metric before it ships.
6. **Every feature must pass the custodian test:** it must not make OneMember the owner of customers, the seller of record, or the holder of money.

---

## What the Product Bible Is Not

The Product Bible is not a sprint specification (see `docs/OMOS/Sprints/`), not a technical architecture document (see `10-Architecture/` and `12-ADR/`), and not a marketing document.

The Product Bible is the authoritative answer to: **what is OneMember, and why does each part of it exist?**

---

## Document Status

Version 1.0.0 records the Product Owner's foundational decisions of 2026-07-06 (custodian principle, identity model, privacy/access model, commerce principles, phase positioning). Per-module feature specifications with user stories, edge cases per module, and cross-module interaction rules remain to be completed (see [Bible-Gap-Review](./Bible-Gap-Review-2026-07.md)).
