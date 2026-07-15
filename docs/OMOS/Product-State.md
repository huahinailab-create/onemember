# Product-State.md — Current State of OneMember

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-15 |
| **Related Documents** | [CurrentSprint.md](./CurrentSprint.md), [Audits/AI-03-Application-Audit.md](./Audits/AI-03-Application-Audit.md), [Engineering-Backlog.md](./Engineering-Backlog.md), [09-Roadmap/Long-term-Roadmap.md](./09-Roadmap/Long-term-Roadmap.md) |

---

## Purpose

`Product-State.md` is the live snapshot of where OneMember is right now. It is updated at the end of every sprint by Claude Developer and reviewed by the AI CTO after every sprint review.

It answers: **"If someone needed to understand the current state of OneMember in 2 minutes, what would they read?"**

This file is read as part of the `Continue OMOS` initialisation sequence (step 2 in EXECUTE.md).

---

## Current Version

| Field | Value |
|---|---|
| **Application Version** | 0.9.1 |
| **OMOS Version** | 1.2 |
| **PHP Version** | 8.3+ (8.5 in production) |
| **Laravel Version** | 13.17.0 |
| **Active Unmerged Branch** | `customer-001b-saved-addresses` (stacked on `customer-001a-identity-foundation`, off main `20084eb`) — CUSTOMER-001A customer identity foundation (guard, OTP, profile; DECISION-100/ADR-016) + CUSTOMER-001B customer address book & checkout addresses (generic country-aware schema TH/MM, single-default invariant, snapshot-only merchant privacy; DECISION-101/ADR-017). 931 tests green. Both sprints reviewed together; NOT merged, NOT pushed. Separately, `website-002a-public-site` (WEBSITE-002A, DECISION-099) also awaits review. |
| **Last Code Commit** | audit(ux): MR-004 merchant readiness audit (`merchant-ready-001-mr-001`) — MERCHANT-READY-001 declared COMPLETE by CTO 2026-07-10. Later commits are docs-only strategy: INTERNATIONAL-001 blueprint (approved), GO-TO-MARKET-001 acquisition strategy (approved) in [Roadmaps/](./Roadmaps/), SALES-001 sales operating system (approved; 10 docs in [Sales/](./Sales/)), and WEBSITE-001 public-website master blueprint (13 docs in [Website/](./Website/)) — the final documentation assignment before pilot merchant acquisition |
| **Last Code Sprint** | **MERCHANT-READY-001 / MR-004 — Merchant Readiness Audit**: senior-QA/UX pass over the whole merchant experience (no features/logic/architecture/schema). Small safe fixes: last 11 raw-English flashes + limit errors localized to existing EN/TH `messages.*` keys, 4 accessibility label fixes (reward search button/input, earn-method select, media-upload native input), hardcoded alt localized, duplicate lang key removed. Verified clean: zero horizontal overflow at 375/768 across ~20 merchant pages, EN↔TH lang parity 100%, consistent localized page titles, no dead links/TODOs. Thailand READY; Myanmar partially ready (customer-side yes, merchant UI/Zawgyi/MMK decimals documented) — full findings in the sprint file. Before that on this branch (all CTO approved): **MR-003** guided launch journey, **MR-002** empty states + contextual help, **MR-001** Merchant Launch Dashboard. Earlier: `c698cea` integration merge. |
| **Last OMOS Sprint** | OMOS-1.2 Autonomous Sprint Governance (`b99b1f9`) |

---

## Current Phase

**Phase 1 — Merchant Foundation**

Phase 1 is the current active phase. The goal is to establish OneMember as the leading loyalty platform for SMEs in Thailand.

| Phase 1 Exit Criteria | Status |
|---|---|
| 1,000+ active paying merchants | ⏳ Not yet — platform is pre-launch |
| 50,000+ active members across the network | ⏳ Not yet |
| Monthly join rate growing > 10% MoM | ⏳ Not yet |
| Merchant 12-month retention > 60% | ⏳ Not yet |

Phase 2 (Customer Wallet) does not begin until Phase 1 exit criteria are met.

---

## Current Application Health Score

**93 / 100** — updated after RELEASE-1B (2026-07-03)

| Category | Score | Notes |
|---|---|---|
| Core Functionality | 90/100 | Unchanged |
| Security | 85/100 | CSP unsafe-inline (documented, necessary) |
| Testing | 88/100 | 380 tests passing |
| Architecture | 85/100 | LoyaltyProgram/Campaign naming split (ADR-007 pending) |
| Brand Compliance | 100/100 | ✅ RELEASE-1B — corporate website, email identity, all onemember.app → onemember.co |
| Localization | 88/100 | All new strings in EN + TH |
| UX Completeness | 82/100 | Premium SaaS design system deployed; 3 nav items still coming-soon |
| Technical Debt | 82/100 | ✅ navigation.blade.php Tailwind removed; MerchantProfileController legacy remains |
| Performance | 70/100 | Dashboard N+1 risk, no caching layer |
| Documentation | 98/100 | OMOS comprehensive + Pilot Readiness Checklist |

