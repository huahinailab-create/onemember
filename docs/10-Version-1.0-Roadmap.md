# 10 — Version 1.0 Roadmap

> **Last updated:** 2026-06-28  
> **Version target:** 1.0 (Commercial Launch)  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md)

---

## Overview

OneMember Version 1.0 is a multi-tenant SaaS loyalty platform designed for independent merchants. It enables merchants to create loyalty campaigns, manage members, issue points or stamps, and redeem rewards — all through a self-service web interface.

---

## Sprint History & Completion Status

| Sprint | Title | Status | Key Deliverables |
|--------|-------|--------|-----------------|
| Sprint 0 | Foundation & Rules | ✅ Complete | Laravel 13 scaffold, PROJECT_RULES.md, git init, Bootstrap 5 + Vite 8, SQLite dev config |
| Sprint 2 | Authentication & Core UI | ✅ Complete | Laravel Breeze auth, admin sidebar layout, Members CRUD (list/add/view/edit/archive), Merchant Profile stub |
| Sprint 3.1 | Campaign Management | ✅ Complete | Campaign list, create, show, edit, pause, archive; PHP enums; SoftDeletes |
| Sprint 3.2 | Campaign Configuration & Rewards | ✅ Complete | Points and Stamps campaign rules configuration; Reward create/edit/archive; earn rules engine; `settings` JSON column |
| Sprint 3.3 | Earn, Redeem & Maintenance | ✅ Complete | Record Purchase flow (PurchaseController), Member Activity page, Reward Redemption (immediate), Transaction ledger (immutable), obsolete model method removal |
| Sprint 4.1 | Merchant Dashboard | ✅ Complete | KPI cards, Quick Actions, Recent Activity, Top Members, Active Campaigns; real data queries; empty states |
| Sprint 4.2 | Onboarding Wizard | ✅ Complete | 6-step wizard (Welcome → Business Info → Settings → Loyalty Preference → Quick Start → Finish); wizard layout; starter campaign auto-creation; `onboarding_completed_at` tracking |
| Sprint 5.1 | Settings Module | ✅ Complete | 4-tab Settings page (Business Profile, Preferences, Account, Security); password change with audit; legacy redirect |

---

## Feature Completion Matrix

### Core Member Management

| Feature | Status | Notes |
|---------|--------|-------|
| Member registration (merchant-side) | ✅ Complete | Name, email, phone, DOB, notes |
| Member profile view | ✅ Complete | Full activity history, point balance, stamp progress |
| Member edit | ✅ Complete | All fields editable |
| Member archive (soft delete) | ✅ Complete | `SoftDeletes`, resolveRouteBinding with `withTrashed` |
| Member search / filter | ⬜ Planned | Sprint TBD |
| Member QR code / barcode lookup | ⬜ Planned | Quick lookup at POS — Sprint TBD |
| Bulk member import (CSV) | 🔮 Future | Version 1.x |
| Birthday reward auto-delivery | ⬜ Planned | Requires scheduler + BirthdayReward model (already seeded); Sprint TBD |
| Manual point adjustment | ⬜ Planned | Merchant override for points correction; Sprint TBD |

### Campaign Management

| Feature | Status | Notes |
|---------|--------|-------|
| Points campaign (create/edit/configure/archive) | ✅ Complete | Spend-based earn rules via `settings` JSON |
| Stamps campaign (create/edit/configure/archive) | ✅ Complete | Configurable stamps_required |
| Campaign pause / resume | ✅ Complete | `CampaignStatus` enum: Active, Paused, Archived |
| Campaign status badges | ✅ Complete | Bootstrap badges on all campaign views |
| Business type gating (campaign selection) | ⬜ Planned | DECISION-025 deferred; business_type captured in onboarding |
| Multiple active campaigns per merchant | ✅ Complete | No limit enforced |
| Campaign expiry dates | 🔮 Future | `starts_at` / `ends_at` columns exist; gate not enforced |

### Reward Management

| Feature | Status | Notes |
|---------|--------|-------|
| Reward create / edit / archive | ✅ Complete | Per-campaign rewards with RewardType enum |
| Reward redemption (immediate) | ✅ Complete | DECISION-035: status=Used, redeemed_at=now() |
| Reward quantity limits | ✅ Complete | `quantity_available`, `quantity_redeemed`, `remainingQuantity()` |
| Reward point cost | ✅ Complete | `points_required` on Reward model |
| Reward expiry | ⬜ Planned | Preferences captured; enforcement Sprint TBD |
| Reward catalogue (merchant-facing) | ⬜ Planned | Global rewards view (sidebar "Rewards" shows coming-soon) |
| Birthday rewards (delivery trigger) | ⬜ Planned | BirthdayReward model and BirthdayRewardType enum exist; delivery logic Sprint TBD |

