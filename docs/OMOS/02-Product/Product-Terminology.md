# Product Terminology

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Glossary.md](./Glossary.md), [Product-Bible.md](./Product-Bible.md), [11-Standards/Coding-Standards.md](../11-Standards/Coding-Standards.md) |

---

## Purpose

Product Terminology maps the language used in the product (UI labels, error messages, emails) to the language used in code (model names, table names, variable names, route names). When product and engineering use different words for the same thing, confusion and bugs follow.

This document is the bridge between the Glossary (definitions) and the codebase (implementation).

---

## Terminology Mapping

| Product (UI/Copy) | Code (Model / Table / Column) | Notes |
|---|---|---|
| Merchant | `Merchant` model / `merchants` table | Always capitalised in code |
| Member / Customer | `Member` model / `members` table | Use "Member" in code, "customer" in public-facing copy |
| Loyalty Programme | `Campaign` model / `campaigns` table | The word "Campaign" is used in code; "Programme" or "Loyalty Programme" in UI |
| Points | `points_balance` column | Stored as integer (whole points only) |
| Stamps | `stamps_count` column | Stored as integer |
| Reward | `Reward` model / `rewards` table | |
| Redemption | `Redemption` model / `redemptions` table | |
| Transaction | `LoyaltyTransaction` model / `loyalty_transactions` table | |
| Settings | `merchant->settings` (JSON column) | Cast to array; never null in accessors |
| Birthday Bonus | `birthday_enabled`, `birthday_points` in campaign settings | Stored in campaign settings JSON |
| Point Expiry | `expiration_type`, `expiration_duration` in campaign settings | Stored in campaign settings JSON |
| Subscription | Stripe subscription | Managed via Stripe webhooks |
| Trial | `trial_ends_at` on `merchants` table | |

---

## Route Naming Conventions

| Feature Area | Route Name Pattern | Example |
|---|---|---|
| Dashboard | `dashboard` | `route('dashboard')` |
| Campaigns | `campaigns.*` | `route('campaigns.index')` |
| Campaign show/edit | `campaigns.show`, `campaigns.edit` | `route('campaigns.show', $campaign)` |
| Members | `members.*` | `route('members.index')` |
| Rewards | `campaigns.rewards.*` | `route('campaigns.rewards.create', $campaign)` |
| Settings | `settings.*` | `route('settings.index')` |
| Auth | `auth.*` | `route('auth.login')` |
| Verification | `verification.*` | `route('verification.notice')` |

---

## UI Label Standards

These labels must be used consistently across all Blade views. Do not invent synonyms.

| Concept | Approved UI Label | Do Not Use |
|---|---|---|
| A business using OneMember | Merchant | Store, Vendor, Brand, Shop |
| A loyalty programme participant | Member | User, Customer (in app context), Client |
| The points a member holds | Points balance | Credits, Coins, Tokens |
| A stamps-based programme | Stamp Card | Punch card, Loyalty card |
| The act of collecting a stamp | Earn a stamp | Punch, Check in |
| The act of using points | Redeem | Cash in, Use, Spend |
| A loyalty benefit | Reward | Prize, Gift, Perk |
| AI health analysis | Merchant Intelligence | AI Score, Analytics AI |

---

## Campaign Type Labels

| `campaign->type` value | UI Label | Description |
|---|---|---|
| `points` | Points Programme | Earn points based on spend |
| `stamps` | Stamp Card | Earn stamps per visit |

---

## Status Labels

### Campaign Status

| Status | UI Label | Colour |
|---|---|---|
| `draft` | Draft | Grey (`#6c757d`) |
| `active` | Active | Green (`#28a745`) |
| `paused` | Paused | Yellow (`#ffc107`) |
| `archived` | Archived | Dark grey (`#495057`) |

### Member Status

| Status | UI Label | Colour |
|---|---|---|
| `active` | Active | Green |
| `inactive` | Inactive | Grey |
| `blocked` | Blocked | Red |

---

## Language and Localisation Notes

All user-visible strings in Blade views must use `__()` helpers. The keys follow the `feature.descriptor` pattern:

```php
__('campaigns.status_active')   // "Active"
__('members.points_balance')    // "Points balance"
__('rewards.redeem_button')     // "Redeem reward"
```

Thai translations live in `lang/th/`. Every English key must have a Thai counterpart.

When adding new UI strings during a sprint, add both `lang/en/` and `lang/th/` entries before the sprint is marked complete.