Next health score update: after next sprint completes.

---

## Current Sprint

| Field | Value |
|---|---|
| **Sprint ID** | MERCHANT-READY-001 |
| **Title** | Merchant Readiness (Help Center & Manual ✅ · MR-001 Merchant Launch Dashboard 🔄) |
| **Status** | 🔄 In Progress — MR-001 |
| **Sprint File** | [Sprints/MERCHANT-READY-001.md](./Sprints/MERCHANT-READY-001.md) |
| **Final Commit** | Help Center: `856a9e9`/`dd801dd` · MR-001: see git log |

---

## Next Sprint

| Field | Value |
|---|---|
| **Sprint ID** | TBD |
| **Title** | Awaiting CTO sprint selection |
| **Status** | ⬜ No approved sprint queued |
| **Sprint File** | [Sprints/Backlog.md](./Sprints/Backlog.md) |
| **Priority** | — |

---

## Last CTO Review

| Field | Value |
|---|---|
| **Sprint Reviewed** | MVP-004 — Birthday and Expiry Automation |
| **Review Date** | 2026-07-03 |
| **Verdict** | ✅ Approved |
| **Notes** | CTO approved MVP-004, reprioritised roadmap, activated MVP-005 (Pilot Merchant Readiness) in place of CRUD Test Coverage |

---

## Last Deployment

| Field | Value |
|---|---|
| **Deployment Date** | 2026-07-03 |
| **Version Deployed** | 0.9.0 — RELEASE-1A OneMember Product Identity |
| **Environment** | Production (Laravel Forge + DigitalOcean) |
| **Status** | ✅ Live |
| **Next Deployment** | TBD — awaiting next approved sprint |

---

## Current Risks

| ID | Risk | Severity | Status |
|---|---|---|---|
| R-001 | `LoyaltyProgram.settings` raises ErrorException on NULL | High | ✅ Fixed — MVP-001 (`37b7d8c`) |
| R-002 | Brand colours wrong (`#2563eb` shown instead of `#1A2E5A`) | Medium | ✅ Fixed — MVP-001 (`37b7d8c`) |
| R-003 | Birthday automation built but not running | Medium | ✅ Fixed — MVP-004 (`2c35ce6`) |
| R-004 | Point expiry configured but not processed | Medium | ✅ Fixed — MVP-004 (`2c35ce6`) |
| R-005 | 3 navigation items lead to coming-soon pages | Low | ⏳ Phase 1 known — merchants notified via UI |
| R-006 | Stripe webhook signature verification unconfirmed | Medium | 🔲 To be verified in MVP-001 or separately |
| R-007 | CSP uses `unsafe-inline` (XSS risk if injection occurs) | Low | 📋 Documented — architectural limitation of Alpine.js |

---

## Top Priorities

Based on current product state and OMOS sprint backlog (all P1 audit items complete):

| # | Priority | Sprint | Description |
|---|---|---|---|
| 1 | 🟠 | MVP-006 | Build member-facing email notifications (points earned, birthday, reward available) |
| 2 | 🟡 | MVP-007 | Build Counter Mode UI for staff sale recording |
| 3 | 🟡 | MVP-008 | Win-back campaign dashboard alerts |
| 4 | 🟡 | MVP-009 | CRUD test coverage (deferred from original backlog) |
| 5 | 🟡 | MVP-010 | ADR-007 — LoyaltyProgram/Campaign naming decision |

---

## Production Readiness

| Criterion | Status | Notes |
|---|---|---|
| Core loyalty features work | ✅ | Points, stamps, rewards, redemptions, members — all functional |
| Email verification required | ✅ | Cannot bypass (CEO-006) |
| Event-driven email | ✅ | All email via Events/Listeners (CTO-003) |
| Multi-tenant isolation | ✅ | `abort_unless()` on all resource access |
| DevTools gated | ✅ | Double gate: env + flag |
| Stripe billing integrated | ✅ | Subscription, checkout, webhooks |
| 380 tests passing | ✅ | Full suite green |
| Brand colours correct | ✅ | Fixed in MVP-001 (`37b7d8c`) |
| LoyaltyProgram null-safe | ✅ | Fixed in MVP-001 (`37b7d8c`) |
| Birthday automation live | ✅ | MVP-004 (`2c35ce6`) |
| Point expiry processing | ✅ | MVP-004 (`2c35ce6`) |
| Trial-ending reminder emails | ✅ | MVP-005 — non-Stripe trial path covered |
| Merchant acquisition flow | ✅ | MVP-003 — landing page, onboarding trial badge, finish confirmation |
| Pilot readiness checklist | ✅ | MVP-005 — `docs/OMOS/Pilot-Readiness-Checklist.md` |
| Member notification emails | ❌ | Not yet built — documented as known limitation for pilot |
| Counter Mode UI | ❌ | Not yet built — documented as known limitation for pilot |

**Recommended production readiness gate:** MVP-001 and MVP-002 complete before merchant onboarding begins.
