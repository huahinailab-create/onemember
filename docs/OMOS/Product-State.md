# Product-State.md — Current State of OneMember

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
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
| **Application Version** | 0.4.2 |
| **OMOS Version** | 1.1 |
| **PHP Version** | 8.3+ (8.5 in production) |
| **Laravel Version** | 13.17.0 |
| **Last Code Commit** | `45619fc` — CurrentSprint.md hash update |
| **Last Code Sprint** | AI-03 Application Audit (`f8d6ac8`) |
| **Last OMOS Sprint** | OMOS-1.1 Operational Readiness (this sprint) |

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

**72 / 100** — as of AI-03 Audit (2026-07-02)

| Category | Score | Blocker |
|---|---|---|
| Core Functionality | 90/100 | None |
| Security | 85/100 | CSP unsafe-inline (documented, necessary) |
| Testing | 80/100 | Missing CRUD coverage for campaigns, members, rewards |
| Architecture | 85/100 | LoyaltyProgram/Campaign naming split |
| Brand Compliance | 50/100 | `--bs-primary` not overridden — Bootstrap blue shown everywhere |
| Localization | 75/100 | THB hardcoded in fallbacks |
| UX Completeness | 60/100 | 3 nav items lead to coming-soon pages |
| Technical Debt | 70/100 | LoyaltyProgram nullable JSON, MerchantProfileController legacy |
| Performance | 70/100 | Dashboard N+1 risk, no caching layer |
| Documentation | 95/100 | OMOS is comprehensive |

Next health score update: after MVP-001 completes.

---

## Current Sprint

| Field | Value |
|---|---|
| **Sprint ID** | OMOS-1.1 |
| **Title** | Operational Readiness |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint File** | [Sprints/](./Sprints/) |
| **Type** | Documentation / Governance |

---

## Next Sprint

| Field | Value |
|---|---|
| **Sprint ID** | MVP-001 |
| **Title** | Merchant Experience Polish |
| **Status** | 🔲 Planning |
| **Sprint File** | [Sprints/MVP-001-Merchant-Experience-Polish.md](./Sprints/MVP-001-Merchant-Experience-Polish.md) |
| **Priority** | High |
| **Depends On** | OMOS-1.1 complete and CTO approved |

---

## Last CTO Review

| Field | Value |
|---|---|
| **Sprint Reviewed** | AI-OMOS-BOOTSTRAP |
| **Review Date** | 2026-07-02 |
| **Verdict** | Approved (implied — PO sent new sprint) |
| **Notes** | Stop-and-wait governance introduced; "Continue OMOS" workflow established |

---

## Last Deployment

| Field | Value |
|---|---|
| **Deployment Date** | Not recorded — pre-OMOS |
| **Version Deployed** | 0.4.2 (estimated) |
| **Environment** | Production (Laravel Forge + DigitalOcean) |
| **Status** | ✅ Live |
| **Next Deployment** | After MVP-001 — pending PO approval |

---

## Current Risks

| ID | Risk | Severity | Status |
|---|---|---|---|
| R-001 | `LoyaltyProgram.settings` raises ErrorException on NULL | High | 🔲 Scheduled — MVP-001 |
| R-002 | Brand colours wrong (`#2563eb` shown instead of `#1A2E5A`) | Medium | 🔲 Scheduled — MVP-001 |
| R-003 | Birthday automation built but not running | Medium | 🔲 Scheduled — MVP-002 |
| R-004 | Point expiry configured but not processed | Medium | 🔲 Scheduled — MVP-002 |
| R-005 | 3 navigation items lead to coming-soon pages | Low | ⏳ Phase 1 known — merchants notified via UI |
| R-006 | Stripe webhook signature verification unconfirmed | Medium | 🔲 To be verified in MVP-001 or separately |
| R-007 | CSP uses `unsafe-inline` (XSS risk if injection occurs) | Low | 📋 Documented — architectural limitation of Alpine.js |

---

## Top Priorities

Based on the AI-03 audit and OMOS sprint backlog:

| # | Priority | Sprint | Description |
|---|---|---|---|
| 1 | 🔴 | MVP-001 | Fix `LoyaltyProgram.settings` null-safe pattern (bug risk) |
| 2 | 🟠 | MVP-001 | Fix brand colours — `--bs-primary` to `#1A2E5A` |
| 3 | 🟠 | MVP-002 | Wire birthday automation to scheduled command |
| 4 | 🟠 | MVP-002 | Wire point expiry to scheduled command |
| 5 | 🟠 | MVP-003 | Build member-facing email notifications |
| 6 | 🟡 | MVP-004 | Build Counter Mode UI |
| 7 | 🟡 | MVP-005 | Write missing CRUD test coverage |
| 8 | 🟡 | MVP-006 | Build win-back campaign dashboard alerts |

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
| 324 tests passing | ✅ | Full suite green |
| Brand colours correct | ❌ | MVP-001 required |
| LoyaltyProgram null-safe | ❌ | MVP-001 required |
| Member notification emails | ❌ | MVP-003 required |
| Birthday automation live | ❌ | MVP-002 required |

**Recommended production readiness gate:** MVP-001 and MVP-002 complete before merchant onboarding begins.
