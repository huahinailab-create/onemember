# 08 — Product Decisions

This file is the authoritative record of all approved architectural and business decisions for the OneMember project.

Every entry must be recorded here **before** implementation begins.

No decision may be assumed, invented, or implemented without a corresponding entry in this log.

---

## Decision Log Format

```
## [DECISION-XXX] Short Title
- **Date:** YYYY-MM-DD
- **Requested by:** Product Owner / CTO
- **Status:** Approved
- **Decision:** What was decided.
- **Reason:** Why this decision was made.
- **Impact:** Files, tables, or systems affected.
```

---

## Approved Decisions

### [DECISION-001] Laravel 13 as Application Framework
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** Use Laravel 13 with PHP 8.3+ as the backend framework.
- **Reason:** Modern LTS-track release with strong ecosystem, Eloquent ORM, and built-in queue/event/mail support appropriate for a SaaS platform.
- **Impact:** Entire application stack.

---

### [DECISION-002] Bootstrap 5 as Frontend Framework
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** Use Bootstrap 5.3 and Bootstrap Icons as the sole frontend UI framework. Tailwind CSS was removed.
- **Reason:** Team familiarity, desktop-first responsive grid, no build step required for prototyping, avoids JavaScript framework complexity at MVP stage.
- **Impact:** `resources/css/app.css`, `resources/js/app.js`, all Blade templates.

---

### [DECISION-003] SQLite for Local Development / MySQL for Production
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** SQLite is used for local development. MySQL 8+ is the target for staging and production.
- **Reason:** Zero-config local setup. MySQL provides production-grade performance and full-text indexing when needed.
- **Impact:** `.env`, `config/database.php`, migration compatibility (no MySQL-specific syntax in migrations).

---

### [DECISION-004] Core Domain: Merchants, Members, Loyalty Programs, Rewards, Transactions, Redemptions, Birthday Rewards, Audit Logs
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** The eight tables above constitute the MVP data model. No additional tables are to be added without a new decision entry.
- **Reason:** Covers the complete loyalty SaaS lifecycle: merchant onboarding → member enrolment → point earning → reward redemption → audit trail.
- **Impact:** All migrations in `database/migrations/2026_06_27_*`.

---

### [DECISION-005] Desktop-First Responsive Design
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** The admin/merchant web application is designed desktop-first. Mobile breakpoints are supported via Bootstrap's grid but are not the primary target.
- **Reason:** The merchant-facing dashboard is primarily used on desktop browsers. Mobile-first is reserved for a future member-facing app.
- **Impact:** All Blade layouts and component markup.

---

### [DECISION-006] Sidebar + Topbar Application Layout
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** The authenticated layout uses a fixed left sidebar (260px, dark) and a sticky topbar (60px). Guest pages use a centred card layout.
- **Reason:** Standard SaaS dashboard pattern. Provides consistent navigation without page reloads.
- **Impact:** `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`.

---

### [DECISION-007] Laravel Breeze (Blade + Alpine.js) as Authentication System
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Install `laravel/breeze` using the Blade + Alpine.js stack as the permanent authentication system. No React, Vue, Livewire, or Inertia.
- **Reason:** Standard Laravel auth scaffolding. Blade keeps the stack consistent with the rest of the application. Alpine.js provides lightweight interactivity without a full JS framework.
- **Impact:** Adds auth routes (login, register, password reset, email verification), auth views in `resources/views/auth/`, and a `dashboard` route. Publishes to `routes/auth.php`.

---

### [DECISION-008] Merchant Profile Fields (Sprint 2)
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** The merchant profile page captures: Business Name, Contact Person, Business Email, Mobile Number, Business Address, Business Logo (placeholder only), Currency (default THB), Time Zone (default Asia/Bangkok).
- **Reason:** Core merchant identity data required before any loyalty program setup.
- **Impact:** New migration to add `contact_person` and `timezone` columns to `merchants` table (other fields already exist). New controller, form request, and view.

---

