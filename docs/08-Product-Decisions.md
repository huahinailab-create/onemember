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

### [DECISION-029] UI Consistency Standard (Standing Rule)
- **Date:** 2026-06-28
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Every new page and UI component must follow the existing OneMember design system. Unless explicitly instructed otherwise:
  - Use the existing `x-app-layout` admin layout.
  - Reuse existing cards, buttons, badges, and form components as established in the Members and Campaign modules.
  - Do not introduce new colours beyond those already defined in `resources/css/app.css` and Bootstrap 5 defaults.
  - Do not introduce new spacing values beyond Bootstrap 5 utility classes and existing CSS custom properties.
  - Do not introduce new icons beyond Bootstrap Icons already in use.
  - Do not redesign existing pages.
  - Extend existing patterns instead of creating new UI patterns.
  - If a new UI pattern is genuinely required, STOP and ask the Product Owner before implementing it.
- **Reason:** Consistent UI reduces cognitive load for merchants, reduces maintenance cost, and ensures the product feels like a single coherent application rather than a collection of independent screens.
- **Impact:** All future Blade views. Claude must reference existing views (e.g. `members/index.blade.php`, `members/show.blade.php`, `campaigns/show.blade.php`) as the design baseline before writing any new view.

---

### [DECISION-030] Reward Types for MVP (Sprint 3.2.2)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.2.2 spec)
- **Status:** Approved
- **Decision:** The `RewardType` enum supports five values for MVP: `free_item`, `discount_percentage`, `discount_amount`, `voucher`, `custom`. This supersedes the types suggested in `docs/09-Loyalty-Business-Rules.md` Section 4.1 (`discount`, `free_item`, `gift`, `cashback`), which are deferred or replaced. The `type` column in the `rewards` table stores these string values.
- **Reason:** The sprint 3.2.2 spec defines the merchant-facing reward types. The doc 09 types were architectural placeholders and are updated here.
- **Impact:** New `app/Enums/RewardType.php`. `app/Models/Reward.php` cast updated.

---

### [DECISION-031] Reward Status — Draft/Active + Soft Delete for Archived (Sprint 3.2.2)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.2.2 spec)
- **Status:** Approved
- **Decision:** Rewards have two explicit statuses: `draft` and `active`, stored in a new `status` varchar column (default `draft`). Archived rewards use the existing `deleted_at` soft-delete column — no third status value. This pattern is consistent with Campaigns and Members. A new `App\Enums\RewardStatus` enum (Draft, Active) is added. The `is_active` boolean column remains in the schema but `status` is the authoritative field from this sprint onward. A new `internal_notes` text column (nullable) and making `points_required` nullable are required via a new migration.
- **Reason:** Stamp campaigns do not use `points_required`; it must be nullable. The `status` and `internal_notes` fields were not in the original migration. Soft delete is already supported by the schema.
- **Impact:** New migration `2026_06_28_000002_update_rewards_for_sprint_3_2_2.php`. New `app/Enums/RewardStatus.php`. `app/Models/Reward.php` updated.

---

### [DECISION-032] Earn Rules — Expiration UI Pattern and Birthday Valid-Days Fields (Sprint 3.2.3)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.2.3 spec)
- **Status:** Approved
- **Decision:**
  1. **Point Expiration UI** — The existing checkbox + Duration + Unit pattern (stored as `expiration_enabled`, `expiration_duration`, `expiration_unit`) is replaced with three radio options: *Never expire (Recommended)*, *Expire after N months*, *Expire after N years*. The settings JSON keys change to `expiration_type` (values: `never` | `months` | `years`) and `expiration_duration` (integer, null when `never`). The old keys `expiration_enabled` and `expiration_unit` are removed.
  2. **Birthday Bonus Valid-Days** — Two new settings keys are added: `birthday_valid_days_before` (integer ≥ 0, default 7) and `birthday_valid_days_after` (integer ≥ 0, default 7). The existing `birthday_bonus_enabled` and `birthday_bonus_points` keys are renamed to `birthday_enabled` and `birthday_points` for consistency.
  3. **Helper text** — Point Expiration section must include: "Customers are more likely to return when points never expire or have a long expiration period such as 2 years."
  4. These settings only apply to Points campaigns. Stamp campaigns retain `stamps_required` and `reward_description` and have no expiration or birthday configuration.
- **Reason:** Sprint 3.2.3 refines the Earn Rules UX. Radio buttons communicate the three mutually-exclusive expiration options more clearly than a checkbox+select combo. Birthday valid-days allow merchants to define the claim window around a member's birthday.
- **Impact:** `app/Http/Requests/ConfigureCampaignRequest.php`, `resources/views/campaigns/show.blade.php` (Alpine data keys, form fields, summary card getters). No new database migration — `settings` is a JSON column.

---

