# 09 — Loyalty Engine Business Rules

This document defines all business rules for the OneMember loyalty engine.

It is the authoritative reference for the Product Owner, CTO, and Lead Developer.

No loyalty feature may be implemented without a corresponding rule documented here **and** a decision entry in `docs/08-Product-Decisions.md`.

---

## 1. Goals

### 1.1 Purpose of the Loyalty Engine

The loyalty engine allows merchants to reward members for purchases and interactions. It records every point movement, links rewards to those movements, and gives the merchant a clear audit trail.

The engine serves two parties:

- **Merchant** — configures programs, defines rewards, monitors activity, and manually controls point adjustments.
- **Member** — earns points through purchases, redeems points for rewards, and receives birthday bonuses.

### 1.2 Merchant Flexibility

Each merchant operates independently. Their loyalty programs, rewards, and member data are fully isolated from other merchants on the platform.

A merchant may run more than one loyalty program simultaneously (e.g. a points program for regular purchases and a stamp card for coffee visits). Each program has its own earn rate, rewards, and rules.

### 1.3 Simplicity for MVP

The MVP loyalty engine is intentionally limited. It covers the most common patterns used by small-to-medium merchants in Southeast Asia:

- Points earned per currency unit spent.
- Stamp cards collected per visit.
- Rewards redeemed by spending points or completing a stamp card.
- Birthday bonuses delivered automatically or manually.

Complexity such as tiers, cashback, referrals, and expiry schedules is deferred to future sprints.

---

## 2. Loyalty Program Types

Only two program types are supported in MVP.

### 2.1 Points Program (`type = 'points'`)

The member earns a configurable number of points for every unit of currency spent.

**How it works:**

1. The merchant records a purchase transaction for the member.
2. The system calculates points earned: `floor(purchase_amount × points_per_unit)`.
3. The calculated points are added to the member's `total_points` and `lifetime_points`.
4. A transaction record of type `earn` is written with `balance_before` and `balance_after`.

**Key rules:**

- Points are always whole integers. Fractional results are floored (never rounded up).
- A single transaction records a single purchase. Multiple items in one purchase are submitted as a single transaction amount.
- Points are credited immediately upon saving the transaction. There is no pending/approval step in MVP.
- `total_points` represents the member's spendable balance. It can decrease through redemptions and adjustments.
- `lifetime_points` is a running total of all points ever credited (earn and birthday only). It never decreases.

### 2.2 Stamp Card Program (`type = 'stamps'`)

The member earns one stamp per qualifying visit or purchase (regardless of amount). When the stamp card is complete, the member receives a predefined reward.

**How it works:**

1. The merchant records a stamp transaction for the member.
2. Each stamp transaction adds 1 point to `total_points` (stamps are modelled as points with a value of 1).
3. When `total_points` reaches the program's `stamps_required` threshold (stored in `settings`), the reward is automatically issued as a redemption record.
4. `total_points` is reset to 0 after the stamp card completes, OR accumulated across cards — this behaviour is configured per program in `settings.reset_on_complete` (boolean).

**Key rules:**

- The qualifying condition for a stamp (minimum spend, visit check-in) is defined in `settings.stamp_condition`.
- One stamp per transaction. A merchant cannot award two stamps in a single transaction.
- Stamp programs may have a maximum number of active cards per member (`settings.max_active_cards`). Default: 1.

> **Open Question OQ-001** — Should stamp cards support partial stamps (e.g. buy 2 coffees, get 2 stamps)? Or always 1 stamp per visit? See Section 10.

---

## 3. Program Configuration

A loyalty program is configured at the `loyalty_programs` table level. The following fields are merchant-configurable:

| Field | Description |
|---|---|
| `name` | Display name shown to the merchant (e.g. "Coffee Stamp Card") |
| `type` | `points` or `stamps` |
| `description` | Optional description for internal reference |
| `points_per_unit` | Points earned per 1 currency unit (Points Program only). Default: 1.00 |
| `is_active` | Whether the program accepts new transactions. Inactive programs are read-only |
| `starts_at` | Optional date the program becomes active. Null = active immediately |
| `ends_at` | Optional date the program closes. Null = no end date |
| `settings` | JSON field for type-specific configuration (see below) |

### 3.1 Settings JSON — Points Program

```json
{
  "min_purchase_amount": 0,
  "max_earn_per_transaction": null,
  "allow_manual_adjustment": true
}
```

