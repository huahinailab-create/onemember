# ADR-018 — OneMember Wallet MVP (CUSTOMER-001C)

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-15) |
| **Date** | 2026-07-15 |
| **Author** | Claude Fable 5 (sprint CUSTOMER-001C) |
| **Related Documents** | [ADR-016](./ADR-016-Customer-Identity-Foundation.md), [ADR-017](./ADR-017-Customer-Address-Book.md), [ADR-010](./ADR-010-Custodian-Identity-Consent.md), [ADR-001](./ADR-001-OneMember-First-Wallet.md), DECISION-102 |

---

## Context

ADR-001 declared the customer wallet the core network asset. CUSTOMER-001A gave customers an account; CUSTOMER-001B gave them an address book. The wallet is where those investments become visible to the customer: their home inside OneMember — "my relationships with local businesses live here", not "my loyalty points live here". The charter demands a hub every future customer feature (rewards, orders, gift cards, subscriptions, appointments, membership cards, notifications, AI recommendations) extends naturally, without redesign.

## Decision (approved)

### A relationship hub built as a read model

The wallet MVP is **read-only aggregation** over data that already exists — no new domain writes except customer preferences and order ownership. `App\Services\Wallet\WalletService` is the wallet's read model: memberships, home summary, rewards by merchant, chronological activity, order history. Every aggregate starts from the customer's own consented `CustomerMemberLink`s (ADR-010), so cross-customer or cross-merchant leakage is impossible by construction. Future surfaces are new aggregate methods + new nav items — the hub pattern, not a redesign.

### Wallet navigation and surfaces

`/account/wallet` (home: welcome by first name, merchants joined, rewards available, memberships preview, recent activity, quick links), My Places (memberships), membership detail, My Rewards, Activity, My Orders — plus the existing addresses, profile, settings reached via quick links. Login, OTP verification, and password reset all land on the wallet. Navigation is a horizontally scrollable pill bar (44px touch targets, `aria-current`, reduced-motion aware) on a wider shell.

### Merchant sovereignty inside the customer's hub

Each membership card shows **that merchant's** balance, labelled points or stamps from the merchant's active programme type. Points are never combined across merchants (tested). The membership detail is read-only: balance, membership info, campaign, rewards, last 10 transactions, merchant contact, storefront link when Commerce is installed. No redemption flow — the Redeem button exists but is honestly disabled with an explanatory tooltip. Reward statuses are only what the domain has: **Available** (active, affordable, in stock) and **Coming soon**; rewards have no expiry in the domain, so no "Expired" state was invented.

### Order ownership

New nullable `orders.customer_id`: orders placed while signed in belong to the wallet — genuine order history with items, totals, status, and the delivery-address snapshot used (ADR-017). Guest orders stay `NULL` and invisible; "Order again" links to the merchant's storefront rather than faking one-click reorder.

### Preferences

New `customers.preferences` JSON column (communication channel, marketing consent today) — extensible so notification settings and future preferences need no further migration. A "notifications coming soon" placeholder is honestly labelled, as are the six reserved wallet tiles (Membership Cards, Gift Cards, Subscriptions, Appointments, Bookings, Digital Wallet).

## Consequences

- **Positive:** the customer finally *sees* the network ("one account, many places"); every CUSTOMER-001x investment surfaces in one place; future features have an obvious home and an established pattern (aggregate method → view → nav pill).
- **Negative / accepted:** read-only means the wallet cannot yet act (no redemption, no reorder-in-place); activity/orders queries aggregate per request (fine at MVP scale, a wallet feed table can materialize it later); pre-CUSTOMER-001C orders without `customer_id` or a member link stay outside the history (documented, honest).
- **Reversibility:** high — the wallet is a layer over existing data; dropping routes/views/service and the two additive columns restores CUSTOMER-001B exactly.
