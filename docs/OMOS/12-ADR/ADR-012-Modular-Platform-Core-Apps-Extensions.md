# ADR-012 — Modular Platform: Core, Apps, Country Extensions

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-06 — global repositioning directive) |
| **Date** | 2026-07-06 |
| **Author** | Claude Fable 5 (GLOBAL-001), decisions by Product Owner |
| **Supersedes** | Module framing in Bible v1.0.0 (Commerce/POS/Inventory/Accounting as platform phases-modules); resolves DR-31 (regional expansion) |
| **Related Documents** | [Global-Platform-Repositioning.md](../00-Executive/Global-Platform-Repositioning.md), [Product-Bible.md](../02-Product/Product-Bible.md), [ADR-004](./ADR-004-Laravel-Architecture.md), [ADR-011](./ADR-011-Commerce-Principles-Phase-4.md) |

---

## Context

OneMember is repositioning from a Thailand-focused loyalty application to a global merchant membership platform. A single ever-growing feature set would turn the Core into the monolithic everything-app the Product Owner explicitly forbids, and hardcoded Thai specifics (payments, defaults) block any-country merchants.

## Decision (approved)

1. **Three layers.** Layer 1 **OneMember Core** (global, every merchant): merchant management, customer identity, membership, loyalty, rewards, campaigns, analytics, notifications, authentication, APIs, AI foundation. Layer 2 **OneMember Apps** (optional, per merchant): Commerce, POS, Accounting, Procurement, Restaurant, Hotel PMS, Appointment Booking, CRM, Inventory, AI Marketing, Shipping, Gift Cards, Coupons, Staff Management, Payroll, ERP Connectors. Layer 3 **Country Extensions** (optional, per country): PromptPay (TH), DuitNow (MY), KBZ Pay (MM), UPI (IN), VNPay (VN), Stripe/PayPal (global).
2. **Commerce is an App, not Core.** The Merchant Storefront and everything commerce-shaped lives in the Commerce App (ADR-011 rules unchanged). Phase 4 remains its target availability window.
3. **Core stays lightweight and global.** Country-specific code never enters Core unless absolutely necessary; local payment systems are never hardcoded into Core.
4. **Product modularity precedes code modularity.** Apps are implemented first as first-party modules inside the existing Laravel monolith (ADR-004 stands): per-merchant `installed_apps`, app-scoped routes/migrations/policies/feature-gates, clean namespaces. Physical extraction only ever follows the SCALE-000 §1.19 seam rules on load evidence. **This ADR authorises no microservices.**
5. **Global by configuration.** Merchant chooses country, language, currency, timezone; those choices drive available extensions, defaults, and copy. Browser-language detection stays banned (DECISION-067 rule, now global).
6. **DR-31 resolved:** there is no "regional expansion phase" requiring local operations. Any-country merchants are served by the same software plus Country Extensions; OneMember ships software, not offices.
7. The name **"OneStore" is retired**; the ecosystem is "OneMember Apps".

## Options Considered

**A — Three-layer modular platform inside the monolith (chosen).** Product-level modularity, zero premature infrastructure, extraction path preserved.
**B — Keep single feature-set platform, gate by plan tiers only.** Simpler billing, but Core inevitably bloats into ERP territory; violates the NOT-list; rejected.
**C — Microservices per App now.** Team size, ops burden, and Scalability Review rejection list all say no; rejected.

## Consequences

- Bible v2.0.0 restructures the module table into Core / Apps / Extensions; audience is global.
- New open decisions: **DR-32** (pricing tiers incl. free-100-members and per-app pricing — CEO), **DR-33** (legal T&C programme; wording pending counsel), **DR-34** (App framework technical spec: registry, install/uninstall lifecycle, data-on-uninstall policy — CTO spec before CORE-001 implementation).
- Recommended next implementation sprint: **CORE-001 — Global Onboarding & App Framework Foundation** (Repositioning doc §14).
- Existing shipped features are all Core; no rework. PH2 wallet track continues unchanged (identity is Core).
- Launch Kit, Counter Mode, analytics remain Core merchant tooling.