### [DECISION-033] Record Purchase Workflow (Sprint 3.3.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.3.1 spec)
- **Status:** Approved
- **Decision:**
  1. **One active campaign** — The system uses the merchant's single active campaign (`status = 'active'`, not soft-deleted). If multiple active campaigns exist the first one (by ID) is used. If none exists, the purchase is rejected with a user-visible error.
  2. **Points calculation** — `floor(purchase_amount / settings['spend_amount']) × settings['points_awarded']`. Integer result. Example: 550 THB ÷ 100 = 5.5 → floor = 5 → 5 × 1 = 5 pts.
  3. **Stamp calculation** — Always 1 stamp per qualifying purchase regardless of amount.
  4. **Member balance** — `total_points` is incremented for both campaign types. `lifetime_points` is incremented for Points campaigns only.
  5. **Transaction record** — Created in the existing `transactions` table with `type = 'earn'`. Two new columns are added: `purchase_amount` (decimal 10,2 nullable) and `invoice_number` (varchar 100 nullable).
  6. **Immutability** — Transactions have no `updated_at` column (existing design). No edit or delete routes are added.
  7. **UI placement** — "Record Purchase" card appears on the Member Workspace below the Profile/Loyalty row and above the tab card. Disabled with a message for archived or inactive members.
  8. **Validation rejections** — purchase_amount ≤ 0, no active campaign, archived member, inactive member, cross-merchant access.
- **Reason:** First earn-side feature. Sprint scope is purchase recording only — no redemption, no reporting, no notifications.
- **Impact:** New migration, new `PurchaseController`, new `RecordPurchaseRequest`, updated `Transaction` model, updated `MemberController::show`, updated `members/show.blade.php`, one new route.

---

### [DECISION-034] "Activity" as Merchant-Facing Label for Transactions (Sprint 3.3.2)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.3.2 spec)
- **Status:** Approved
- **Decision:** The merchant-facing UI displays the word **Activity** wherever the underlying `transactions` table records are shown. The database table, Eloquent model (`Transaction`), controller classes, routes, and PHP variables retain the word "Transaction" internally. Only UI copy (tab labels, headings, descriptions) uses "Activity."
- **Reason:** "Transactions" feels like accounting/developer terminology. "Activity" is more natural for small business merchants reviewing member engagement history.
- **Impact:** `resources/views/members/show.blade.php` — tab renamed from "Transactions" to "Activity". Internal code naming is unaffected.

---

### [DECISION-035] Reward Redemption Workflow (Sprint 3.3.3)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 3.3.3 spec)
- **Status:** Approved
- **Decision:**
  1. **UI entry point** — A "Redeem Reward" card is added to the Member Workspace (below Record Purchase, above tabs). A single button opens a Bootstrap modal listing only eligible rewards.
  2. **Eligible rewards** — Rewards must: belong to the member's active campaign, have `status = 'active'`, not be soft-deleted, and have remaining quantity (or be unlimited). For Points campaigns, the member must have `total_points >= reward.points_required`. For Stamp campaigns, the member must have `total_points >= settings['stamps_required']`.
  3. **Points deduction** — Points campaigns: deduct `reward.points_required`. Stamp campaigns: deduct `settings['stamps_required']` (full card). Partial stamp redemption is not allowed.
  4. **Redemption record** — Created in the existing `redemptions` table with `status = 'used'` and `redeemed_at = now()`. Status is set to `Used` immediately (no pending state) since the merchant fulfils the reward on the spot.
  5. **Transaction record** — Created in `transactions` with `type = 'redeem'` and `points = -(points_deducted)` (negative debit). This is the Activity entry shown in the Activity tab.
  6. **Quantity tracking** — `rewards.quantity_redeemed` is incremented by 1 after each successful redemption for limited-quantity rewards. Unlimited rewards (`quantity_available = NULL`) are not tracked.
  7. **Validation** — All eligibility checks re-run on submission (server-side guard). Rejections redirect back with an error message.
- **Reason:** Completes the MVP earn→redeem loyalty cycle. Immediate fulfilment model is simplest for small merchants.
- **Impact:** New `RedemptionController`, new `RedeemRewardRequest`, new route `POST /members/{member}/redemptions`, updated `MemberController::show()`, updated `members/show.blade.php`.

---

### [DECISION-036] Merchant Dashboard — Data Model and Query Strategy (Sprint 4.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 4.1 spec)
- **Status:** Approved
- **Decision:** The Merchant Dashboard is served by a new `DashboardController` replacing the existing route closure. It reads exclusively from existing tables — no new columns or migrations required. KPIs: (1) total active members = non-trashed `members` count, (2) active campaigns = `loyalty_programs` with `status = active` and `deleted_at = null`, (3) rewards redeemed today = `redemptions` where `redeemed_at` date = today, (4) points issued today = sum of `transactions.points` where `type = earn` and `created_at` date = today. Recent Activity = latest 10 transactions with `member` and `loyaltyProgram` (withTrashed) eager-loaded. Top Members = top 5 by `total_points` desc. Active Campaigns section = all active campaigns with non-deleted reward count via `withCount`. Empty states are shown per section when no data exists.
- **Reason:** Dashboard answers "What does the merchant need to know right now?" using data already collected by the earn/redeem cycle. No schema changes needed.
- **Impact:** New `app/Http/Controllers/DashboardController.php`. Updated `routes/web.php` (closure replaced with controller). Updated `resources/views/dashboard.blade.php`.

---

