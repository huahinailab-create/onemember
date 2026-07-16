# ADR-017 — Customer Address Book & Checkout Address Foundation (CUSTOMER-001B)

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-15) |
| **Date** | 2026-07-15 |
| **Author** | Claude Fable 5 (sprint CUSTOMER-001B) |
| **Related Documents** | [ADR-016](./ADR-016-Customer-Identity-Foundation.md) (customer identity), [ADR-010](./ADR-010-Custodian-Identity-Consent.md), DECISION-101 |

---

## Context

CUSTOMER-001A gave customers an account. They still had to type their delivery address into every order at every store. The charter: one permanent, customer-owned address book that works across every OneMember merchant — designed as durable identity infrastructure (food delivery, retail shipping, service appointments, hotel delivery, future Wallet), not a delivery form. Merchant privacy is non-negotiable: a merchant must never see anything but the one address chosen for the current order.

## Decision (approved)

### Customer-owned, generic-schema address book

New `customer_addresses` table owned solely by the customer (FK to `customers`, uuid public identity, soft deletes). Administrative areas are stored **generically** — `admin_area_1` (largest: province/state/region) through `admin_area_4` (smallest: ward/village) — so no country's structure is hardcoded into the schema. What each level *means*, which fields a country uses, which are required, and the postcode format all live in `config/customer_address.php` (TH: province/district/subdistrict/postcode required; MM: state-region/district/township/ward-village, township + state required). **A new country is one config entry** — no migration, no code change. Latitude/longitude columns exist but are nullable and never required (reserved for future GPS features, which this sprint explicitly does not build).

### One default, safe lifecycle

`AddressBookService` is the single mutation path and holds the invariants: exactly one default among active addresses; the first address becomes the default automatically; deleting or archiving the default promotes the most recently used active address; duplicate never copies default status; every write trims whitespace and normalizes the contact phone to E.164 via the CUSTOMER-001A `PhoneNumberService`. Archive (`is_active = false`) hides an address from checkout without losing history; delete is a soft delete.

### Checkout: few clicks, guest path untouched

Signed-in customers see **Deliver to** — their active addresses as radio options (default first) plus **Add new address**, which reveals the full country-aware form with a **Save this address** checkbox. Guests see exactly the free-text field they had before (plus a sign-in hint). Works without JavaScript. Selecting a saved address places the order immediately with it.

### Merchant privacy: snapshot, never reference

The order stores only a **plain-text snapshot** of the chosen address (recipient, phone, formatted lines, instructions) in the existing `orders.address` column — no foreign key, no uuid, no schema change to orders. Consequences by construction: merchants can never traverse into the book; later edits to the book never rewrite what a merchant already received; the customer retains full ownership of their address history. Server-side, a chosen saved address must belong to the signed-in customer **and** be active; foreign addresses 404 in the book and fail validation at checkout — existence is never confirmed.

### Security

All ten address-book routes sit behind the `customer` guard; merchants (`web` guard) and guests are redirected. Ownership is enforced per request (404, not 403). Country-specific validation with reasonable length caps; a sanity cap (100) prevents book abuse while addresses remain "unlimited" as a product matter.

## Consequences

- **Positive:** every future fulfillment surface (delivery, shipping, appointments, hotel delivery, Wallet) reuses one address system; country expansion is config-only; privacy holds structurally rather than by policy.
- **Negative / accepted:** the merchant-side order view remains a text blob (no structured address analytics for merchants — deliberate); no admin-area reference data (free-text provinces/districts — a future data pack can add pickers without schema change); checkout's add-new form renders the customer's home-country schema (changing country mid-checkout re-renders only in the address book, not inline).
- **Reversibility:** high — dropping the table, routes, and the checkout branch restores CUSTOMER-001A exactly; guest checkout was never modified.
