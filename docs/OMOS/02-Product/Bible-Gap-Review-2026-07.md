# Product Bible & Roadmap Gap Review (SCALE-000, item 7)

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Review |
| **Last Updated** | 2026-07-05 |
| **Author** | Claude Fable 5 |
| **Related Documents** | [Product-Bible.md](./Product-Bible.md), [Glossary.md](./Glossary.md), [Roadmap.md](../09-Roadmap/Roadmap.md) |

Every finding is a **report, not a change** — resolutions belong to the Product Owner / CTO.

---

## 1. Missing Specifications (placeholder docs still empty)

| Doc | State | Needed by |
|---|---|---|
| `Merchant-Platform.md` | Placeholder since AI-02A | Overdue — the live product has no authoritative feature spec; new-developer onboarding relies on code archaeology |
| `Analytics.md` | Placeholder | Year-1 rollups (Scalability B-09) need the metric definitions this doc should own |
| `Enterprise-Bridge.md` | Placeholder | PH2-003 cannot be specced from one paragraph |
| `Commerce.md`, `POS.md`, `Inventory.md`, `Accounting.md`, `Procurement.md`, `AI-Features.md` | Placeholders | Phase 3/4 — acceptable to defer, but decide *when* they get written |
| Product Bible §"Sections to be completed" | Explicitly lists per-module specs, edge cases, cross-module rules, accessibility/localisation requirements — none written | The Bible itself says these are outstanding |

## 2. Documentation vs Reality Inconsistencies (fix the docs, not the code)

| # | Inconsistency | Detail |
|---|---|---|
| I-01 | **Glossary schema names are wrong.** Glossary: Balance "stored on members as `points_balance`", Transactions in "`loyalty_transactions`". Actual schema: `members.total_points`, table `transactions`. | Update Glossary (doc-only fix) |
| I-02 | **Plan tier names differ.** Glossary example tiers "Starter, Growth, Professional, Enterprise"; code enum: free/starter/professional/enterprise (no Growth, has Free). | Align Glossary with `SubscriptionPlan` enum |
| I-03 | **Trial length.** Emails/lang say "14-day free trial"; corporate site sells "30-Day Free Trial" (MerchantAcquisitionTest asserts it). Which is it? | **Business decision — BD-14** |
| I-04 | **"One active Campaign at a time"** (Glossary) is not enforced by code (multiple active LoyaltyPrograms possible; PurchaseController just picks the oldest active). Phase 2+ multi-campaign is roadmapped, but today's silent "oldest wins" rule is undocumented behaviour. | Decide: enforce single-active, or document oldest-wins — **BD-15** |
| I-05 | **Roadmap.md still lists Phase-1-remaining items that are done** (fixed for delivered sprints, but "Receipt QR", "PromptPay", "POS-Lite", "Merchant-defined point value" carry no owner/trigger). | Assign each a decision or parking-lot entry |
| I-06 | Roadmap.md header still says "Current Phase: Phase 1" while PH2-000 is approved as foundation. | Update after BD-01 is formally recorded |
| I-07 | `CurrentSprint.md` "Business Objective" fields drifted from sprint titles historically (RELEASE-3A block carried a 2B objective). Fixed for PH2-000; add a checklist item to the sprint-close protocol. | Process note |

## 3. Ambiguous Requirements Worth Resolving Now

| # | Ambiguity | Why now |
|---|---|---|
| A-01 | **Stamps semantics on redemption**: Glossary says stamp redemption "resets their Stamp count", code deducts `stamps_required` (partial balances persist). | Affects wallet card display (PH2-001B shows stamp progress) — **BD-16** |
| A-02 | **Point value in currency** ("Merchant-defined point value", roadmap Low): wallet UI will surface points prominently; if points ever gain monetary meaning, consent + accounting change. Decide direction before wallet copy is written. | **BD-17** |
| A-03 | **Staff identity**: Glossary promises "Future: Staff accounts with role-based permissions"; Counter Mode currently shares the merchant login. Wallet counter-scan (PH2-001F) logs `created_by = merchant user` — audit trails blur. | Decide staff-accounts timing — **BD-18** |
| A-04 | **North-Star metric ownership of wallet targets**: Functional spec (PH2-000 Doc 01 §6) proposes wallet success metrics; nobody has ratified them. | Ratify with BD-01 |
| A-05 | **Phase 1 exit criteria vs reality**: Long-term-Roadmap requires 1,000 merchants; Roadmap.md Phase 2 gate says 50. The two documents disagree. | Reconcile — part of **BD-01** |

## 4. Future Decisions Better Resolved Now (new BD entries)

Consolidated register (BD-01…BD-10 from PH2-000 remain open):

| ID | Decision | Suggested owner |
|---|---|---|
| BD-11 | Wallet directory search/geo scope for Phase 2.0 vs 2.1 (drives search strategy §1.6) | PO |
| BD-12 | RTO/RPO targets for production (proposal: RPO ≤ 15 min, RTO ≤ 4 h) | CEO |
| BD-13 | Production hosting + vendor set (cloud, Redis, object storage, error tracking, log sink) — budget | CEO |
| BD-14 | Trial length: 14 vs 30 days (I-03) | CEO |
| BD-15 | Single-active-campaign enforcement vs documented multi (I-04) | PO + CTO |
| BD-16 | Stamp redemption semantics (A-01) | PO |
| BD-17 | Points-to-currency direction (A-02) | CEO |
| BD-18 | Staff accounts timing (A-03) | PO |

## 5. Recommended Documentation Sprints (no code)

1. **DOC-001 — Merchant Platform Spec**: write `Merchant-Platform.md` from the live product (highest onboarding value, zero risk).
2. **DOC-002 — Glossary & Roadmap Reconciliation**: fix I-01/I-02/I-05/I-06 after BD-14/15 land.
3. **DOC-003 — Analytics Metric Definitions**: prerequisite for Year-1 rollup tables.