### [DECISION-009] OneMember Brand Color Palette (Admin Shell)
- **Date:** 2026-06-27
- **Requested by:** Product Owner (Sprint 2 Task 2.2)
- **Status:** Approved
- **Decision:** The admin shell uses the color palette already established in Task 2.1: sidebar background `#1e293b` (dark slate), sidebar hover `#334155`, nav text `#94a3b8`, Bootstrap default primary blue (`#0d6efd`) for accents. Body background `#f8f9fa`. No new brand palette is introduced until the Product Owner specifies one.
- **Reason:** Consistency with already-approved and committed layout. Avoids inventing brand colors without instruction.
- **Impact:** `resources/css/app.css`, all Blade layouts.

---

### [DECISION-010] Collapsible Sidebar Implementation
- **Date:** 2026-06-27
- **Requested by:** Product Owner (Sprint 2 Task 2.2)
- **Status:** Approved
- **Decision:** The sidebar collapses to `width: 0` (hidden) on toggle using Alpine.js `x-data`/`:class` with a CSS `transition`. A hamburger button (`bi-list` icon) in the topbar triggers the toggle. No icon-only collapsed state — full hide/show only.
- **Reason:** KISS. Full icon-only sidebar requires additional complexity not requested in the sprint.
- **Impact:** `resources/views/layouts/app.blade.php`, `resources/css/app.css`.

---

### [DECISION-011] Members Table — Nickname Column (Sprint 2 Task 2.3)
- **Date:** 2026-06-27
- **Requested by:** Product Owner (Sprint 2 Task 2.3 spec)
- **Status:** Approved
- **Decision:** Add a nullable `nickname` column (varchar 100) to the `members` table. Display it in the Members List page between Full Name and Mobile Number.
- **Reason:** Required column in the Members List page specification. Not present in the original migration.
- **Impact:** New migration `2026_06_27_200001_add_nickname_to_members_table`, `app/Models/Member.php` fillable array.

---

### [DECISION-012] Add Member Form — Dedicated Page + Notes Column (Sprint 2 Task 2.4)
- **Date:** 2026-06-27
- **Requested by:** Product Owner (Sprint 2 Task 2.4 spec)
- **Status:** Approved
- **Decision:** The Add Member feature uses a dedicated `/members/create` page (not a modal). A nullable `notes` text column is added to the `members` table to support the optional Notes field. Phone uniqueness is validated per merchant (scoped to `merchant_id`). After a successful save the user is redirected to `/members` with a Bootstrap success flash.
- **Reason:** Six fields plus validation error display fits a full page better than a modal. Notes is a new field required by the task spec not present in the original migration.
- **Impact:** New migration `2026_06_27_200002_add_notes_to_members_table`, `app/Models/Member.php` fillable, new `StoreMemberRequest`, new `create`/`store` controller actions, new view `resources/views/members/create.blade.php`, updated `members/index.blade.php` Add Member button, new routes.

---

### [DECISION-013] Plan Limits Deferred Until After Beta Testing
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Plan limits (member caps, transaction limits, feature gates per tier) are intentionally deferred. They will be determined before commercial launch, after beta testing and feedback from real merchants.
- **Reason:** Defining limits before real usage data is available risks restricting legitimate use or under-monetising the product.
- **Impact:** No plan enforcement logic may be built until limits are approved and recorded here. `docs/11-Pricing-Strategy.md` updated.

---

### [DECISION-014] Pricing — Thailand First, Amounts TBD Before Launch
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Thailand is the initial market for OneMember. All pricing will be validated against the Thai market first. Specific pricing amounts (in THB) will be finalised before commercial launch after market validation. No pricing amounts are defined at this time.
- **Reason:** Market validation before price commitment reduces commercial risk.
- **Impact:** `docs/11-Pricing-Strategy.md` updated. No billing code should reference specific amounts.

---

### [DECISION-015] Billing — Monthly Only at Version 1.0
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Version 1.0 supports monthly billing only. Annual billing will be introduced in a later release.
- **Reason:** Simplifies billing infrastructure for MVP. Annual billing adds complexity (proration, refunds) that is not required at launch.
- **Impact:** `docs/11-Pricing-Strategy.md` updated. Billing implementation must not include annual cycle logic at V1.0.

