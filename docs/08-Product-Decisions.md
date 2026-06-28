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

### [DECISION-023] Campaign Status Column — Four-State Model on loyalty_programs (Sprint 3.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.1 spec)
- **Status:** Approved
- **Decision:** Add a `status` varchar column (not null, default `draft`) to the `loyalty_programs` table via a new migration. Valid values: `draft`, `active`, `paused`. Archived campaigns are represented by a soft-deleted row (`deleted_at` not null) and are not stored as a separate status value. A new `App\Enums\CampaignStatus` enum (cases: Draft, Active, Paused) is added. The existing `is_active` boolean column is retained in the schema but is no longer the primary source of truth for campaign state; `status` is the authority from Sprint 3.1 onward.
- **Reason:** The existing `is_active` boolean is insufficient for the four states (Draft, Active, Paused, Archived) required by the Campaign Management UI. A dedicated status column provides explicit, readable state without over-engineering a separate status table.
- **Impact:** New migration `2026_06_28_000001_add_status_to_loyalty_programs_table.php`. New enum `app/Enums/CampaignStatus.php`. `app/Models/LoyaltyProgram.php` fillable and casts updated.

---

### [DECISION-024] Campaign UI Terminology — "Campaigns" Replaces "Loyalty Programs" in Navigation (Sprint 3.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.1 spec)
- **Status:** Approved
- **Decision:** The sidebar navigation item and all UI labels use the term "Campaigns". The underlying database table remains `loyalty_programs` and the Eloquent model remains `LoyaltyProgram`. The `/loyalty-programs` coming-soon route is removed and replaced with `/campaigns` routes. The sidebar link is updated to point to `campaigns.index`.
- **Reason:** The Product Owner's Sprint 3.1 spec consistently uses the term "Campaign" for this module. "Loyalty Programs" is retained only at the database and model layer for continuity with the existing schema.
- **Impact:** `routes/web.php` (remove loyalty-programs closure, add CampaignController routes), `resources/views/layouts/app.blade.php` (sidebar link update).

---

### [DECISION-025] Business Type Gate Deferred Past Sprint 3.1 (Sprint 3.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.1 spec)
- **Status:** Approved
- **Decision:** DECISION-018 states that business type selection must be implemented before campaign creation. The Product Owner has explicitly approved Sprint 3.1 (Campaign Management) without requiring the business type gate to be in place first. The business type gate will be enforced in a future sprint. Sprint 3.1 allows any authenticated merchant to create campaigns regardless of whether a business type has been set.
- **Reason:** The Product Owner prioritised Campaign Management for Sprint 3.1 with full awareness of DECISION-018. The gate is deferred, not cancelled.
- **Impact:** No business-type enforcement logic in Sprint 3.1. `docs/12-Merchant-User-Journey.md` Step 5 status remains "not yet implemented".

---

### [DECISION-026] Campaign Configuration — Settings JSON Keys (Sprint 3.2.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.2.1 spec)
- **Status:** Approved
- **Decision:** Campaign configuration is stored in the existing `settings` JSON column on `loyalty_programs`. No new columns are added. The following keys are used per campaign type:
  - **Points:** `spend_amount` (integer), `points_awarded` (integer), `expiration_enabled` (boolean), `expiration_duration` (integer or null), `expiration_unit` (string: "months" or "years"), `birthday_bonus_enabled` (boolean), `birthday_bonus_points` (integer or null).
  - **Stamps:** `stamps_required` (integer), `reward_description` (string).
  - These keys do not conflict with keys referenced in `docs/09-Loyalty-Business-Rules.md` Section 3. Keys from doc 09 (`min_purchase_amount`, `max_earn_per_transaction`, etc.) are reserved for the transaction engine sprint and are not used here.
- **Reason:** Sprint 3.2.1 is configuration-only with no loyalty calculations. The settings JSON column is the correct location for type-specific campaign configuration per the sprint spec instruction.
- **Impact:** `app/Http/Requests/ConfigureCampaignRequest.php` (new), `app/Http/Controllers/CampaignController.php` (new `configure` method), `routes/web.php` (new route), `resources/views/campaigns/show.blade.php` (Rules tab replaced with configuration form and live summary).

---

### [DECISION-027] Campaign Workspace UI Terminology — "Configuration" → "Rules" (Sprint 3.2.1 Change Request)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Change Request after Sprint 3.2.1)
- **Status:** Approved
- **Decision:** In the merchant-facing Campaign Workspace UI, the word "Configuration" is replaced with "Rules" wherever it appears. Affected labels: "Points Configuration" → "Points Rules", "Stamp Card Configuration" → "Stamp Card Rules", "Save Configuration" button → "Save Rules". Internal code names (`configure()`, `ConfigureCampaignRequest`, `campaigns.configure` route, `settings` JSON) are unchanged. The Campaign Summary card earn description is rewritten as natural prose: "Customers earn X point(s) for every Y [currency] spent." (Points) and "Customers receive 1 stamp for every qualifying purchase." (Stamps), replacing the two-row "Customers earn / Every" format.
- **Reason:** "Rules" is friendlier and more accessible than "Configuration" for small business owners who are the primary audience. The summary card natural language reads more clearly than two disconnected rows.
- **Impact:** `resources/views/campaigns/show.blade.php` (text-only UI changes). No route, controller, database, or architectural changes.

---

### [DECISION-028] Merchant-Facing Terminology Standard (Standing Rule)
- **Date:** 2026-06-28
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** All merchant-facing UI text must use business-friendly language that small business owners immediately understand. The following terminology is approved and mandatory:
  - **Rules** (not "Configuration")
  - **Campaign** (not "Loyalty Program")
  - **Members** (not "Users")
  - **Rewards** (not "Redemptions")
  - Natural language descriptions preferred. Examples: "Customers earn 1 point for every ฿100 spent." / "Customers receive 1 stamp for every qualifying purchase."
  - Developer terminology (controllers, requests, models, database columns, routes, enums, etc.) may continue using technical naming internally and is not subject to this rule.
- **Reason:** OneMember's target audience is small business owners, not developers. The merchant interface must feel approachable and immediately understood without any technical background.
- **Impact:** All future Blade view copy must comply. Any merchant-facing label, button, heading, placeholder, flash message, or help text that uses non-approved terminology must be corrected before shipping. Internal code naming is unaffected.

---

*New decisions must be appended above this line in the format shown.*
