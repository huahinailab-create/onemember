# ADR-011 — Commerce Principles & Phase 4 Positioning

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-06) |
| **Date** | 2026-07-06 |
| **Author** | Claude Fable 5 (governance sprint GOV-001), decisions by Product Owner |
| **Supersedes** | Phase ordering in [Long-term-Roadmap](../09-Roadmap/Long-term-Roadmap.md) v1.0 (commerce was Phase 3) and the corresponding sections of the Master Roadmap v1.0 |
| **Related Documents** | [Commerce.md](../02-Product/Commerce.md), [Product-Bible.md](../02-Product/Product-Bible.md), [Version-2.0-Vision-and-Master-Roadmap-2026-2030.md](../09-Roadmap/Version-2.0-Vision-and-Master-Roadmap-2026-2030.md) |

---

## Context

Earlier roadmaps placed commerce in Phase 3 and growth tools across Phases 2–3, and left the commerce money-flow model implicit. The Product Owner has now fixed both the sequencing and the commercial model.

## Decision (approved)

1. **Phase re-ordering:** Phase 3 = merchant growth tools (AI marketing, advanced CRM, referrals, campaign automation). **Phase 4 = Merchant Storefront / commerce** (product catalogues, ordering, pickup, merchant delivery, shipping, QR payment display, merchant-controlled fulfillment). Commerce is explicitly **not** Phase 2.
2. **Merchant of record, always.** Customers order from the merchant; the merchant sells, invoices, fulfils, refunds, and serves.
3. **No money through OneMember.** Payment is direct customer → merchant. OneMember never receives, holds, settles, transfers, or escrows funds. "PromptPay integration" in earlier documents is re-scoped to **QR payment display** of the merchant's own payment identity — not a checkout gateway through OneMember.
4. **No commission economics.** No GP, commission, marketplace fee, or transaction percentage — ever. Revenue is merchant subscription tiers plus future approved platform services. Merchants keep 100% of each sale.
5. **Fulfillment is merchant-controlled** and product-type-dependent (pickup, merchant delivery, shipping, appointment booking, future digital). Restaurants define their own delivery radius and rules.
6. **Negative space:** OneMember never becomes Grab, Lazada, Shopee, or a commission marketplace.

## Rationale

Trust before transactions; engagement before ordering (full reasoning: [Commerce.md](../02-Product/Commerce.md) §"Why Phase 4"). The no-money-touch rule also keeps OneMember outside payment-institution licensing scope — a deliberate regulatory posture consistent with the existing exclusion of financial services.

## Consequences

- Long-term-Roadmap and Master Roadmap phase content re-mapped (Phase 3 ↔ growth tools, Phase 4 ↔ commerce). POS Lite and Inventory follow commerce into Phase 4.
- Regional expansion, previously Phase 4's headline, is **not repositioned by this ADR** — its placement is an open Product Owner decision (recorded in the decision register).
- Any future feature proposal involving holding funds, taking transaction cuts, or OneMember-as-seller is dead on arrival under this ADR plus the Bible's custodian test.
- Revenue-stream candidates in Master Roadmap §10 (API usage, marketing services) remain candidates under "future approved platform services" — each still needs individual CEO approval (DR-30).