---

### [DECISION-016] Payment Gateway — Selection Deferred to Billing Implementation Phase
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** The payment gateway provider has not been selected. Selection will occur during the subscription billing implementation phase. No provider is recommended or committed to at this time.
- **Reason:** Provider selection requires evaluation of fees, regional coverage, and integration complexity closer to the billing sprint.
- **Impact:** `docs/11-Pricing-Strategy.md` updated. No payment gateway packages or SDKs may be installed until a provider is chosen.

---

### [DECISION-017] Plan Limit Enforcement Principle
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** The guiding principle for plan limit enforcement is: "The merchant experience should never be interrupted unexpectedly while serving customers." The exact enforcement behaviour (soft warning, grace period, auto-upgrade prompt, or other) will be decided before billing implementation. No enforcement behaviour may be implemented without explicit approval.
- **Reason:** Interrupting a merchant mid-transaction damages trust and the brand promise of simplicity.
- **Impact:** `docs/11-Pricing-Strategy.md` updated. Enforcement design is deferred.

---

### [DECISION-018] Business Type — Required Before First Campaign, Does Not Restrict Functionality
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Business Type selection is required before a merchant can create their first loyalty campaign. It does not restrict any platform functionality. Its purpose is better onboarding, relevant campaign templates, and future analytics segmentation.
- **Reason:** Gating campaign creation on business type selection ensures merchants receive relevant templates and improves onboarding quality.
- **Impact:** `docs/12-Merchant-User-Journey.md` Step 5 updated. Business type UI must be built before Loyalty Program creation (Sprint 3). Onboarding flow must enforce this gate.

---

### [DECISION-019] Industry Templates — Optional Starter Templates After Business Type Selection
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** After selecting a business type, the merchant is offered optional starter templates (campaign types and example rewards) drawn from `docs/13-Industry-Strategy.md`. The merchant may use a template as-is, modify it, or skip templates entirely and create manually. Templates are never mandatory.
- **Reason:** Templates reduce time-to-first-program for new merchants without removing flexibility for experienced users.
- **Impact:** `docs/12-Merchant-User-Journey.md` Step 5 updated. Template content should be sourced from `docs/13-Industry-Strategy.md`. Template implementation is planned alongside business type selection.

---

### [DECISION-020] Pet-Specific Fields — Not Part of Version 1.0
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Members represent people only in Version 1.0. Pet-specific data fields (pet name, pet birthday, pet type) are not part of Version 1.0 and will not be added to the members table or any related schema. Pet-specific functionality may be evaluated in a future version.
- **Reason:** Keeps the member schema simple and focused. Pet features add complexity that is not validated by the core loyalty use case.
- **Impact:** `docs/13-Industry-Strategy.md` updated to remove the pet birthday field suggestion. No schema changes required.

---

### [DECISION-021] Hardware — No Proprietary Hardware Required
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** OneMember must not require any proprietary hardware. The MVP must be fully functional on desktop computers, laptops, tablets, and mobile browsers. No dedicated terminal, scanner, or device is required to use any core feature.
- **Reason:** Proprietary hardware increases cost, friction, and support complexity. It conflicts with the brand promise of simplicity and the target market of small businesses.
- **Impact:** All features must be designed for standard browser-based access. Any feature that would require dedicated hardware must be flagged for Product Owner review before design begins.

---

### [DECISION-022] Connectivity — Internet Required, No Offline Mode in MVP
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** An internet connection is required to use OneMember. Offline mode is not part of the MVP.
- **Reason:** Offline support adds significant architectural complexity (local data storage, sync, conflict resolution) that is out of scope for MVP. The target market (physical retail in Thailand) has reliable mobile internet access.
- **Impact:** No offline caching, service workers, or sync logic should be implemented. All operations require a live server connection.

---

*New decisions must be appended above this line in the format shown.*
