# Commerce — Merchant Storefront (Phase 4)

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active — principles approved by Product Owner 2026-07-06; detailed spec deferred to Phase 4 planning |
| **Last Updated** | 2026-07-06 |
| **Related Documents** | [Product-Bible.md](./Product-Bible.md), [ADR-011](../12-ADR/ADR-011-Commerce-Principles-Phase-4.md), [Merchant-Platform.md](./Merchant-Platform.md), [POS.md](./POS.md) |

---

## Purpose

Defines the approved, binding principles for OneMember's commerce capability — the **Merchant Storefront** — so that no earlier phase makes a decision that violates them, and so Phase 4 planning starts from settled ground.

## Why Commerce Is Phase 4, Not Phase 2 (approved)

Commerce is deliberately last:

1. **Trust before transactions.** Customers must first trust OneMember as the custodian of their identity and consent (Phase 2) before it mediates their orders.
2. **Engagement before ordering.** Merchants must first have growth tools — AI marketing, CRM, referrals, automation (Phase 3) — so a storefront launches into an engaged member base, not an empty room.
3. **Stakes.** Commerce touches pricing, tax, fulfillment, and disputes. Introducing it on immature identity/consent rails would put the whole trust model at risk.

## The Merchant Storefront (approved principles)

1. Merchants can list **products or services** inside OneMember (catalogues, categories, pricing, photos).
2. **Customers order from the merchant, not from OneMember.** The merchant is always the **seller / merchant of record**.
3. The **merchant issues the invoice or receipt**.
4. OneMember provides the surrounding tools: **ordering, identity, loyalty, communication, and analytics**.

## Payment Flow (approved — non-negotiable)

- **Payment goes directly from customer to merchant** (e.g., the merchant's own PromptPay QR displayed at order time).
- **OneMember never receives, holds, settles, transfers, or escrows customer money.**
- **OneMember charges no GP, no commission, no marketplace fee, no transaction percentage.**
- OneMember earns from **merchant subscription tiers and future approved platform services** only.
- Goal: the merchant keeps **100% of each sale**.

## Fulfillment Responsibility (approved)

The **merchant** handles refunds, customer service, tax obligations, and all fulfillment. Fulfillment modes depend on product type:

| Mode | Notes |
|---|---|
| Pickup | Default for food/retail |
| Merchant delivery | **Restaurants define their own delivery radius and delivery rules** |
| Shipping | Physical goods |
| Appointment booking | Services (salons, clinics) |
| Digital fulfillment | Future |

OneMember provides the configuration and order-management UI for these modes; it never performs fulfillment.

## What OneMember Commerce Must Never Become (approved)

- **Grab, Lazada, Shopee, or any commission marketplace.**
- A payment processor, wallet-of-funds, or escrow service.
- A seller competing with its own merchants.

## Open Items for Phase 4 Planning (not yet decided — do not assume)

- Detailed order lifecycle, statuses, and notifications spec
- Loyalty-on-commerce rules (orders auto-award points — mechanics TBD)
- QR payment display UX (merchant PromptPay QR presentation) and whether any payment-confirmation signal is captured
- Commerce analytics scope
- POS Lite / Inventory integration sequencing
- Pricing tier(s) that include Storefront (CEO pricing decision)

These are tracked in the decision register (`docs/08-Product-Decisions.md` open items + Master Roadmap DR register).