| Key | Description |
|---|---|
| `min_purchase_amount` | Minimum purchase amount required to earn points. Default: 0 (any amount) |
| `max_earn_per_transaction` | Cap on points per transaction. Null = no cap |
| `allow_manual_adjustment` | Whether the merchant can manually credit/debit points |

### 3.2 Settings JSON — Stamp Card Program

```json
{
  "stamps_required": 10,
  "stamp_condition": "visit",
  "reset_on_complete": true,
  "max_active_cards": 1
}
```

| Key | Description |
|---|---|
| `stamps_required` | Number of stamps to complete one card |
| `stamp_condition` | `visit` (one per transaction, no minimum) or `min_spend` (requires minimum amount) |
| `reset_on_complete` | `true` = balance resets to 0 after card completion. `false` = stamps accumulate |
| `max_active_cards` | Maximum number of incomplete cards a member may hold at once |

### 3.3 Multiple Active Programs

A merchant may have multiple programs active simultaneously. Each program operates independently. A member earns points/stamps in each program separately. Redemptions are always associated with a specific program.

> **Open Question OQ-002** — If a merchant runs both a Points Program and a Stamp Card Program, should a single purchase transaction award points in both programs simultaneously, or must the merchant select which program to record against? See Section 10.

---

## 4. Reward Rules

### 4.1 Reward Definition

Rewards are defined by the merchant at the `rewards` table level and belong to a specific loyalty program. The following fields define a reward:

| Field | Description |
|---|---|
| `name` | Display name (e.g. "Free Coffee") |
| `type` | `discount`, `free_item`, `gift`, or `cashback` |
| `points_required` | Points the member must spend to redeem this reward |
| `value` | Monetary value or discount amount (used for `discount` and `cashback` types) |
| `quantity_available` | Stock limit. Null = unlimited |
| `quantity_redeemed` | Running count of times this reward has been redeemed |
| `is_active` | Whether the reward can be redeemed |
| `valid_from` | Earliest date the reward can be redeemed. Null = available immediately |
| `valid_until` | Latest date the reward can be redeemed. Null = no expiry |

### 4.2 Earning — Points Program

- Points are earned when the merchant records a purchase transaction.
- Earn rate: `floor(purchase_amount × points_per_unit)`.
- Minimum purchase amount is enforced if `settings.min_purchase_amount > 0`.
- Maximum earn per transaction is enforced if `settings.max_earn_per_transaction` is set.

### 4.3 Earning — Stamp Card Program

- One stamp is awarded per qualifying transaction.
- The qualifying condition is defined by `settings.stamp_condition`.

### 4.4 Redemption Flow

1. The merchant selects a member and a reward.
2. The system checks:
   - Member status is `active`.
   - Member `total_points` ≥ `reward.points_required`.
   - Reward `is_active` is `true`.
   - Current date is within `valid_from` and `valid_until` (if set).
   - `quantity_redeemed < quantity_available` (if `quantity_available` is not null).
3. If all checks pass:
   - A `redemption` record is created with `status = 'pending'` and a unique one-time `code`.
   - A `transaction` record of type `redeem` is written, with `points` as a negative integer.
   - `member.total_points` is decreased by `points_required`. `lifetime_points` is unchanged.
   - `reward.quantity_redeemed` is incremented by 1.
4. The merchant physically delivers the reward to the member.
5. The merchant marks the redemption as `used`, recording `redeemed_at` and `used_by`.

### 4.5 Redemption Expiry

A pending redemption that is not marked `used` before `expires_at` is considered expired. The system does not automatically refund points on expiry in MVP.

> **Open Question OQ-003** — Should expired redemptions automatically refund the points to the member, or is the points deduction permanent regardless of expiry? See Section 10.

### 4.6 Redemption Cancellation

A merchant may cancel a `pending` redemption. Upon cancellation:

- `status` is set to `cancelled`.
- Points are refunded to the member: a new `adjust` transaction is written for the positive point amount.

Only `pending` redemptions may be cancelled. `used` and `expired` redemptions cannot be reversed in MVP.

---

## 5. Birthday Rewards

### 5.1 Overview

A merchant may configure one birthday reward per loyalty program. The birthday reward is granted to a member during their birthday window.

Birthday rewards are defined at the `birthday_rewards` table level.

