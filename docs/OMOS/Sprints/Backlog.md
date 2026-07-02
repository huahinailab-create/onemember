# Sprint Backlog

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [README.md](./README.md), [../CurrentSprint.md](../CurrentSprint.md), [../Engineering-Backlog.md](../Engineering-Backlog.md), [../02-Product/Parking-Lot.md](../02-Product/Parking-Lot.md) |

---

## Purpose

The Sprint Backlog contains all planned sprints that have not yet been scheduled. Items here are approved for development but waiting for the current sprint to complete.

This is distinct from:
- **Parking Lot** (`02-Product/Parking-Lot.md`) — product ideas that need evaluation before becoming sprints
- **Engineering Backlog** (`Engineering-Backlog.md`) — technical debt, performance, and infrastructure work
- **Roadmap** (`09-Roadmap/Long-term-Roadmap.md`) — the multi-year platform evolution

---

## Priority Legend

| Priority | Meaning |
|---|---|
| 🔴 Critical | Production issue or blocking future work |
| 🟠 High | Significant merchant or product value |
| 🟡 Medium | Meaningful improvement, not urgent |
| 🟢 Low | Nice to have, no urgency |
| ⬜ Deferred | Planned but not yet prioritised |

---

## Phase 1 Backlog — Merchant Foundation

These sprints are required to complete Phase 1 of the OneMember roadmap.

### MVP-001 — Merchant Experience Polish

| Field | Value |
|---|---|
| **Priority** | 🟠 High |
| **Type** | Feature + Bug Fix |
| **Sprint File** | [MVP-001-Merchant-Experience-Polish.md](./MVP-001-Merchant-Experience-Polish.md) |
| **Status** | 🔲 Planning |
| **Depends On** | OMOS-1.1 complete |

Fix brand colour mismatch and LoyaltyProgram nullable JSON risk. Brand compliance and critical bug prevention identified in AI-03 audit.

---

### MVP-002 — Birthday and Expiry Automation

| Field | Value |
|---|---|
| **Priority** | 🟠 High |
| **Type** | Feature |
| **Sprint File** | TBD |
| **Status** | ⬜ Deferred |
| **Depends On** | MVP-001 complete |

Wire the `BirthdayReward` model and `expiration_type` settings to scheduled Artisan commands. Both are ~80% built. High merchant retention value.

**Key tasks:**
- `ProcessBirthdayRewards` Artisan command
- `ProcessPointExpiry` Artisan command
- Schedule both in `routes/console.php`
- Tests for both commands

---

### MVP-003 — Member Notification Emails

| Field | Value |
|---|---|
| **Priority** | 🟠 High |
| **Type** | Feature |
| **Sprint File** | TBD |
| **Status** | ⬜ Deferred |
| **Depends On** | MVP-002 complete |

Members currently receive zero emails. Add: points-earned notification, reward-available notification, birthday greeting. All via Events/Listeners (CTO-003).

---

### MVP-004 — Counter Mode UI

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Type** | Feature |
| **Sprint File** | TBD |
| **Status** | ⬜ Deferred |
| **Depends On** | MVP-001 complete |

The Counter Mode toggle exists but no staff-facing UI exists. Build the simplified sale-recording view for staff who need a quick "record purchase" interface without full merchant dashboard access.

---

### MVP-005 — CRUD Test Coverage

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Type** | Testing |
| **Sprint File** | TBD |
| **Status** | ⬜ Deferred |
| **Depends On** | None |

Write missing HTTP tests for: campaign create/update, member create/update, reward create/update, purchase recording, redemption flow, onboarding wizard.

---

### MVP-006 — Win-back Campaign Alerts

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Type** | Feature |
| **Sprint File** | TBD |
| **Status** | ⬜ Deferred |
| **Depends On** | MVP-003 complete |

Dashboard alert and merchant email when a member has not visited in a configurable number of days. Highest merchant retention value after birthday automation.

---

### MVP-007 — ADR-007 Naming Decision + LoyaltyProgram Alias

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Type** | Documentation + Architecture |
| **Sprint File** | TBD |
| **Status** | ⬜ Deferred |
| **Depends On** | None |

Document the LoyaltyProgram/Campaign naming split in ADR-007. Consider adding a `Campaign` type alias or accessor for developer ergonomics. Prevents future confusion as the codebase grows.

---

## Phase 2 Backlog — Customer Wallet (Future)

These sprints are not yet scheduled. They require Phase 1 exit criteria to be met first.

| Sprint | Description | Priority |
|---|---|---|
| PH2-001 | Customer Wallet — account creation and cross-merchant QR scan | 🟠 High |
| PH2-002 | LINE OA integration for member notifications | 🟠 High |
| PH2-003 | Enterprise Bridge API v1 | 🟡 Medium |
| PH2-004 | Tier-based loyalty (Bronze/Silver/Gold) | 🟡 Medium |

---

## Engineering Backlog

For technical debt, performance, and infrastructure sprints, see [../Engineering-Backlog.md](../Engineering-Backlog.md).

---

## Parking Lot (Product Ideas Under Evaluation)

For feature ideas not yet approved for development, see [../02-Product/Parking-Lot.md](../02-Product/Parking-Lot.md).
