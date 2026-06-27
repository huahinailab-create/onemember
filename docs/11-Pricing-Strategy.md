# 11 — Pricing Strategy

This document defines the commercial model for OneMember.

Pricing amounts are **not** documented here. All pricing amounts will be determined by the Product Owner before commercial launch.

---

## Pricing Philosophy

### Free Trial

Every new merchant receives a **FREE 30-day Professional trial** upon registration.

- The trial begins on the date of registration.
- No credit card is required to start the trial.
- The merchant has access to all Professional-tier features during the trial period.
- The merchant is notified before the trial expires.

### After 30 Days

When the trial period ends, the merchant must choose a plan:

1. **Free** — limited functionality, no payment required.
2. **Starter** — paid plan for growing businesses.
3. **Professional** — paid plan for established businesses.
4. **Enterprise** — custom plan for large or multi-location businesses.

If the merchant takes no action at the end of the trial, they are **automatically moved to the Free plan**. No access is lost abruptly.

### Data Retention Guarantee

**Customer data is never deleted regardless of plan.**

A merchant who downgrades from Professional to Free retains all member records, transaction history, and loyalty program data. Feature access may be restricted, but data is never removed due to a plan change.

---

## Commercial Principles

| Principle | Detail |
|---|---|
| No setup fee | Merchants pay nothing to register and configure their account |
| Monthly billing | Default billing cycle at launch |
| Annual billing | To be introduced in a future release |
| Cancel any time | No minimum contract period for monthly plans |
| No hidden charges | All limits and features are clearly disclosed on the pricing page |
| Merchant data ownership | All data belongs to the merchant; OneMember does not sell or share it |

---

## Plans

### Intended Audience (by tier)

| Plan | Intended Audience |
|---|---|
| **Free** | Sole traders and micro-businesses testing the product or running a minimal programme |
| **Starter** | Small businesses with a growing member base who need core loyalty features |
| **Professional** | Established businesses that need the full feature set, automation, and reporting |
| **Enterprise** | Businesses with multiple locations, large member bases, or custom integration needs |

> **Note:** Feature limits, member caps, transaction limits, and pricing amounts for each plan will be determined by the Product Owner before commercial launch. Do not implement any plan enforcement logic until limits are formally approved and documented in `docs/08-Product-Decisions.md`.

---

## Plan Feature Matrix (Placeholder)

The table below identifies feature categories by plan. Specific limits are to be confirmed.

| Feature | Free | Starter | Professional | Enterprise |
|---|---|---|---|---|
| Members | TBD | TBD | TBD | TBD |
| Loyalty Programs | TBD | TBD | TBD | TBD |
| Rewards | TBD | TBD | TBD | TBD |
| Transactions per month | TBD | TBD | TBD | TBD |
| Birthday rewards | TBD | TBD | TBD | TBD |
| Reports & analytics | TBD | TBD | TBD | TBD |
| Staff accounts | TBD | TBD | TBD | TBD |
| Data export | TBD | TBD | TBD | TBD |
| API access | — | — | TBD | TBD |
| Priority support | — | — | TBD | TBD |
| Custom branding | — | — | TBD | TBD |
| Multi-location | — | — | — | TBD |

**Pricing will be determined before commercial launch.**

---

## Billing Notes (for future implementation)

The following are planning notes only. No billing infrastructure exists in MVP.

- Billing will be handled by a third-party payment provider (to be selected).
- Invoices will be issued by email.
- Plan changes take effect at the start of the next billing cycle.
- Downgrade behaviour (feature restriction, not data deletion) must be explicitly designed before implementation.

---

*All pricing amounts, plan limits, and billing implementation must be approved by the Product Owner and recorded in `docs/08-Product-Decisions.md` before any enforcement logic is built.*