### 5.2 Birthday Reward Types

| Type | Behaviour |
|---|---|
| `points` | A fixed number of bonus points is credited to the member |
| `discount` | A percentage or fixed discount is issued as a redemption |
| `reward` | A specific reward from the rewards catalogue is issued as a redemption |

### 5.3 Birthday Window

Each birthday reward defines:

- `valid_days_before` — how many days before the member's birthday the reward becomes available. Default: 0 (birthday day only).
- `valid_days_after` — how many days after the birthday the member can still claim the reward. Default: 7.

**Example:** `valid_days_before = 2`, `valid_days_after = 5` means the member can claim the reward from 2 days before their birthday until 5 days after.

### 5.4 Delivery

In MVP, birthday rewards are **not delivered automatically**. The merchant must manually trigger the birthday reward from the member's workspace.

> **Open Question OQ-004** — Should birthday rewards be awarded automatically (e.g. by a scheduled job on the member's birthday), or always require manual merchant action? Automatic delivery requires a background scheduler. See Section 10.

### 5.5 Transaction Record

When a birthday reward of type `points` is granted:

- A `transaction` record of type `birthday` is written.
- `points` is the positive integer bonus amount.
- `reference` morphs to the `birthday_rewards` record.
- `member.total_points` and `member.lifetime_points` are both incremented.

When a birthday reward of type `discount` or `reward` is granted:

- A `redemption` record is created with `status = 'pending'`.
- A `transaction` record of type `birthday` is written with `points = 0` (no point cost to the member).
- The redemption `expires_at` is set to `member.birthday + valid_days_after` for the current year.

### 5.6 One Reward Per Year

A member may only receive one birthday reward per loyalty program per calendar year. The system must check whether a birthday reward has already been issued for the current year before granting another.

---

## 6. Transaction Rules

All point movements must be recorded as transactions. No point balance may change without a corresponding transaction record.

### 6.1 Transaction Types

| Type | Direction | Description |
|---|---|---|
| `earn` | Credit (+) | Points earned from a purchase |
| `redeem` | Debit (−) | Points spent to claim a reward |
| `adjust` | Credit (+) or Debit (−) | Manual adjustment by the merchant |
| `expire` | Debit (−) | Points removed due to expiry (reserved for future use) |
| `birthday` | Credit (+) | Birthday bonus points |

### 6.2 Mandatory Fields

Every transaction record must include:

- `merchant_id` — the merchant it belongs to.
- `member_id` — the member whose balance changed.
- `loyalty_program_id` — the program the transaction is associated with.
- `type` — one of the types above.
- `points` — integer; positive for credits, negative for debits.
- `balance_before` — the member's `total_points` immediately before this transaction.
- `balance_after` — the member's `total_points` immediately after this transaction.
- `created_by` — the user (merchant admin) who recorded the transaction. May be null for system-generated transactions.

### 6.3 Immutability

Transactions are immutable once written. They may not be edited or deleted. Corrections are made by writing a new compensating transaction of type `adjust`.

### 6.4 Balance Consistency

`balance_after` must always equal `balance_before + points`. Any discrepancy is a data integrity error. The application must validate this before writing.

### 6.5 Reference Morphs

The `reference` polymorphic columns (`reference_type`, `reference_id`) may point to:

- A `Redemption` record (for `redeem` transactions).
- A `BirthdayReward` record (for `birthday` transactions).
- Null (for `earn` and `adjust` transactions in MVP).

---

## 7. Member Behaviour

### 7.1 Enrolment

A member is enrolled in the merchant's loyalty ecosystem when their record is created. Enrolment into a specific program happens implicitly on their first transaction against that program. There is no explicit enrolment step in MVP.

### 7.2 Points Balance

- `total_points` — spendable balance. Increases on `earn`, `adjust` (credit), and `birthday`. Decreases on `redeem` and `adjust` (debit). Cannot go below 0.
- `lifetime_points` — cumulative credit-only total. Increases on `earn`, `adjust` (credit), and `birthday` only. Never decreases.

> **Open Question OQ-005** — Should `lifetime_points` include manual credit adjustments (`adjust` type), or only organic earn and birthday transactions? See Section 10.

### 7.3 Member Status

| Status | Effect |
|---|---|
| `active` | May earn points and redeem rewards |
| `inactive` | Cannot earn or redeem. Read-only on the member workspace |
| `blocked` | Cannot earn or redeem. Merchant must manually unblock |

Status changes are not implemented in MVP. Status is set to `active` on creation and remains there unless manually changed via a future admin action.

### 7.4 Archived Members

Archived members (soft-deleted) are excluded from all active program logic. They cannot earn points, receive birthday rewards, or redeem rewards. Their historical transaction records are preserved.

### 7.5 Duplicate Members

The system enforces unique mobile numbers per merchant. If a merchant attempts to enrol a member with a mobile number already on file, the system rejects the request with a validation error.

---

## 8. Merchant Limitations (MVP)

The following limitations apply for MVP and must be communicated to merchants during onboarding.

| Limitation | Detail |
|---|---|
| Program types | Only `points` and `stamps`. Tiers and cashback are not available |
| Programs per merchant | No hard limit, but UI is designed for 1–3 active programs |
| Manual point adjustment | Supported if `settings.allow_manual_adjustment = true` |
| Automatic expiry | Not implemented. Points do not expire in MVP |
| Automatic birthday delivery | Not implemented. Merchant must manually trigger birthday rewards |
| QR code member identification | QR codes are generated (future sprint) but not scanned in MVP |
| Bulk point import | Not implemented |
| Member-facing portal | Not implemented. Merchants manage everything |
| Multi-location | Not implemented. Each merchant account is single-location |
| API access | Not implemented |
| Email / SMS notifications | Not implemented |

---

## 9. Future Enhancements

The following ideas are noted for future consideration only. None are designed or approved for implementation.

- **Tier / VIP programs** — e.g. Bronze, Silver, Gold based on lifetime points.
- **Cashback programs** — percentage of spend returned as store credit.
- **Point expiry** — points expire after a configurable number of months of inactivity.
- **Automated birthday delivery** — scheduled job awards birthday bonuses without merchant action.
- **Member-facing web portal** — members check their own balance and redeem rewards.
- **QR code scanning** — merchant scans member QR at point of sale to identify the member.
- **Multi-location support** — one merchant account with multiple branches sharing a member pool.
- **Referral rewards** — members earn points for referring new members.
- **Bulk import** — CSV import of members and historical point balances.
- **API access** — REST API for POS system integration.
- **SMS / Email notifications** — automated messages when points are earned, rewards are available, or birthdays approach.
- **Program stacking rules** — explicit rules for whether a purchase earns across multiple programs simultaneously.
- **Reward approval workflow** — redemptions require manager approval above a threshold value.

---

## 10. Open Questions

The following business rules are unresolved and require Product Owner approval before implementation.

| # | Question | Impact |
|---|---|---|
| OQ-001 | Stamp cards: should one transaction award more than one stamp (e.g. buy 2 items = 2 stamps), or always exactly 1 stamp per transaction? | Affects transaction recording logic and settings schema |
| OQ-002 | Multi-program earn: if a merchant runs both a Points Program and a Stamp Card simultaneously, should a single purchase transaction record in both programs, or does the merchant choose one? | Affects the transaction creation form and points calculation |
| OQ-003 | Expired redemptions: should the system automatically refund points when a pending redemption passes its `expires_at` date? Or are points permanently deducted at redemption time? | Affects point balance integrity and any expiry job |
| OQ-004 | Birthday reward delivery: manual (merchant triggers) or automatic (scheduled job on birthday)? Automatic requires a background queue/scheduler. | Affects architecture — background jobs vs. manual workflow |
| OQ-005 | Lifetime points: should manual credit adjustments (`adjust` type) increment `lifetime_points`, or only organic `earn` and `birthday` transactions? | Affects reporting and tier eligibility in future |
| OQ-006 | Minimum member status for earning: should `inactive` members be fully blocked from earning, or only from redeeming? | Affects transaction validation rules |
| OQ-007 | Stamp card completion: when a stamp card completes, is the reward issued automatically as a `pending` redemption, or does the merchant confirm and trigger it manually? | Affects stamp card transaction flow |
| OQ-008 | Points floor on debit: if a manual `adjust` debit or a `redeem` would bring `total_points` below 0, should the system reject the transaction or clamp the balance to 0? | Affects balance validation rules |

---

*This document must be updated before implementation begins on any loyalty engine sprint.*
*All open questions must be resolved and recorded in `docs/08-Product-Decisions.md` before coding starts.*