### Merchant Onboarding

| Feature | Status | Notes |
|---------|--------|-------|
| 6-step onboarding wizard | ✅ Complete | DECISION-037 |
| Skip-for-now option | ✅ Complete | Session-based `onboarding_skipped` flag |
| Starter campaign auto-creation | ✅ Complete | DECISION-038: defaults for Points and Stamps types |
| Wizard progress persistence | ✅ Complete | `settings['onboarding_step']` tracks current step |
| Existing merchant backward compat | ✅ Complete | Migration seeds `onboarding_completed_at` for existing records |

### Settings & Profile

| Feature | Status | Notes |
|---------|--------|-------|
| Business Profile (4-tab Settings) | ✅ Complete | DECISION-039 |
| Business Preferences (currency, timezone, date format, expiry, birthday) | ✅ Complete | Stored in `merchants.settings` JSON |
| Account info tab (read-only) | ✅ Complete | Trial days remaining, plan, account dates |
| Password change with audit | ✅ Complete | `password_changed_at` timestamp on users |
| Email change | ⬜ Planned | Requires verification flow; Sprint TBD |
| Logo / branding upload | ⬜ Planned | `logo_path` column exists; file upload Sprint TBD |
| Two-factor authentication | 🔮 Future | Version 1.x |

### Dashboard & Reporting

| Feature | Status | Notes |
|---------|--------|-------|
| KPI dashboard (4 cards) | ✅ Complete | Active Members, Active Campaigns, Rewards Redeemed Today, Points Issued Today |
| Quick Actions panel | ✅ Complete | Add Member, Record Purchase, Redeem Reward, Create Campaign |
| Recent Activity feed | ✅ Complete | Last 10 transactions with eager-loaded member/program |
| Top Members list | ✅ Complete | Top 5 by total_points |
| Active Campaigns table | ✅ Complete | With reward count |
| Reports module | ⬜ Planned | Sidebar link shows "coming soon" |
| Analytics charts | ⬜ Planned | Sprint TBD — DECISION-036 deferred charts |
| Export (CSV/PDF) | 🔮 Future | Version 1.x |

### Billing & Subscriptions

| Feature | Status | Notes |
|---------|--------|-------|
| Trial period tracking | ✅ Complete | `$user->created_at->addDays(30)`, displayed on Account tab |
| Subscription plans | ⬜ Planned | Stripe integration Sprint TBD |
| Plan limits enforcement | ⬜ Planned | Member cap, campaign cap per plan |
| Billing portal | ⬜ Planned | Sprint TBD |
| Payment method management | ⬜ Planned | Sprint TBD |

### Notifications

| Feature | Status | Notes |
|---------|--------|-------|
| Transactional email (welcome, redemption) | ⬜ Planned | Laravel Mail + Mailgun/SES; Sprint TBD |
| Birthday reward email trigger | ⬜ Planned | Requires scheduler |
| SMS notifications | 🔮 Future | Version 1.x |
| In-app notifications | 🔮 Future | Version 1.x |

### Multi-User / Staff

| Feature | Status | Notes |
|---------|--------|-------|
| Staff accounts (per merchant) | ⬜ Planned | Sprint TBD |
| Role-based access (owner/staff) | ⬜ Planned | Sprint TBD |
| Audit log | ✅ Complete | `AuditLog` model exists (creation/edit events) |

---

## V1.0 Milestone Plan

### Milestone 1 — Core Complete ✅
*All foundational CRUD and the earn/redeem cycle.*

Delivered: Sprints 0, 2, 3.1, 3.2, 3.3

### Milestone 2 — Merchant Experience ✅
*Self-service onboarding, dashboard, and settings.*

Delivered: Sprints 4.1, 4.2, 5.1

### Milestone 3 — Product Hardening ⬜ In Progress
*Fill gaps before commercial launch.*

Remaining:
- Member search & filter
- Reward catalogue view (global)
- Birthday rewards delivery
- Manual point adjustments
- Email notifications (transactional)
- Business type campaign gate (DECISION-025)
- Logo upload
- Email change flow

### Milestone 4 — Commercial Readiness ⬜ Planned
*Billing, staff accounts, and launch infrastructure.*

- Stripe subscription integration
- Plan limits enforcement
- Staff accounts + roles
- Reports module (basic)
- Security audit
- Performance review
- Production deployment (MySQL, Redis, queue worker)

---

## Status Legend

| Symbol | Meaning |
|--------|---------|
| ✅ Complete | Built, tested, committed |
| ⬜ Planned | Required for V1.0; sprint not yet assigned |
| 🔮 Future | Post-V1.0 (see docs/14-Version-2.0-Ideas.md) |
