# Roadmap

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [02-Product/Product-Bible.md](../02-Product/Product-Bible.md), [00-Executive/Vision.md](../00-Executive/Vision.md), [00-Executive/North-Star-Metric.md](../00-Executive/North-Star-Metric.md), [roadmap/01-Development-Phases.md](../../../roadmap/01-Development-Phases.md) |

---

## Purpose

This document is the authoritative product roadmap for OneMember. It translates the long-term vision into phased, prioritised delivery milestones that the development team and Product Owner can act on.

The roadmap is not a fixed schedule. It is a prioritised sequence of outcomes. Timelines are estimates; priority order is firm.

---

## Relationship to Other Documents

- **[Version-2.0-Vision-and-Master-Roadmap-2026-2030.md](./Version-2.0-Vision-and-Master-Roadmap-2026-2030.md)** — the strategic 5-year blueprint (VISION-001, 2026-07-05). This operational roadmap covers near-term sprints; the Master Roadmap covers phases, milestones, and strategy through 2030.

- `roadmap/` folder — High-level phase descriptions and the master vision narrative (created in Sprint AI-01). Those documents inform this one.
- This document (`docs/OMOS/09-Roadmap/Roadmap.md`) — The detailed, maintained roadmap that is updated after every sprint.
- `backlog/` — The raw backlog from which roadmap items are drawn.

When these documents conflict, this document takes precedence as the more recently maintained version.

---

> **2026-07-05:** PH2-000 design package approved as Phase 2 foundation; implementation specs PH2-001A–F and SCALE-001 are written and gated on decisions BD-01…BD-18 (see [Bible-Gap-Review](../02-Product/Bible-Gap-Review-2026-07.md) §4 and [Customer-Wallet package](../02-Product/Customer-Wallet/README.md)). Scale readiness: [Scalability-Review-2026-07](../10-Architecture/Scalability-Review-2026-07.md).

## Current Phase: Phase 1 — Merchant Foundation

**Goal:** Every small business in Thailand can run a professional loyalty programme.

### Completed ✅

| Feature | Sprint | Commit |
|---|---|---|
| Merchant registration and onboarding | — | — |
| Email verification (production-safe queue) | — | `d30e09f` |
| Loyalty programme (points + stamps) | — | — |
| Member management | — | — |
| Reward creation and redemption | — | — |
| Customer self-service portal + QR | — | — |
| Stripe subscription billing | — | — |
| Dashboard with metrics | — | — |
| Settings (profile, preferences, branding) | — | — |
| CSV import and data export | — | — |
| Developer tools suite | DEV-01, DEV-02 | `962a82f` |
| Merchant Intelligence (AI health score) | Sprint 6.7 | `73e1af2` |
| BUG: Email verification stale-tab fix | BUG-001 | `a26e761` |
| BUG: Dashboard broken links | BUG-002 | `056495f` |
| Birthday bonus + automated notification | MVP-004 / MVP-006 | `5d44d3d` |
| Point expiry (scheduled) | MVP-004 | `2c35ce6` |
| Member notifications (points, rewards, birthday) | MVP-006 | `5d44d3d` |
| Staff-facing sale entry (Counter Mode UI) | MVP-007 | `d6b34fb` |
| Win-back campaign alerts | MVP-008 | `91c19f2` |
| Campaign analytics dashboard | RELEASE-4A | — |

### In Progress 🔄

| Feature | Sprint | Target |
|---|---|---|
| OMOS foundation | AI-02A | Current |

### Planned — Phase 1 Remaining ⬜

| Feature | Priority | Notes |
|---|---|---|
| Receipt QR purchase linking | Medium | Scan receipt to claim points |
| PromptPay integration | Medium | Thailand-first payment |
| POS-Lite | Medium | Staff-facing sale entry |
| Merchant-defined point value | Low | Points = monetary discount |

---

## Phase 2 — Customer Wallet

> Begins after Phase 1 is stable in production with 50+ active merchants.

| Feature | Priority | Notes |
|---|---|---|
| Customer account (separate from merchant) | High | Prerequisite for wallet |
| Universal QR scan → join | High | Core wallet value |
| Consent management per merchant | High | Legal requirement |
| Wallet dashboard (all brands) | High | Core wallet UX |
| Enterprise bridge API | Medium | Large brand integration |
| White-label mode | Low | Enterprise client upsell |
| Privacy analytics (anonymised) | Medium | Consent-gated |

---

## Phase 3 — Regional Commerce Network

> Begins after Phase 2 wallet reaches 5,000+ active users.

| Feature | Priority | Notes |
|---|---|---|
| Product/menu listing | High | Commerce foundation |
| Pickup and delivery orders | High | Direct commerce |
| PromptPay checkout (order-triggered) | High | Payment-points link |
| Malaysia expansion | Medium | Malay, DuitNow |
| Vietnam expansion | Medium | Vietnamese, VNPay |
| Singapore expansion | Low | PayNow |
| Native iOS + Android app | High | After regional traction |

---

## Non-Roadmap Items

These have been explicitly decided as out of scope for the foreseeable future:

| Item | Reason |
|---|---|
| Social features (reviews, followers) | Not a social platform |
| Advertising network | We do not resell customer data |
| Financial services (loans, credit) | Outside core competency, regulatory risk |
| Cryptocurrency payments | Not relevant to target market today |

---

## Roadmap Update Protocol

This document is updated:
- After every sprint completion (add to Completed section)
- When the Product Owner changes a priority
- When a new Phase 1 Remaining item is added from the backlog

All roadmap changes must be approved by the Product Owner. Major phase changes (e.g., starting Phase 2) require an explicit decision recorded in `docs/OMOS/12-ADR/`.

---

## Full Content

> **This document is a placeholder for Sprint AI-02A.**
> The detailed roadmap with timelines, milestones, and success criteria will be written in Sprint AI-02B.