### [DECISION-037] Onboarding Wizard — Schema, State Tracking, and Layout (Sprint 4.2)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 4.2 spec)
- **Status:** Approved
- **Decision:**
  1. **New columns on `merchants`:** `business_type` (varchar nullable), `website` (varchar nullable), `onboarding_completed_at` (timestamp nullable). `onboarding_completed_at = null` means not yet complete; set to `now()` on Step 5 completion.
  2. **Current step tracking:** stored in the existing `settings` JSON column as `settings['onboarding_step']` (integer 2–5 as each step is completed). Avoids a new column for transient wizard state.
  3. **Loyalty type preference:** stored as `settings['onboarding_loyalty_type']` (`points` or `stamps`) during Step 4, consumed in Step 5 to create the starter campaign.
  4. **Date format preference:** stored as `settings['date_format']` (string: `DD/MM/YYYY`, `MM/DD/YYYY`, or `YYYY-MM-DD`). Default: `DD/MM/YYYY`.
  5. **Existing merchants:** migration seeds `onboarding_completed_at = NOW()` for all existing merchant records so they never see the wizard.
  6. **Wizard layout:** a dedicated `resources/views/layouts/wizard.blade.php` (no sidebar, centered card, OneMember branding header) is used for all wizard steps. This is explicitly required by the wizard sprint — the sidebar-less layout reduces distraction during first-time setup.
  7. **Skip for Now (Step 1 only):** sets `session('onboarding_skipped', true)` and redirects to dashboard. The wizard re-appears on next login if onboarding is not complete.
  8. **Dashboard guard:** `DashboardController::index()` redirects to `/onboarding` if `$merchant` is null OR `onboarding_completed_at` is null, unless `session('onboarding_skipped')` is true.
- **Reason:** Minimises schema changes (only 3 new columns), reuses the existing settings JSON for wizard-only state, and keeps the wizard experience clean by using a stripped-down layout without navigation.
- **Impact:** New migration, updated `Merchant` model, new `OnboardingController`, new form requests, new layout, 6 new step views, updated routes, updated `DashboardController`.

---

### [DECISION-038] Onboarding Starter Campaign Defaults (Sprint 4.2)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 4.2 spec)
- **Status:** Approved
- **Decision:** When the merchant selects "Yes – create a starter campaign" at Step 5, one campaign and one reward are created using the following defaults. No campaign is created if any campaign already exists (including archived).
  - **Points program:** Campaign name "Points Rewards Program", status `active`, settings: `spend_amount=100`, `points_awarded=1`, `expiration_type=never`. One reward: name "Free Item", type `custom`, `points_required=500`, status `active`, unlimited quantity.
  - **Stamps program:** Campaign name "Stamp Card", status `active`, settings: `stamps_required=10`, `reward_description="Complete your stamp card to claim your reward."`. One reward: name "Free Item", type `free_item`, status `active`, unlimited quantity, `points_required=null`.
- **Reason:** Sensible defaults give the merchant a working campaign immediately. Guarding against existing campaigns prevents duplicate data if onboarding is resumed.
- **Impact:** `OnboardingController::createStarterCampaign()` private method.

---

### [DECISION-039] Settings Module — Schema, Tabs, and Route Strategy (Sprint 5.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.1 spec)
- **Status:** Approved
- **Decision:**
  1. **New structured address columns on `merchants`:** `address_line_1`, `address_line_2`, `city`, `state`, `postal_code`, `country` (all varchar nullable). The existing `address` text column is retained for historical data but the new Settings form uses the structured fields. A `notes` text column (nullable) is also added.
  2. **`password_changed_at` timestamp on `users`:** Nullable. Set by `SettingsController` whenever the merchant changes their password. Displayed as "Last Password Change" in the Security tab. Null displays as "Never changed."
  3. **Settings module replaces Merchant Profile page:** A new `SettingsController` and `resources/views/settings/index.blade.php` with 4 Bootstrap tab panes (Business Profile, Business Preferences, Account, Security). The sidebar "Merchant Profile" item is renamed to "Settings" and links to `settings.index`. The old `GET /merchant/profile` route redirects to `/settings` for backward compatibility. The old `PUT /merchant/profile` route is retained to avoid 404s.
  4. **Business Preferences stored in settings JSON:** `date_format` (already from DECISION-037), `default_expiration_type`, `default_expiration_duration`, `default_birthday_enabled` are merchant-level preference defaults stored in `merchants.settings`. These do NOT retroactively modify existing campaigns.
  5. **Account tab — subscription display:** Professional Trial with days remaining computed as `$user->created_at->addDays(30)`. Read-only. No billing logic.
  6. **Routes:** `GET /settings`, `PUT /settings/profile`, `PUT /settings/preferences`, `POST /settings/password`.
- **Reason:** Centralises merchant configuration into a single tabbed workspace. Structured address fields replace the freeform textarea for better data quality. Separate routes per tab allow per-form validation errors to return to the correct tab.
- **Impact:** 2 new migrations, updated `Merchant` and `User` models, new `SettingsController`, 3 new Form Requests, new view, updated sidebar, updated routes.

---

*New decisions must be appended above this line in the format shown.*
