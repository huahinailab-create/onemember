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

### [DECISION-040] Subscription Architecture — Enums, Config, and Merchant Fields (Sprint 5.2.1)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.2.1 spec)
- **Status:** Approved
- **Decision:**
  1. **`SubscriptionPlan` enum** (PHP 8.1 backed string enum) — cases: `Free`, `Starter`, `Professional`, `Enterprise`. Values match `config/subscriptions.php` plan keys.
  2. **`SubscriptionStatus` enum** (PHP 8.1 backed string enum) — cases: `Trial`, `Active`, `Expired`, `Cancelled`.
  3. **`config/subscriptions.php`** — single source of truth for all plan names, descriptions, and feature flags. No prices stored (DECISION-014). No limit values set (DECISION-013 — limits deferred to after beta). All feature flags default to `false`; explicitly enabled per plan.
  4. **Three new columns on `merchants`:** `subscription_plan` (varchar 50, default `professional`), `subscription_status` (varchar 50, default `trial`), `trial_ends_at` (timestamp nullable). Cast to their respective enums and `datetime` in the Merchant model.
  5. **Merchant creating hook** — when a new `Merchant` record is created, the `booted()` hook automatically sets `subscription_plan = Professional`, `subscription_status = Trial`, `trial_ends_at = now() + 30 days` unless already set. Configuration values come from `config/subscriptions.php`.
  6. **Merchant helper methods** — `isOnTrial()`, `trialDaysRemaining()`, `currentPlan()`, `subscriptionStatus()`, `canUseFeature(string $feature)`, `isEnterprise()`. `canUseFeature()` uses Professional-plan feature flags during an active trial.
  7. **No payment integration** — this sprint is architecture only. No Stripe, no checkout, no webhooks, no payment tables.
  8. **`SettingsController` updated** — Account tab now reads `trial_ends_at` and `trialDaysRemaining()` from the `Merchant` model when available, instead of computing from `$user->created_at`.
- **Reason:** Establishes the subscription foundation without payment complexity. All plan behaviour is driven by config, not hardcoded logic. Future billing sprints add Stripe integration on top of this layer.
- **Impact:** New `app/Enums/SubscriptionPlan.php`, new `app/Enums/SubscriptionStatus.php`, new `config/subscriptions.php`, new migration `2026_06_28_300001_add_subscription_fields_to_merchants_table.php`, updated `app/Models/Merchant.php`, updated `app/Http/Controllers/SettingsController.php`.

---

### [DECISION-041] Usage Limits & Upgrade Experience (Sprint 5.2.2)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.2.2 spec)
- **Status:** Approved
- **Decision:**
  1. **`config/subscriptions.php` extended** — each plan now contains a `limits` section with four keys: `members`, `campaigns`, `rewards_per_campaign`, `staff_users`. Values are integers (capped) or `null` (unlimited). Free and Starter have placeholder integers clearly commented as TBD per DECISION-013. Professional and Enterprise have `null` for all limits.
  2. **`app/Services/SubscriptionService`** — central class for all plan enforcement. No controller or view may contain subscription logic. Methods: `effectivePlanKey`, `featureLimit`, `usageCount`, `rewardUsageCount`, `isUnlimited`, `remaining`, `rewardRemaining`, `usagePercentage`, `rewardUsagePercentage`, `warningLevel`, `rewardWarningLevel`, `canCreateMember`, `canCreateCampaign`, `canCreateReward`, `usageSummary`.
  3. **Warning levels** — `normal` (<80%), `warning` (80–99%), `limit_reached` (≥100%). Thresholds driven by `config/subscriptions.php` `warning_threshold` and `limit_reached_threshold` keys.
  4. **Blade component `subscription-limit-warning`** — anonymous component in `resources/views/components/`. Accepts `level`, `feature`, `percentage`, `used`, `limit`. Renders yellow warning (Bootstrap `alert-warning`) for the warning state, blue informational card (`alert-info`) with disabled "Upgrade Plan" button for limit-reached state. Silent (no output) for normal state.
  5. **Controller enforcement** — `MemberController::store()`, `CampaignController::store()`, `RewardController::store()` check `canCreate*()` before creating. If limit reached, redirect back with a user-friendly `errors['limit']` message. Create views (`members/create`, `campaigns/create`, `rewards/create`) show the warning component above the form.
  6. **Dashboard Subscription card** — shows effective plan name, trial badge + days remaining (if applicable), members and campaigns progress bars with colour-coded warning states, disabled Upgrade Plan button.
  7. **Settings Account tab updated** — shows current plan badge, subscription status badge, trial end date with days remaining, compact usage progress bars for members and campaigns, disabled Upgrade Plan button.
  8. **No payment integration** — the Upgrade Plan button is disabled on all surfaces. Billing is a future sprint.
  9. **Unlimited plans never blocked** — `null` limit = `canCreate*()` always returns `true`. Professional and Enterprise merchants are never blocked.
- **Reason:** Builds the usage enforcement layer on top of Sprint 5.2.1's subscription foundation. All limit values come from config, not code, so they can be updated by the Product Owner without code changes after beta testing.
- **Impact:** Updated `config/subscriptions.php`, new `app/Services/SubscriptionService.php`, new `resources/views/components/subscription-limit-warning.blade.php`, updated `MemberController`, `CampaignController`, `RewardController`, `DashboardController`, `resources/views/dashboard.blade.php`, `resources/views/settings/index.blade.php`, `resources/views/members/create.blade.php`, `resources/views/campaigns/create.blade.php`, `resources/views/rewards/create.blade.php`.

---

### [DECISION-042] Trial Lifecycle — Expiry, Downgrade, and Countdown Banners (Sprint 5.2.3)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.2.3 spec)
- **Status:** Approved
- **Decision:**
  1. **`ProcessExpiredTrials` Artisan command** (`subscriptions:process-expired-trials`) — finds merchants with `subscription_status = trial` and `trial_ends_at <= now()`, sets `subscription_status = expired` and `subscription_plan = free`. Never deletes any data. Logs count via Laravel Log. Not yet scheduled.
  2. **Immediate enforcement without waiting for the command** — `SubscriptionService::effectivePlanKey()` checks `trial_ends_at.isPast()` even when `subscription_status` is still `trial`. Free plan limits are enforced the moment the trial expires, not on the next command run.
  3. **`Merchant::isTrialExpired()`** — new helper method. Returns true if `subscription_status = expired` OR if `subscription_status = trial` and `trial_ends_at.isPast()`.
  4. **`trial-banner` Blade component** — anonymous component in `resources/views/components/`. Logic: expired = non-dismissible grey `alert-secondary`; ≤1 day = red `alert-danger`; ≤3 days = yellow `alert-warning`; ≤7 days = yellow `alert-warning`; ≤14 days = blue `alert-info`; >14 days = no output. Dismissible banners (countdown states) use Alpine.js + `sessionStorage` key `trial_banner_dismissed` — dismissed for the browser session only. Expired banner is not dismissible. All banners include a disabled "Upgrade Plan" button.
  5. **Dashboard** — `<x-trial-banner :merchant="...">` included between page header and Section 1 KPI cards.
  6. **Settings Account tab** — trial end date now shows "Trial ended" badge when expired; plan label shows "(Free plan limits apply)" when expired; "Professional features active" when on active trial.
  7. **Data preservation guarantee** — the downgrade command only updates two columns (`subscription_status`, `subscription_plan`). Members, campaigns, rewards, and transactions are never touched.
- **Reason:** Completes the trial lifecycle without requiring payment integration. Merchants experience a graceful expiry with clear messaging and immediate Free-plan enforcement.
- **Impact:** New `app/Console/Commands/ProcessExpiredTrials.php`, new `resources/views/components/trial-banner.blade.php`, updated `app/Models/Merchant.php` (`isTrialExpired()`), updated `app/Services/SubscriptionService.php` (`effectivePlanKey()`), updated `resources/views/dashboard.blade.php`, updated `resources/views/settings/index.blade.php`.

---

### [DECISION-043] Subscription Centre — Dedicated Page (Sprint 5.2.4)
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.2.4 spec)
- **Status:** Approved
- **Decision:**
  1. **`SubscriptionController`** — new controller with a single `index()` action. Injects `SubscriptionService`. Passes merchant, usage summary, effective plan key, and full plan config array to the view. No form submissions in this sprint — no `SubscriptionRequest` required.
  2. **Route** — `GET /subscription` → `SubscriptionController@index`, named `subscription.index`. Protected by `auth` middleware.
  3. **`resources/views/subscription/index.blade.php`** — dedicated Subscription Centre page showing: current plan status card (plan, status, trial end date, days remaining), usage summary with progress bars, plan comparison table (all four plans from `config/subscriptions.php`), feature comparison grid. All plan names and descriptions read from config — nothing hardcoded. Current effective plan is highlighted in the comparison table. Upgrade buttons are disabled placeholders labelled "Coming Soon". Enterprise contact button is a disabled placeholder.
  4. **Settings Account tab simplified** — the full subscription block (plan badge, usage bars, upgrade button, "coming soon" text) is replaced with a concise two-row summary (Current Plan + Subscription Status) and a single "Manage Subscription" button linking to `subscription.index`. No subscription logic remains in the Settings view.
  5. **Sidebar** — "Subscription" nav link added under the Account section above Settings, pointing to `subscription.index`. Active when route matches `subscription.*`.
  6. **No payment integration** — this sprint is display-only.
- **Reason:** Centralises all subscription information in one place. Prevents duplication between Settings and subscription display. Gives merchants a clear view of their plan, usage, and available upgrades.
- **Impact:** New `app/Http/Controllers/SubscriptionController.php`, new `resources/views/subscription/index.blade.php`, updated `routes/web.php`, updated `resources/views/layouts/app.blade.php` (sidebar), updated `resources/views/settings/index.blade.php` (Account tab).

---

### [DECISION-044] Authentication Hardening — Sprint 5.4.1
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.4.1 spec)
- **Status:** Approved
- **Decision:**
  1. **Password policy** — `Password::defaults()` configured globally in `AppServiceProvider::boot()` to require: minimum 12 characters, mixed case (upper + lower), at least one number, at least one symbol. This single configuration propagates to all three password validation points: registration (`RegisteredUserController`), password reset (`NewPasswordController`), and password change (`Auth\PasswordController`).
  2. **Email verification enforced** — `User` model implements `MustVerifyEmail`. The `verified` middleware is added to all authenticated route groups (main group and onboarding group). After registration, the user is redirected to `/dashboard`, which re-directs to the email verification notice page until verified.
  3. **Password confirmation for sensitive actions** — the account deletion route (`DELETE /profile`) is protected by the built-in `password.confirm` middleware. Laravel's `ConfirmablePasswordController` handles the confirmation flow. Confirmation stays valid for 3 hours (Laravel default).
  4. **`password_changed_at` tracking** — implemented via `User::booted()` model observer (Sprint 5.4.1 refactor commit). Fires on any `updating` event where the `password` attribute is dirty. Applies to all three password change paths (change, reset, admin).
  5. **Session regeneration** — already implemented in `AuthenticatedSessionController::store()` via `$request->session()->regenerate()` (Breeze default).
  6. **Login throttling** — already implemented in `LoginRequest::ensureIsNotRateLimited()` (Breeze default): 5 attempts per unique email+IP combination, then locked with countdown.
  7. **Remember Me** — already implemented: checkbox in `auth.login` view, `$this->boolean('remember')` passed to `Auth::attempt()` in `LoginRequest::authenticate()` (Breeze default).
- **Reason:** Hardens authentication for V1.0 production readiness. All seven controls use Laravel's built-in mechanisms with zero custom auth code.
- **Impact:** `app/Providers/AppServiceProvider.php` (password policy), `app/Models/User.php` (MustVerifyEmail), `routes/web.php` (verified middleware + password.confirm), `docs/08-Product-Decisions.md`.

---

### [DECISION-045] Authorization Strategy — Sprint 5.4.2
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.4.2 spec)
- **Status:** Approved
- **Decision:**
  1. **Tenant isolation pattern** — all merchant-owned resources (Members, LoyaltyPrograms, Rewards, Transactions, Redemptions) are isolated by comparing `resource->merchant_id` to `$request->user()->merchant?->id` via `abort_unless(...)`. This pattern is applied consistently in every controller action that reads or mutates a resource.
  2. **No Laravel Policies introduced** — audit confirmed the `abort_unless` pattern already provides correct, non-duplicated isolation across all controllers. Adding Policies would add abstraction without improving correctness. Decision: retain the existing inline `abort_unless` checks.
  3. **Route model binding** — `resolveRouteBinding` on `Member`, `LoyaltyProgram`, and `Reward` models uses `withTrashed()->where(id)->firstOrFail()` (global scope). Tenant checks are performed at the controller level after binding. This is safe because no data is returned to the caller before the tenant check fires.
  4. **Indirect cross-tenant access** — verified: PurchaseController scopes the campaign lookup to `merchant_id`. RedemptionController scopes the reward lookup to `merchant_id`. No indirect paths expose cross-tenant data.
  5. **Feature tests added** — `tests/Feature/TenantIsolationTest.php` proves Merchant A cannot view, edit, or delete Merchant B's Members, Campaigns, or Rewards; cannot record purchases for Merchant B's members; cannot redeem Merchant B's rewards. 13 tests, all passing.
- **Reason:** Multi-tenant SaaS requires hard isolation. Using `abort_unless` at the controller level is the simplest correct approach for the current codebase complexity. Policies are reserved for when authorization logic diverges between roles.
- **Impact:** No application code changed. New: `tests/Feature/TenantIsolationTest.php`.

---

### [DECISION-046] Security Headers & Browser Protection — Sprint 5.4.3
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.4.3 spec)
- **Status:** Approved
- **Decision:**
  1. **Centralised middleware** — `App\Http\Middleware\SecurityHeaders` applies all security response headers. Registered globally on the `web` middleware stack in `bootstrap/app.php`.
  2. **Headers applied:**
     - `X-Frame-Options: SAMEORIGIN` — prevents clickjacking
     - `X-Content-Type-Options: nosniff` — prevents MIME-type sniffing
     - `Referrer-Policy: strict-origin-when-cross-origin` — limits referrer leakage across origins
     - `Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()` — disables dangerous browser APIs not used by the application
     - `Content-Security-Policy` — see below
     - `Strict-Transport-Security: max-age=31536000; includeSubDomains` — applied only when `$request->isSecure()` is true (HTTPS), so local dev is unaffected
  3. **CSP policy rationale:**
     - `script-src 'self' 'unsafe-inline'` — `'unsafe-inline'` required: application has inline `<script>` blocks in `settings/index`, `members/show`, `campaigns/show`, `onboarding/quick-start`, `onboarding/loyalty-preference`, and `onclick` event handlers in navigation. Removing these would require view redesign, which is out of scope.
     - `style-src 'self' 'unsafe-inline' https://fonts.bunny.net` — `'unsafe-inline'` required: 117 `style=""` attributes across templates. `fonts.bunny.net` required for Figtree font.
     - `font-src 'self' https://fonts.bunny.net` — Figtree font served from Bunny CDN.
     - `img-src 'self' data:` — allows `data:` URIs for inline images.
     - `connect-src 'self'` — restricts XHR/fetch to same origin only.
     - `frame-src 'none'`, `object-src 'none'` — no frames or plugins used.
     - `base-uri 'self'`, `form-action 'self'` — hardens against base tag injection and form hijacking.
  4. **Session cookies reviewed** — existing `config/session.php` already has: `http_only=true`, `same_site='lax'`. The `secure` flag reads from `SESSION_SECURE_COOKIE` env var (default false for local dev). Production must set `SESSION_SECURE_COOKIE=true`. `.env.example` updated to document these three variables explicitly.
  5. **No nonce-based CSP** — implementing nonces would require modifying every Blade layout and inline script, which violates the "no view redesign" constraint. Accepted trade-off: `'unsafe-inline'` is weaker than nonces but still meaningfully restricts script/style origins.
- **Reason:** Adds defence-in-depth browser protection without changing any application logic or views. All headers applied with a single middleware class.
- **Impact:** New `app/Http/Middleware/SecurityHeaders.php`, updated `bootstrap/app.php`, updated `.env.example`, new `tests/Feature/SecurityHeadersTest.php`.

---

### [DECISION-047] Security Logging Architecture — Sprint 5.4.4
- **Date:** 2026-06-28
- **Requested by:** Product Owner (Sprint 5.4.4 spec)
- **Status:** Approved
- **Decision:**
  1. **`SecurityLogger` service** (`app/Services/SecurityLogger.php`) — the single entry point for all security event logging. Each event type is a typed public method (e.g., `loginSucceeded()`, `passwordChanged()`). All writes go through a private `write()` method that assembles the structured payload and calls `Log::channel('security')`. Controllers and observers call the service by resolving it from the container.
  2. **`SecurityEventSubscriber`** (`app/Listeners/SecurityEventSubscriber.php`) — a Laravel event subscriber that maps built-in Laravel auth events to `SecurityLogger` methods. Auto-discovered by Laravel 11's event discovery (no manual registration required). Covers: `Login`, `Failed`, `Logout`, `PasswordResetLinkSent`, `PasswordReset`, `Verified`, `Registered`.
  3. **Dedicated `security` log channel** (`config/logging.php`) — `daily` driver writing to `storage/logs/security.log`, retained for 90 days, level `info`. Separate from the application log so security events can be forwarded independently to a SIEM in future.
  4. **Log structure** — every entry includes `event`, `user_id`, `merchant_id`, `email`, `ip_address`, `user_agent`. The `timestamp` is added by Monolog. Context-only events (trial expiration) include `context` with business-relevant data instead of user fields. Fields are omitted rather than null-filled when not available for the event type.
  5. **Sensitive data exclusions** — passwords, tokens, session IDs, cookies, API keys, and reset tokens are never passed to `SecurityLogger`. The service only receives IDs, emails, and status strings. This is enforced by the service API design (no method accepts a password or token parameter).
  6. **Password changed hook** — added to `User::booted()` observer (`static::updated`) alongside the existing `password_changed_at` tracking. Fires after the model is saved, not before, so it logs only when the DB write succeeds.
  7. **Merchant onboarding completed** — logged from `OnboardingController::storeQuickStart()` after `onboarding_completed_at` is persisted, before redirect. Single line, keeps controller thin.
  8. **Trial expiration** — `ProcessExpiredTrials` command now calls `trialExpired()`, `subscriptionStatusChanged()`, and `subscriptionPlanChanged()` per merchant. The generic `Log::info('ProcessExpiredTrials completed.')` call is removed; the per-merchant security log entries replace it.
  9. **How to add future events** — (a) add a typed public method to `SecurityLogger`; (b) if it maps to a built-in Laravel event, add a handler method and `$events->listen()` call to `SecurityEventSubscriber`; (c) if it's a custom trigger, call `app(SecurityLogger::class)->myEvent(...)` at the call site.
- **Reason:** Centralised, structured security logging is a prerequisite for production observability. All events go through one service, making it easy to change the log destination (e.g., add a SIEM or alerting channel) without modifying business code.
- **Impact:** New: `app/Services/SecurityLogger.php`, `app/Listeners/SecurityEventSubscriber.php`, `tests/Feature/SecurityLoggingTest.php`. Modified: `config/logging.php` (security channel), `app/Models/User.php` (password changed hook), `app/Http/Controllers/OnboardingController.php` (onboarding log), `app/Console/Commands/ProcessExpiredTrials.php` (trial expiration log).

---

### [DECISION-048] Production Security Review — Sprint 5.4.5
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.4.5 spec)
- **Status:** Approved
- **Decision:**
  1. **Full security audit completed** — all categories audited: Laravel config, PHP runtime, sessions/cookies, HTTPS/HSTS, authentication, authorisation, security headers, XSS/injection, CSRF, secrets management, dependencies, logging, scheduling, queues. See `docs/16-Production-Security-Review.md` for complete findings.
  2. **Security score: 93/100** — application is production-ready from a security perspective. All critical controls are in place. Remaining gaps are deployment-time configuration requirements, not code deficiencies.
  3. **One code fix applied** — `ProcessExpiredTrials` command was not registered in the scheduler. Added `Schedule::command(ProcessExpiredTrials::class)->dailyAt('01:00')` to `routes/console.php`. This is a blocking deployment requirement (trial expiration would never run otherwise).
  4. **Dependency audit: clean** — `composer audit` and `npm audit` both returned 0 vulnerabilities.
  5. **No secrets committed** — `.env` confirmed in `.gitignore` and not in git history. All secret fields empty in `.env.example`.
  6. **Accepted risk: `'unsafe-inline'` in CSP** — already documented in DECISION-046. Not changed in this sprint.
  7. **Warnings documented** — W-001 (TrustProxies), W-002 (registration rate limiting), W-003 (session encryption), W-004 (CSP unsafe-inline), W-005 (TrustHosts). None are blocking for V1.0; all are addressed by env config or deferred to post-launch hardening.
  8. **Required production settings** — documented in `docs/16-Production-Security-Review.md` including all mandatory `.env` values, scheduler crontab entry, queue worker Supervisor config, storage permissions, and php.ini recommendations.
- **Reason:** Pre-launch security audit is required by the Launch Checklist (section 2, OWASP Top 10 review). Complete audit ensures no security regressions have been introduced across the 5.4.x sprint series.
- **Impact:** New: `docs/16-Production-Security-Review.md`. Modified: `routes/console.php` (ProcessExpiredTrials scheduled).

---

### [DECISION-049] Production Deployment Infrastructure — Sprint 5.5.1
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.5.1 spec)
- **Status:** Approved
- **Decision:**
  1. **Provider-agnostic deployment** — all deployment documentation, scripts, and configuration are written for a generic Linux server (Ubuntu 22.04/24.04 LTS). No cloud-provider-specific code (no AWS-specific configs, no GCP/Azure SDKs, no platform.sh or Forge-specific files) is introduced. The application deploys equally to any VPS, dedicated server, or managed Linux host.
  2. **JSON health endpoint at `GET /up`** — replaced Laravel's built-in HTML `/up` page (from `withRouting(health:...)`) with a custom `HealthController` returning structured JSON: `{status, app, environment, timestamp, version}`. Rationale: (a) uptime monitors expect machine-readable responses; (b) HTML responses are fragile to parse; (c) the custom controller is 10 lines and testable. The endpoint is unauthenticated and intentionally returns no sensitive data (no DB credentials, no APP_KEY, no internal paths).
  3. **`APP_VERSION` config key** — added `'version' => env('APP_VERSION', '1.0')` to `config/app.php`. Version is surfaced in the health endpoint only. It is set via `.env` so deployments can inject the git tag or release number without code changes. Default is `'1.0'`.
  4. **Deployment guide scope** — `docs/17-Production-Deployment-Guide.md` covers: server requirements, PHP extensions, production environment variables, first-time and update deployment steps, database migrations, storage link, cache commands, queue setup (Supervisor), scheduler (crontab), HTTPS/Nginx configuration, SSL (Let's Encrypt/Certbot), file permissions, database backup strategy, monitoring recommendations, rollback procedure, post-deployment checklist, and common troubleshooting. The guide is the single source of truth for operating OneMember V1.0 in production.
  5. **No new business features** — this sprint added only infrastructure and operational tooling. All business logic, authentication flows, subscription logic, and UI remain unchanged.
- **Reason:** Production readiness requires both runnable infrastructure and documented operational procedures. A health endpoint is the minimum viability requirement for uptime monitoring. Provider-agnostic documentation preserves optionality for hosting decisions.
- **Impact:** New: `app/Http/Controllers/HealthController.php`, `tests/Feature/HealthCheckTest.php`, `docs/17-Production-Deployment-Guide.md`. Modified: `config/app.php` (APP_VERSION), `bootstrap/app.php` (removed built-in health route), `routes/web.php` (added `/up` route), `.env.example` (APP_VERSION, session security keys, production comments).

---

### [DECISION-050] Operational Readiness Philosophy — Sprint 5.5.2
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.5.2 spec)
- **Status:** Approved
- **Decision:**
  1. **Operations runbook over automated tooling** — For V1.0 with a small operations team, a comprehensive runbook (`docs/18-Operations-Runbook.md`) is preferred over automated monitoring dashboards or alert integrations. The runbook documents all procedures that a single operator can execute. Automated tooling (uptime monitors, error tracking, log aggregation) is documented as recommendations but not integrated into the codebase — keeping the application code free of vendor-specific monitoring dependencies.
  2. **Vendor-neutral monitoring strategy** — monitoring tool recommendations are provided for each category (uptime, errors, logs, metrics, scheduler, SSL, queue, disk) without prescribing a specific vendor. This preserves optionality for hosting and tooling choices. All monitoring is based on standard interfaces: the `/up` JSON endpoint for uptime, standard log files for log aggregation, standard Supervisor for process monitoring.
  3. **Health endpoint is the single uptime signal** — `GET /up` (DECISION-049) is the canonical target for all external uptime monitors. It is unauthenticated, returns structured JSON, exposes no sensitive data, and responds 200 even during `php artisan down` maintenance mode (it is excluded from the maintenance middleware). No alternative health routes are added.
  4. **Security log retention: 90 days** — `storage/logs/security.log` is retained for 90 days on the production server using Laravel's `daily` driver. Logs older than 90 days must be archived to off-site storage before local deletion. This satisfies basic audit trail requirements without prescribing a SIEM or external log service. Application logs (`laravel.log`) retain 14 days by default (configurable via `LOG_DAILY_DAYS`).
  5. **Incident severity model: P1–P4** — incidents are classified into four severity levels (P1 Critical, P2 High, P3 Medium, P4 Low) with defined response times and escalation paths. This model is documented in the runbook and does not require any tooling changes.
  6. **No new business features** — this sprint added only operational documentation and procedures. All business logic, authentication flows, subscription logic, and UI are unchanged.
- **Reason:** An operations team (even a team of one) needs documented procedures before going live. Documented runbooks reduce mean time to recovery (MTTR) during incidents by removing the need to derive procedures under pressure. A vendor-neutral approach avoids lock-in and keeps infrastructure costs predictable.
- **Impact:** New: `docs/18-Operations-Runbook.md`. Modified: `docs/08-Product-Decisions.md` (this entry). No code changes.

---

### [DECISION-051] Backup, Disaster Recovery & Operational Policy — Sprint 5.5.3
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.5.3 spec)
- **Status:** Approved
- **Decision:**
  1. **Backup philosophy — simple, shell-based, no packages** — database backups are performed by a shell script (`onemember-backup.sh`) invoking `mysqldump` and compressing with `gzip -9`. No Laravel backup package (e.g., spatie/laravel-backup) is introduced for V1.0. The shell script approach has no package dependencies, is auditable, and can be run independently of the Laravel application. Packages may be evaluated post-launch if operational complexity grows.
  2. **Backup schedule** — database backup: daily at 02:00 server time. File storage sync: daily at 02:30. Backup verification: daily at 03:00 (via Laravel scheduler, `backup:verify` command). The 1-hour window between backup and verification provides time for the dump to complete before the check runs.
  3. **`backup:verify` Artisan command** — a minimal command (`app/Console/Commands/VerifyDatabaseBackup.php`) checks whether a `db_*.sql.gz` file exists in the configured `BACKUP_PATH` directory that is less than 25 hours old. It logs pass/fail to the application log channel and exits 0 (success) or 1 (failure). The `--path` option allows testing with a custom directory. The backup path is configured via `BACKUP_PATH` in `.env` (default: `/var/backups/onemember`). This command has 5 automated feature tests.
  4. **Recovery objectives** — RTO (Recovery Time Objective): < 4 hours. RPO (Recovery Point Objective): < 24 hours. These are achievable with daily backups and the procedures in `docs/19-Backup-and-Disaster-Recovery.md`. A lower RPO requires MySQL binary log shipping or a managed database with point-in-time recovery — deferred to post-launch.
  5. **Retention policy** — local: daily backups retained 30 days; weekly snapshots retained 12 weeks. Off-site: daily backups retained 90 days; weekly snapshots retained 1 year. Pre-deployment snapshots retained 7 days locally, 30 days off-site.
  6. **Off-site backups are operator-configured, not code-driven** — the application does not hardcode any cloud provider or backup destination. Off-site sync (via rsync, rclone, SFTP, or any tool) is documented as an operational step performed by the hosting engineer. This preserves full provider independence.
  7. **Annual disaster recovery drill** — documented in `docs/19`, Section 15. Required annually. Verifies that the full rebuild procedure (Section 12.4) can be executed from scratch using only off-site backups and git. Target: < 4 hours end-to-end.
  8. **Secrets backup is manual and encrypted** — `.env` must be backed up to a secure vault (password manager, GPG-encrypted file, or secrets manager of operator's choice). It must never be committed to git. This is a standing operational requirement.
- **Reason:** Disaster recovery readiness is a launch prerequisite (docs/11-Launch-Checklist.md, Section 4.6). Without documented and tested restore procedures, production data loss is unrecoverable. A minimal code addition (backup:verify) provides automated daily confidence that backups are running without introducing package dependencies.
- **Impact:** New: `app/Console/Commands/VerifyDatabaseBackup.php`, `tests/Feature/BackupVerifyCommandTest.php` (5 tests), `docs/19-Backup-and-Disaster-Recovery.md`. Modified: `routes/console.php` (backup:verify scheduled at 03:00), `.env.example` (BACKUP_PATH key added).

---

### [DECISION-052] Performance Optimization — Sprint 5.5.4
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.5.4 spec)
- **Status:** Approved
- **Decision:**
  1. **Duplicate member COUNT query eliminated** — `DashboardController::index` previously called `$merchant->members()->count()` and then `$subscriptionService->usageSummary($merchant)`, which internally also called `$merchant->members()->count()`. Fixed by calling `usageSummary` first and reading `$subscriptionUsage['members']['used']` as `$totalActiveMembers`. Saves one `COUNT(*)` query per dashboard load.
  2. **`hasAnyMembers` short-circuit** — `$merchant->members()->withTrashed()->exists()` is now skipped when `$totalActiveMembers > 0` (the common case). The `EXISTS` query only fires for new merchants with no active members — the rare cold-start path.
  3. **Three composite indexes added** — migration `2026_06_29_000001_add_performance_indexes`:
     - `loyalty_programs (merchant_id, status)`: the `status` column was added in Sprint 3.1 without a supporting index. Every campaign list page and dashboard query filters `WHERE merchant_id = ? AND status = ?`. Without this index, the query performs a full tenant scan of `loyalty_programs`.
     - `redemptions (merchant_id, redeemed_at)`: the dashboard "Redeemed Today" KPI queries `WHERE merchant_id = ? AND DATE(redeemed_at) = ?`. Without this index, the query scans all redemption rows for the tenant.
     - `members (merchant_id, total_points)`: the dashboard "Top Members" query `ORDER BY total_points DESC LIMIT 5` can use this composite index to skip a full-tenant sort; MySQL returns the top 5 via an index range scan instead.
  4. **No new packages** — all optimizations are in application code and database schema only. No query caching, Redis caching, or external dependencies introduced. KISS principle followed.
  5. **No N+1 queries found** — all controllers were audited. Relationships that would cause N+1 are already eager-loaded (`transactions → member, loyaltyProgram`). List pages return flat columns without nested relationship access.
  6. **Legacy `is_active` indexes left in place** — `loyalty_programs` and `rewards` have legacy `(merchant_id, is_active)` indexes from the original migration. These are inert but harmless. Dropping them would require a destructive migration and provides no functional benefit at V1.0 scale. Deferred to a future maintenance sprint.
  7. **Production optimization commands** — documented in `docs/20-Performance-Optimization.md` Section 7. `php artisan optimize` must run after every deployment. Already included in `docs/17-Production-Deployment-Guide.md`.
- **Reason:** Performance audit is part of pre-launch hardening. The two code fixes reduce dashboard DB round-trips. The three indexes prevent full-tenant table scans on the most frequently executed queries. All changes are targeted and safe with no business logic impact.
- **Impact:** Modified: `app/Http/Controllers/DashboardController.php` (query order + short-circuit). New: `database/migrations/2026_06_29_000001_add_performance_indexes.php`, `docs/20-Performance-Optimization.md`. No schema columns changed.

---

### [DECISION-053] Release Candidate Audit — Sprint 5.5.5
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.5.5 spec)
- **Status:** Approved
- **Decision:**
  1. **Scope** — audit-only sprint. Only bugs, missing translations, broken active states, and developer-facing text visible to merchants are fixed. No new features, no UI redesign, no refactoring of working code.
  2. **Members sidebar active state** — `routeIs('members')` changed to `routeIs('members', 'members.*')` so the Members nav link stays highlighted on `members.show`, `members.create`, and any nested member route.
  3. **Footer version** — hardcoded `v0.1.0` replaced with `v{{ config('app.version') }}`, which reads from `APP_VERSION` in `.env` (set up in Sprint 5.5.1).
  4. **Settings view localization** — all 40+ hardcoded English strings in `resources/views/settings/index.blade.php` replaced with `__()` calls using the existing `settings.*` and `buttons.*` translation keys. Three missing keys (`business_type`, `business_phone`, `website`) were added to both `lang/en/settings.php` and `lang/th/settings.php`. One missing key (`select`) was added to both `lang/en/buttons.php` and `lang/th/buttons.php`.
  5. **Translation parity restored** — `lang/th/validation.php` was missing 14 Laravel validation rule keys present in `lang/en/validation.php` (`hex_color`, `missing`, `missing_if`, `missing_unless`, `missing_with`, `missing_with_all`, `multiple_of`, `present_if`, `present_unless`, `present_with`, `present_with_all`, `prohibits`, `required_if_accepted`, `ulid`). All 14 keys added with Thai translations. All 12 translation namespaces now have equal key counts between `lang/en/` and `lang/th/`.
  6. **Developer tooltip text removed from merchant UI** — three disabled buttons had `title="Coming in a future task/sprint"` text visible to merchants on hover. Replaced with `{{ __('buttons.coming_soon') }}` (translatable, product-appropriate).
  7. **Dashboard hardcoded strings** — reviewed; strings such as "Trial" badge, "Professional trial" sentence, "Plan:", "Status:", "Upgrade Plan", and "Lifetime pts" appear in `dashboard.blade.php`. These strings are part of the subscription display logic and are already partially covered by `dashboard.*` and `subscription.*` translation keys. Remaining hardcoded strings in this view are deferred to a dedicated localization sprint to avoid touching subscription display logic in an audit sprint.
  8. **No security issues found** — no SQL injection vectors, no XSS risks, no exposed secrets, no insecure direct object references. All tenant scoping is applied via `Auth::user()->merchant` at the controller level.
  9. **No broken routes or missing views found** — all routes registered in `routes/web.php` resolve to existing controllers and views.
  10. **All 62 automated tests pass** after all fixes.
- **Reason:** Pre-launch audit is required before V1.0 release per `docs/11-Launch-Checklist.md`. Fixing translation gaps, navigation active state, and visible developer text are minimum-quality requirements for a product shipped to real merchants.
- **Impact:** Modified: `resources/views/layouts/app.blade.php` (sidebar active state, footer version), `resources/views/settings/index.blade.php` (full localization), `resources/views/members/index.blade.php` (tooltip text), `resources/views/members/show.blade.php` (tooltip text), `resources/views/campaigns/show.blade.php` (tooltip text), `lang/en/settings.php` (+3 keys), `lang/th/settings.php` (+3 keys), `lang/en/buttons.php` (+1 key), `lang/th/buttons.php` (+1 key), `lang/th/validation.php` (+14 keys). New: `docs/21-Release-Candidate-Audit.md`.

---

### [DECISION-054] Feedback and Analytics Abstraction Layer — Sprint 5.7
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 5.7 spec)
- **Status:** Approved
- **Decision:**
  1. **Abstraction layer required** — all analytics and error tracking must go through `App\Services\AnalyticsService`. Vendor SDKs (PostHog, Sentry, Clarity) must never be called directly from controllers, commands, or any application code outside of `AnalyticsService`.
  2. **No-op by default** — analytics is disabled by default (`ANALYTICS_ENABLED=false`). The service never throws; all errors are swallowed silently so analytics failures cannot break the application.
  3. **No database migration** — user feedback is stored as timestamped JSON files in `storage/app/feedback/`. No new database table is introduced.
  4. **Vendor independence** — PostHog integration uses native PHP `file_get_contents()` with stream context; no SDK package dependency added. Sentry integration uses `class_exists()` to detect an optional SDK; no hard `require` added.
  5. **Feature toggles** — individual analytics features (page views, events, exceptions, identify) can be enabled or disabled independently via `.env` flags without code changes.
  6. **14 named events tracked** — `merchant_registered`, `onboarding_completed`, `campaign_created`, `campaign_archived`, `member_created`, `member_archived`, `reward_created`, `reward_archived`, `purchase_recorded`, `reward_redeemed`, `settings_updated`, `subscription_viewed`, `trial_expired`, `dashboard_viewed`, `feedback_submitted`.
- **Reason:** Preventing vendor lock-in is a core architectural principle. Analytics providers change. Wrapping all provider calls in a service layer ensures a provider swap requires editing only one file. Feedback-as-files avoids premature database schema complexity before V1.0 launch.
- **Impact:** New: `app/Services/AnalyticsService.php`, `config/analytics.php`, `app/Http/Controllers/FeedbackController.php`, `app/Http/Requests/FeedbackRequest.php`, `resources/views/feedback/modal.blade.php`, `lang/en/feedback.php`, `lang/th/feedback.php`, `storage/app/feedback/.gitkeep`, `docs/22-Feedback-and-Analytics.md`, `tests/Feature/FeedbackAnalyticsTest.php`. Modified: `resources/views/layouts/app.blade.php`, `lang/en/navigation.php`, `lang/th/navigation.php`, `routes/web.php`, `.env.example`, all controllers listed in point 6 above, `app/Console/Commands/ProcessExpiredTrials.php`.

---

### [DECISION-055] Stripe as Billing Source of Truth — Sprint 6.1
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 6.1 spec)
- **Status:** Approved
- **Decision:**
  1. **Stripe is the billing source of truth.** All subscription state (plan, status, renewal date, cancellation flag) is updated exclusively through verified Stripe webhook events. Browser-initiated actions (clicking upgrade, cancel, portal) only trigger Stripe API calls — they never directly mutate `merchants.subscription_status`, `merchants.subscription_plan`, or `merchants.subscription_renews_at`.
  2. **BillingService is the sole Stripe integration point.** No Stripe SDK class may be imported or instantiated outside of `App\Services\BillingService`. Controllers call BillingService methods only.
  3. **Webhook signatures must be verified.** Every incoming webhook is verified with `Stripe\Webhook::constructEvent()` using `STRIPE_WEBHOOK_SECRET`. Any request that fails signature verification receives a 400 response without processing.
  4. **Idempotency via cache.** Processed Stripe event IDs are cached for 24 hours. Duplicate delivery of the same event ID is silently skipped.
  5. **Webhook route is CSRF-exempt.** The route `POST /stripe/webhook` is excluded from Laravel CSRF verification because Stripe's signature verification serves the equivalent security purpose.
  6. **Billing events are dispatched, not emails.** `SubscriptionPurchased`, `SubscriptionCancelled`, `SubscriptionRenewed`, `PaymentFailed`, and `TrialEnding` Laravel events are dispatched from `BillingService`. No email delivery is implemented in this sprint — a future email sprint will add listeners.
  7. **No card data touches OneMember.** Stripe Checkout and Billing Portal handle all payment method collection. OneMember never sees or stores raw card numbers. PCI scope is Stripe's responsibility.
  8. **Stripe Checkout used for new subscriptions.** Trial merchants who wish to subscribe are redirected to Stripe Checkout. Existing subscribers who upgrade/downgrade use `Subscription::update()` with proration.
  9. **Billing Portal used for self-service.** Card updates, invoice downloads, and cancellations can all be performed via the Stripe Billing Portal. The portal URL is created server-side and is merchant-specific.
- **Reason:** Making Stripe the authoritative source prevents split-brain states where the application database and Stripe disagree on the merchant's subscription. Webhook-only state updates ensure consistency even when browser sessions disconnect, payment processing is asynchronous, or Stripe retries failed events.
- **Impact:** New: `app/Services/BillingService.php`, `config/stripe.php`, `database/migrations/2026_06_29_100001_add_stripe_fields_to_merchants_table.php`, `app/Events/SubscriptionPurchased.php`, `app/Events/SubscriptionCancelled.php`, `app/Events/SubscriptionRenewed.php`, `app/Events/PaymentFailed.php`, `app/Events/TrialEnding.php`, `resources/views/subscription/success.blade.php`, `tests/Feature/StripeBillingTest.php`, `docs/23-Stripe-Billing.md`. Modified: `app/Http/Controllers/SubscriptionController.php` (expanded), `app/Models/Merchant.php` (new fillable/casts), `resources/views/subscription/index.blade.php` (live buttons), `routes/web.php` (8 new routes), `bootstrap/app.php` (CSRF exclusion), `lang/en/subscription.php`, `lang/th/subscription.php`, `.env.example`, `composer.json` (`stripe/stripe-php ^20`).

---

### [DECISION-056] All Transactional Email is Event-Driven — Sprint 6.2
- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 6.2 spec)
- **Status:** Approved
- **Decision:**
  1. **Controllers never send email.** No `Mail::send()`, `Mail::to()->send()`, or `Mail::to()->queue()` call may appear in any controller, service, or model. The only permitted location is `EmailEventSubscriber`.
  2. **All email is triggered by Laravel events.** An event is dispatched (registration, password change, billing update, feedback submission) and `EmailEventSubscriber` handles the email dispatch. This decouples email delivery from business logic.
  3. **Every Mailable implements ShouldQueue.** Emails are never sent synchronously. All emails go through the Laravel queue (`QUEUE_CONNECTION=database`). If the queue is not running, emails are delayed — not lost.
  4. **EmailEventSubscriber is the sole email dispatcher.** It subscribes to all relevant events and calls `Mail::to($email)->queue($mailable)`. It is auto-discovered by Laravel 13 — no manual registration in `AppServiceProvider` or `EventServiceProvider`.
  5. **EmailLogger records all email activity.** Every send attempt (sending, sent, failed) is written to `storage/logs/email.log` via a dedicated `email` log channel. Email addresses are masked in log output.
  6. **Delivery status events are dispatched.** `EmailSending`, `EmailSent`, and `EmailFailed` events are dispatched for future telemetry or retry integration.
  7. **Notification preferences are merchant-controlled.** Optional email categories (`product_updates`, `tips`, `feature_announcements`) can be disabled per-merchant via Settings → Preferences. Billing receipts and security alerts cannot be disabled.
  8. **Billing emails come from billing events.** No email logic exists in `BillingService`. `BillingService` dispatches billing events; `EmailEventSubscriber` sends the emails.
  9. **All emails are localized.** EN and TH translations must maintain identical key counts. The locale at render time is the merchant's configured locale.
  10. **FeedbackSubmitted sends two emails.** A thank-you to the submitter and a support notification to `SUPPORT_EMAIL`. Both go through `FeedbackReceivedEmail` with a `$forSupport` flag.
- **Reason:** Keeping email dispatch event-driven ensures that (a) any future email provider change only requires updating `EmailEventSubscriber`, not hunting through controllers; (b) email sending failures never break controller flows (exceptions are caught and logged); (c) queued delivery ensures emails don't block HTTP response times.
- **Impact:** New: `config/email.php`, `app/Events/TrialStarted.php`, `app/Events/PasswordChanged.php`, `app/Events/FeedbackSubmitted.php`, `app/Events/EmailSending.php`, `app/Events/EmailSent.php`, `app/Events/EmailFailed.php`, `app/Listeners/EmailEventSubscriber.php`, `app/Services/EmailLogger.php`, `app/Mail/` (10 mailables), `resources/views/emails/` (11 templates), `lang/en/email.php`, `lang/th/email.php`, `tests/Feature/EmailInfrastructureTest.php`, `docs/24-Production-Email.md`. Modified: `app/Http/Controllers/Auth/PasswordController.php` (dispatch PasswordChanged), `app/Http/Controllers/FeedbackController.php` (dispatch FeedbackSubmitted), `app/Http/Controllers/OnboardingController.php` (dispatch TrialStarted), `app/Http/Controllers/SettingsController.php` (save email_notifications), `app/Http/Requests/UpdateMerchantPreferencesRequest.php` (add email prefs fields), `app/Models/Merchant.php` (add wantsEmail()), `config/logging.php` (add email channel), `lang/en/settings.php`, `lang/th/settings.php`, `resources/views/settings/index.blade.php` (email notification UI), `.env.example`.

---

### DECISION-057: White Label Lite — Merchant Branding with Visible OneMember Credit

- **Date:** 2026-06-29
- **Requested by:** Product Owner (Sprint 6.3 spec)
- **Status:** Approved
- **Decision:**
  1. **OneMember is not a full white-label platform in V1.** Merchants can customize their dashboard and emails with a logo, brand colors, tagline, and social links, but OneMember branding is always visible. Full white-label (removing all OneMember references) is deferred to V2.
  2. **White Label Lite definition:** Merchant logo is displayed in the sidebar and emails. "Powered by OneMember" text appears alongside the merchant logo in the sidebar. The OneMember favicon, footer, and domain remain unchanged.
  3. **MerchantBrandingService is the sole source of branding logic.** Controllers and views may NOT access the Merchant model directly for any branding purpose. All branding decisions (logo URL, colors, fallbacks) go through `MerchantBrandingService`.
  4. **Fallback behavior is defined.** When no merchant logo exists: show hexagon icon + app name. When no brand_color: use `#2563EB`. When no secondary_color: use `#1E293B`. All fallbacks are defined as constants in `MerchantBrandingService`.
  5. **Logo files are tenant-namespaced.** Filenames include `merchant_id` to prevent cross-tenant file collision or overwrite. Pattern: `merchant-logos/{merchant_id}_{timestamp}.{ext}`.
  6. **Billing emails show merchant identity.** `subscription-purchased`, `subscription-renewed`, `subscription-cancelled`, `payment-failed`, `trial-starting`, and `trial-ending-reminder` templates show the merchant logo and business name so the merchant's customers recognize the email source.
  7. **Receipt footer is opt-in.** Merchants can add a custom footer to billing emails via `receipt_footer`. This is shown below the email body and above the sender name. It is nullable.
  8. **Billing and security categories cannot be white-labeled away.** The `billing` and `security_alerts` email preferences remain always-on regardless of branding settings.
- **Reason:** A full white-label offering in V1 would require domain mapping, custom favicon, footer customization, and revenue-share or reseller agreements. "White Label Lite" gives merchants the brand personalization they need (logo, colors) while keeping the platform's identity visible — which builds trust for members and reduces support burden.
- **Impact:** New: `app/Services/MerchantBrandingService.php`, `database/migrations/2026_06_29_000001_add_branding_fields_to_merchants_table.php`, `tests/Feature/MerchantBrandingTest.php`, `docs/25-Merchant-Branding.md`. Modified: `app/Models/Merchant.php` (fillable), `app/Http/Requests/UpdateMerchantProfileRequest.php` (branding validation), `app/Http/Controllers/SettingsController.php` (logo upload + branding save), `resources/views/layouts/app.blade.php` (merchant logo in sidebar), `resources/views/settings/index.blade.php` (branding section), `resources/views/emails/*.blade.php` (6 templates updated), `lang/en/settings.php`, `lang/th/settings.php`, `lang/en/navigation.php`, `lang/th/navigation.php`, `lang/en/email.php`, `lang/th/email.php`.

---

### DECISION-058: Data Import Never Overwrites; Duplicates Require Explicit Action

- **Date:** 2026-06-30
- **Requested by:** Product Owner (Sprint 6.4 spec)
- **Status:** Approved
- **Decision:**
  1. **Imports never overwrite existing records automatically.** When a CSV row matches an existing member (by phone or email), the row is flagged as a duplicate and skipped — never updated, merged, or overwritten. The merchant receives a report showing which rows were skipped and why.
  2. **Duplicate detection is based on phone (primary) and email (secondary).** A row is a duplicate if a member with the same phone number OR the same email address already exists for that merchant. Phone is checked first because it is the primary identifier in OneMember.
  3. **Merchants must take explicit action to resolve duplicates.** Resolving duplicates (e.g., editing existing records or choosing to skip) must be a deliberate manual step in the application — never automatic.
  4. **Large imports (>5,000 rows) are automatically queued.** Imports with more than 5,000 rows dispatch an `ImportMembersJob` and the merchant is notified that the import is processing. Imports ≤5,000 rows run synchronously.
  5. **Imports use per-row transactions.** Each row is committed or rolled back independently. A failure on one row does not abort the remaining rows. The result report distinguishes: imported, skipped (duplicate), failed (error), and warnings.
  6. **Exports are always scoped to the authenticated merchant.** No cross-tenant data can appear in any export. Exports are streamed (not buffered in memory) and include a UTF-8 BOM for Excel compatibility.
  7. **CSV only.** Imports and exports use CSV format exclusively. Excel (`.xlsx`) support is deferred.
  8. **Maximum upload size: 10 MB.** Files exceeding 10 MB are rejected at the request validation layer.
  9. **Security logging.** Import attempts, completions, and failures are written to the security log. Export generation is also logged. No PII (member names, emails, phones) is written to logs.
- **Reason:** Silent overwrites are a data-safety risk. A merchant who re-imports a CSV after edits should not silently lose changes made inside OneMember. The "flag and skip" approach maximises data integrity while still giving the merchant visibility into what happened. Queue-based large import prevents HTTP timeouts and provides a better UX for bulk migrations.
- **Impact:** New: `app/Services/ImportService.php`, `app/Services/ExportService.php`, `app/Http/Controllers/DataManagementController.php`, `app/Jobs/ImportMembersJob.php`, `resources/views/data/*.blade.php`, `lang/en/data.php`, `lang/th/data.php`, `tests/Feature/DataImportExportTest.php`, `docs/26-Data-Import-and-Export.md`. Modified: `routes/web.php`, `resources/views/settings/index.blade.php`, `app/Http/Controllers/SettingsController.php`, `app/Services/SecurityLogger.php`, `lang/en/settings.php`, `lang/th/settings.php`.

---

### DECISION-059: Mobile Experience — Overlay Sidebar, Counter Mode, FAB, PWA Readiness

- **Date:** 2026-06-30
- **Requested by:** Product Owner (Sprint 6.5 spec)
- **Status:** Approved
- **Decision:**
  1. **Mobile sidebar is an overlay, not a push layout.** On viewports narrower than 768 px, the sidebar slides in as a fixed overlay (z-index 1055) with a semi-transparent backdrop. Tapping the backdrop closes the sidebar. On desktop (≥768 px) the existing inline push layout is unchanged.
  2. **Sidebar starts closed on mobile.** The Alpine.js initial state uses `window.innerWidth >= 768` so the sidebar is open by default on desktop and closed by default on mobile. No server-side detection is used.
  3. **Counter Mode is a persisted merchant preference.** When enabled, a full-width quick-action bar is shown at the top of every authenticated page, providing one-tap access to Find Member and Add Purchase. The preference is stored as `merchant.settings['counter_mode']` (boolean) and toggled via `PUT /settings/counter-mode`. The toggle button appears in the topbar on all screens.
  4. **FAB (Floating Action Button) is mobile-only.** A circular FAB is shown in the bottom-right corner of every authenticated page on viewports < 768 px. It links to Add Member (`/members/create`). It is hidden on desktop to avoid cluttering the desktop UX.
  5. **Minimum touch target size is 44 × 44 px.** All interactive elements on mobile (nav links, buttons, form controls) must meet the WCAG 2.5.5 target size guideline. Achieved through `min-height: 44px` CSS rules in the mobile media query.
  6. **Form keyboard hints.** Phone number inputs use `type="tel"` and `inputmode="numeric"`. Email inputs use `type="email"` and `autocomplete="email"`. This triggers the correct software keyboard on mobile without changing validation behaviour.
  7. **PWA Readiness (groundwork only).** A `manifest.webmanifest` is served from `/public`, with app name, icons placeholder, and `theme-color`. Apple touch meta tags and `<meta name="theme-color">` are added to the layout `<head>`. Full PWA (service worker, offline cache) is deferred to a future sprint.
  8. **No Tailwind. No inline CSS. No inline JS beyond Alpine.js attribute expressions.** All new styles go into `resources/css/app.css`. All behaviour is driven by Alpine.js `x-data`, `x-show`, `:class`, and `@click` attribute expressions — the established pattern in this project.
- **Reason:** Merchants increasingly manage their loyalty programme from a phone at the counter. The existing desktop-first layout is unusable below 768 px: the sidebar takes up the full width, touch targets are too small, and there is no quick path to core POS actions. Counter Mode and the FAB address the two most common POS micro-tasks without restructuring the desktop experience.
- **Impact:** New: `app/Http/Controllers/CounterModeController.php`, `resources/views/components/fab.blade.php`, `lang/en/mobile.php`, `lang/th/mobile.php`, `public/manifest.webmanifest`, `tests/Feature/MobileExperienceTest.php`, `docs/27-Mobile-Experience.md`. Modified: `resources/css/app.css` (mobile overlay sidebar, FAB, counter mode, touch targets), `resources/views/layouts/app.blade.php` (PWA meta, backdrop, counter mode bar, FAB include, x-data init), `resources/views/members/create.blade.php` (phone type="tel", email type="email"), `routes/web.php` (counter mode toggle route).

---

### DECISION-060: Customer Self-Service Portal — Web-First; Native App Deferred

- **Date:** 2026-06-30
- **Requested by:** Product Owner (Sprint 6.6 spec)
- **Status:** Approved
- **Decision:**
  1. **The customer experience begins on the web.** The customer self-service portal is a responsive web page, not a native mobile app. No iOS or Android build is required for V1.
  2. **No customer accounts.** Customers do not create accounts, set passwords, or register. Access is via a unique public URL and QR code generated by the merchant. This removes the need for customer auth infrastructure and dramatically lowers the barrier to adoption.
  3. **Public UUIDs, never database IDs.** Every member receives a `public_uuid` (UUID v4) generated at creation time. Portal URLs are `/member/{public_uuid}`. Database primary keys, merchant IDs, and internal member IDs are never exposed publicly.
  4. **QR codes are deterministic.** A member's QR code always encodes the same URL (`/member/{public_uuid}`) until the merchant explicitly regenerates it (which issues a new `public_uuid`). This means QR codes can be printed safely — they will not break unless the merchant chooses to regenerate.
  5. **Portal is per-member toggleable.** Merchants can disable the portal for a specific member (e.g., archived or suspended members) via `portal_enabled` on the Member model. A disabled portal shows a friendly "not available" message, not a 404.
  6. **Portal shows only customer-safe data.** The portal exposes: member name, member code, points/stamp balance, campaign rewards, redemption history (reward name + date only), birthday banner, member since, and last visit. It never exposes: internal IDs, phone numbers, email addresses, purchase amounts, staff names, internal notes, analytics, or audit logs.
  7. **Portal inherits merchant branding.** `MerchantBrandingService` drives all colours, logos, and names on the portal — same as the merchant dashboard. The portal shows "Powered by OneMember" per DECISION-057 (White Label Lite).
  8. **Native apps are intentionally deferred.** iOS and Android customer apps are a Version 2.0 feature. The web portal can be added to the home screen via standard PWA capabilities (groundwork laid in Sprint 6.5). A native app will be considered once the merchant base demonstrates sufficient demand and the core feature set stabilises.
  9. **Email sharing is prepared but not active.** `CustomerPortalService` includes stub methods for sending member cards, QR codes, and welcome cards via email. These stubs document the future capability but do not send any emails in V1.
- **Reason:** A native app would require App Store approval, code-signing infrastructure, and ongoing maintenance for two platforms. A responsive web portal delivers 80% of the customer value at 20% of the effort. The no-account model eliminates customer registration friction — customers tap the merchant's QR code and immediately see their balance.
- **Impact:** New: `database/migrations/2026_06_30_000001_add_portal_fields_to_members_table.php`, `app/Services/CustomerPortalService.php`, `app/Http/Controllers/CustomerPortalController.php`, `resources/views/layouts/portal.blade.php`, `resources/views/portal/*.blade.php`, `lang/en/portal.php`, `lang/th/portal.php`, `tests/Feature/CustomerPortalTest.php`, `docs/28-Customer-Self-Service.md`. Modified: `app/Models/Member.php` (public_uuid, portal_enabled), `routes/web.php`, `resources/views/members/show.blade.php` (QR card + portal controls), `lang/en/members.php`, `lang/th/members.php`.

---

### DECISION-061: Merchant Intelligence — Rule-Based V1; Architecture Supports Future AI

- **Date:** 2026-06-30
- **Requested by:** Product Owner (Sprint 6.7 spec)
- **Status:** Approved
- **Decision:**
  1. **Merchant Intelligence is rule-based in V1.** All insights, health scores, and opportunity cards are computed from existing merchant data using deterministic rules — no external AI services, no ML models, no API calls.
  2. **The architecture is designed for future AI enhancement.** `InsightProviderInterface` defines the contract between the service and the insight logic. `RuleBasedInsightProvider` is the V1 implementation. A future `AiInsightProvider` can implement the same interface and replace or augment the rule-based provider without changing the UI, the controller, or the caching layer.
  3. **`MerchantIntelligenceService` is the single source of truth.** All insight queries, health score calculations, and opportunity detection go through `MerchantIntelligenceService`. No controller or view may compute insights directly.
  4. **Results are cached for 15 minutes per merchant.** All three outputs (insights, health score, opportunities) are computed together in one provider call and cached under a single key (`merchant_intelligence_{id}`). Cache is not invalidated on individual actions — it expires naturally.
  5. **Health score is 0–100.** Factors: active campaign (+15), active rewards (+10), member count (+5/+10/+15/+20), recent purchases 30d (+5/+15/+20/+25), any redemptions (+15), paid plan (+15). Labels: Excellent (80+), Good (60–79), Needs Attention (40–59), Getting Started (20–39), New Business (0–19).
  6. **Dashboard shows a maximum of 5 insights.** Insights are sorted by priority (high > medium > low). If more than 5 are generated, the lowest-priority ones are dropped.
  7. **No controller business logic.** Business logic belongs inside `MerchantIntelligenceService` and `RuleBasedInsightProvider`. The controller only injects the service and passes data to the view.
  8. **Weekly summary is a data stub.** `getWeeklySummary()` returns a structured array for future email use but does not send any emails in V1.
- **Reason:** The product needs actionable business insights, but building an AI pipeline in V1 would be premature. Rule-based insights are predictable, fast, and testable. The interface-based architecture ensures that upgrading to AI recommendations later is a swap of one class, not a rewrite of the feature.
- **Impact:** New: `app/Contracts/InsightProviderInterface.php`, `app/Services/Intelligence/RuleBasedInsightProvider.php`, `app/Services/MerchantIntelligenceService.php`, `lang/en/intelligence.php`, `lang/th/intelligence.php`, `tests/Feature/MerchantIntelligenceTest.php`, `docs/29-Merchant-Intelligence.md`. Modified: `app/Http/Controllers/DashboardController.php` (intelligence injection), `resources/views/dashboard.blade.php` (intelligence card), `app/Providers/AppServiceProvider.php` (interface binding).

---

### DECISION-062: Developer Tools Module — Development/Debug Only; Production Returns 404

- **Date:** 2026-07-01
- **Requested by:** Engineering (Sprint DEV-01 spec)
- **Status:** Approved
- **Decision:**
  1. **Developer Tools is a development-only module.** It is enabled when `APP_ENV=local` or `APP_ENV=development` or (`APP_DEBUG=true` AND authenticated user). In production (`APP_ENV=production`) all routes return 404 and the nav section is hidden.
  2. **Protection via dedicated middleware.** A `DevToolsAccess` middleware enforces the access rules at the route level. It cannot be bypassed by forgetting a Blade check.
  3. **All developer actions are audit-logged.** Every action (user management, member manipulation, queue commands, DB operations, etc.) is recorded in `developer_actions` with user_id, action, target_type, target_id, details (JSON), ip_address, user_agent, created_at. No soft delete on audit rows.
  4. **12 pages:** Users, Members, Merchant, OTP & Verification, Test Mail, Database, Queue, Storage, Development Helpers, Environment, System Health, Danger Zone.
  5. **Danger Zone requires confirmation.** Destructive actions in the Danger Zone require the user to type `DELETE` in a Bootstrap modal before the action proceeds.
  6. **Service class architecture.** Business logic is encapsulated in `App\Services\DevTools\*` service classes. Controllers are thin and only call services + log audit.
  7. **No production data risk.** The middleware hard-blocks in production. The module is never registered in production routes.
  8. **Uses existing Bootstrap admin layout.** Developer Tools pages use the standard `layouts.app` Blade layout and Bootstrap 5 + Bootstrap Icons consistent with the rest of OneMember.
- **Reason:** Developers need a fast, safe way to manipulate data during local development and testing without writing artisan commands each time. The strict production block ensures the module can never be misused in a live environment.
- **Impact:** New: `app/Http/Middleware/DevToolsAccess.php`, `app/Http/Controllers/DevTools/` (12 controllers), `app/Services/DevTools/` (service classes), `app/Models/DeveloperAction.php`, `database/migrations/*_create_developer_actions_table.php`, `routes/dev.php`, `resources/views/dev/` (12 pages + layout).

---

### DECISION-063: Developer Tools Sprint DEV-02 — Feature Flag + Productivity Suite

- **Date:** 2026-07-01
- **Requested by:** Engineering (Sprint DEV-02 spec)
- **Status:** Approved
- **Decision:**
  1. **Feature flag `DEV_TOOLS_ENABLED`** is added. Middleware now requires BOTH `APP_ENV != production` AND `DEV_TOOLS_ENABLED=true`. Defaults to `false`. Must be explicitly set on staging/local.
  2. **No page is rebuilt from DEV-01.** All new pages are additive: Developer Dashboard (index), Quick Actions, Mail Inspector (replaces/extends existing mail page), Queue Inspector (extends), Environment Inspector (extends), Performance Tools, Log Viewer, Demo Data Reset.
  3. **Queued jobs for heavy data generation.** `GenerateDemoDataJob` runs member/transaction/redemption seeding in the background. All jobs go through `QUEUE_CONNECTION`.
  4. **Log Viewer tails `storage/logs/laravel.log`** — last 100 lines by default, search, filter by level, download, clear. Server-side only; no websocket tail.
  5. **Developer Dashboard** is the new root `/dev` page showing system-wide stats and health at a glance.
  6. **Demo Data Reset** runs inside a database transaction. Archives merchants, deletes demo members/transactions/campaigns/rewards/notifications/failed jobs, then re-seeds via `DatabaseSeeder` dev fixture classes.
  7. **API keys are never displayed.** Only last 4 chars shown (e.g. `***xxxx`).
  8. **Feature flags page** shows `DEV_TOOLS_ENABLED` and other boolean env vars relevant to development; cannot be edited from the UI (env is read-only at runtime).
- **Reason:** DEV-01 built the operational tools. DEV-02 adds productivity tooling: at-a-glance dashboard, data generation, log inspection, and the safety gate of `DEV_TOOLS_ENABLED` so staging environments require opt-in.
- **Impact:** Modified: `DevToolsAccess` middleware, `.env.example`, `routes/dev.php`, `_nav.blade.php`. New: `DevDashboardController`, `DevQuickActionsController`, `DevMailInspectorController`, `DevQueueInspectorController`, `DevEnvironmentInspectorController`, `DevPerformanceController`, `DevLogViewerController`, `DevDemoResetController`, `DevFeatureFlagsController`, `GenerateDemoDataJob`, `DevDemoService`, `tests/Feature/DevTools/`.

---

### [DECISION-052] RELEASE-1C — Production Multilingual Architecture (Thai First)
- **Date:** 2026-07-04
- **Requested by:** CTO (Executive Order)
- **Status:** Approved
- **Decision:**
  1. **Thai is the default language.** `APP_LOCALE=th` in production. All user-facing strings default to Thai.
  2. **Two supported languages for V1.0:** Thai (`th`) and English (`en`). Architecture must support adding future languages (`zh`, `ja`, `ko`, `vi`, etc.) by creating `lang/xx/` only — no Blade or controller changes required.
  3. **Language switcher (`<x-language-switcher />`) is required on every page** — corporate, guest, wizard, and app layouts. Position: top-right. Display: globe icon + current language label + dropdown.
  4. **Locale switch reloads the current page immediately** — `LocaleController::switch()` persists locale to session (guests) and to `merchant.settings['locale']` (authenticated), then redirects back to the referring URL so the current page reloads in the new language.
  5. **Locale priority chain:**
     - Authenticated: merchant settings → session → `APP_LOCALE`
     - Guests: session → `APP_LOCALE`
  6. **No user-facing hardcoded strings in Blade templates.** All text must use `__('file.key')`. Blade files must never contain raw English or Thai strings as visible content.
  7. **Validation messages follow selected language.** Raw attribute names must never appear in validation errors. The `attributes` array in `lang/*/validation.php` covers all form fields.
  8. **Translation file structure:** `lang/{en,th}/{auth, buttons, campaigns, common, dashboard, members, navigation, onboarding, rewards, settings, subscription, validation}.php`. New language files added by creating `lang/xx/` — no code changes.
  9. **Onboarding locale safety:** `StoreOnboardingBusinessSettingsRequest` must never fail due to a missing locale field. `prepareForValidation()` defaults locale to `app()->getLocale() ?: 'th'`.
- **Reason:** Thailand launch requires full Thai-default operation with professional English support. RELEASE-1C is the permanent i18n foundation. All future features build on this architecture.
- **Impact:** `LocaleController`, `SetLocale` middleware, `<x-language-switcher />` component, all 4 layouts, all auth/onboarding/dashboard/members/campaigns/rewards/settings Blade views, `lang/en/*`, `lang/th/*`, `StoreOnboardingBusinessSettingsRequest`.

---

### [DECISION-064] RELEASE-2A — Corporate Website Completion (PDPA Page + Sprint Formalisation)
- **Date:** 2026-07-04
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:**
  1. **RELEASE-2A formalises the corporate website** built in RELEASE-1B and localised in RELEASE-1C as the canonical public-facing marketing website for `onemember.co`.
  2. **Ten pages are in scope:** Home, Features, Industries, Pricing, About, FAQ, Contact, Privacy Policy, Terms of Service, and the new PDPA Privacy Notice. The first nine already exist; this sprint adds the tenth.
  3. **PDPA Privacy Notice is a required standalone page** at `/pdpa` (route `corporate.pdpa`). It is a formal disclosure under Thailand's Personal Data Protection Act B.E. 2562 and is distinct from the Privacy Policy. Content covers: data controller identity, categories of data, legal bases, purposes, third-party sharing, retention periods, data subject rights, how to exercise rights, cookies, security measures, international transfers, children's data, and DPO contact.
  4. **PDPA Notice linked from the footer Legal column** between Privacy Policy and Security & PDPA.
  5. **All corporate pages use the RELEASE-1C localisation architecture.** Thai is default; English is supported. The PDPA page uses `trans('corporate.pdpa_full_sections')` for section iteration and `__()` for all scalar strings. No hardcoded strings.
  6. **No new infrastructure, no new database tables, no new middleware.** This is a content and routing sprint only.
  7. **SEO, Open Graph, and canonical tags** are already implemented in `layouts/corporate.blade.php` from RELEASE-1B. Each page supplies its own `title` and `description` slot values.
- **Reason:** The PDPA page is a legal compliance requirement for operating in Thailand. RELEASE-2A closes the gap between the corporate website as built and the full scope required before the site goes live at `onemember.co`.
- **Impact:** New: `resources/views/corporate/pdpa.blade.php`, `lang/en/corporate.php` (pdpa_* keys), `lang/th/corporate.php` (pdpa_* keys). Modified: `app/Http/Controllers/CorporateController.php` (pdpa method), `routes/web.php` (pdpa route), `resources/views/layouts/corporate.blade.php` (footer PDPA link).

---

### [DECISION-066] RELEASE-2B — Mobile Merchant Experience
- **Date:** 2026-07-05
- **Requested by:** CTO
- **Status:** Approved
- **Decision:**
  1. **Overlay sidebar on mobile** (< 768 px) uses CSS `position: fixed; left: calc(-1 * var(--om-sidebar-width))` and slides in to `left: 0` when open. Desktop keeps the inline sticky sidebar. Controlled by Alpine.js `sidebarOpen` state.
  2. **Close button inside sidebar** is present on mobile only (`d-md-none`), positioned at the top of the sidebar so users can dismiss without reaching outside the drawer.
  3. **ESC key closes sidebar** on mobile via `@keydown.escape.window` on the root Alpine container.
  4. **Body scroll lock** applied when mobile sidebar is open: Alpine `x-effect` adds/removes `om-sidebar-open` class on `document.body`; CSS `body.om-sidebar-open { overflow: hidden }`.
  5. **Language switcher shows icon-only on mobile** (`< 576 px`) to prevent topbar overflow; label text visible from 576 px up.
  6. **FAB bottom clearance**: `content-area` on mobile gets `padding-bottom: 5rem` so the fixed FAB never overlaps the last row of page content.
  7. **Responsive tables**: `table-responsive-stack` class stacks rows into labelled card rows at `< 576 px`. All tables that need it must carry this class.
  8. **Touch targets**: All interactive elements in the merchant app maintain a minimum of 44 × 44 px on mobile via existing CSS rules — no individual overrides per element.
  9. **No new JS build tooling** introduced; Alpine.js directives used inline in Blade templates to keep the mobile behaviour co-located with markup.
- **Reason:** The merchant application is used on-counter by merchants on mobile devices. An unusable mobile experience directly reduces the product's primary use case. This sprint formalises the mobile UX as production-grade rather than an afterthought.
- **Impact:** Modified: `resources/views/layouts/app.blade.php` (close button, ESC, scroll-lock effect), `resources/css/app.css` (scroll lock, FAB padding, lang switcher). New: `tests/Feature/MobileNavTest.php`, `docs/OMOS/Sprints/RELEASE-2B-Mobile-Merchant-Experience.md`.

---

### [DECISION-065] Domain-Aware Routing — Single App, Two Domains
- **Date:** 2026-07-04
- **Requested by:** CTO
- **Status:** Approved
- **Decision:**
  1. **One Laravel application, one Forge site** serves both `onemember.co` (corporate) and `app.onemember.co` (merchant application). No second app, no second Forge site, no monorepo split.
  2. **Three domain groups in `routes/web.php`:**
     - `www.onemember.co` → 301 redirect to `onemember.co` (canonical)
     - `onemember.co` → corporate pages only; `/login`, `/register` redirect 301 to `app.onemember.co`; `/dashboard`, `/onboarding` redirect 302 to `app.onemember.co`
     - `app.onemember.co` → all merchant application routes + Breeze auth routes
  3. **Domain-agnostic routes** (`/up` health check, `/locale` language switcher) registered before domain groups so they respond on any domain.
  4. **Config file `config/domains.php`** reads three env vars: `CORPORATE_DOMAIN`, `CORPORATE_WWW_DOMAIN`, `APP_DOMAIN`. Defaults are production values. Never hardcoded.
  5. **Session cookie uses `SESSION_DOMAIN=.onemember.co`** (dot prefix) to share sessions across all subdomains.
  6. **Corporate CTAs use absolute URLs** via `$appUrl` View Composer variable (`'https://' . config('domains.app')`). No `route('register')` or `route('login')` calls from corporate views.
  7. **Language switcher form action is `/locale`** (relative) to avoid cross-domain POST from the corporate domain.
  8. **Auth routes (`routes/auth.php`) required inside the `app.onemember.co` domain group** so login/register/password-reset pages only exist on the app domain.
  9. **`phpunit.xml`** sets all three domain env vars for test isolation. `TestCase::setUp()` defaults `HTTP_HOST` to `app.onemember.co` so bare-path test helpers hit the correct domain group.
- **Reason:** The original routing was path-based with a single domain, creating ambiguity between corporate and merchant application pages. Subdomain separation gives each audience a clean URL namespace, enables proper SEO canonicalization, and prepares for future independent scaling.
- **Impact:** New: `config/domains.php`, `tests/Feature/DomainRoutingTest.php`. Modified: `routes/web.php` (complete restructure), `app/Providers/AppServiceProvider.php` (View Composer), `resources/views/layouts/corporate.blade.php`, `resources/views/corporate/*.blade.php` (7 files, CTA links), `resources/views/components/language-switcher.blade.php`, `tests/TestCase.php`, `tests/Feature/ExampleTest.php`, `tests/Feature/SecurityHeadersTest.php`, `.env.example`, `phpunit.xml`.

---

## DECISION-067 — Thai-First Corporate Localization (RELEASE-2C)

- **Date:** 2026-07-05
- **Status:** Approved
- **Decision:**
  1. **`onemember.co` defaults to Thai** with no browser-language sniffing. The locale priority chain is: (1) authenticated merchant's saved locale, (2) explicit session choice via `/locale`, (3) hard default 'th'. Browser Accept-Language detection is intentionally omitted — Thai SME merchants may run English-language OS/browsers.
  2. **Language switcher** uses globe emoji (`🌐`) prefix with full language names: "🌐 English" / "🌐 ภาษาไทย". No flag icons per accessibility and political-neutrality guidelines.
  3. **Translation keys** live in `lang/en/corporate.php` and `lang/th/corporate.php` (880+ keys each). All corporate Blade views use `__('corporate.*')` — no hardcoded text.
  4. **`/locale` POST route** validates the return URL against a whitelist of known app/corporate domains (`config('domains.app')` and `config('domains.corporate')`), not just against `url('/')`, to support cross-domain locale switching from the corporate site.
  5. **SEO:** Each language has localized `<title>`, meta description, og:title, and og:description rendered from translation keys.
- **Reason:** OneMember serves Thai SMEs. The corporate website is the acquisition surface for that audience. Thai-first reduces friction for the primary customer segment; English is a secondary option for international partners and investors.
- **Impact:** Modified: `app/Http/Middleware/SetLocale.php` (removed browser detection, added hard Thai default), `app/Http/Controllers/LocaleController.php` (domain-whitelist redirect validation), `resources/views/components/language-switcher.blade.php` (globe emoji). Test fixes: `tests/Feature/MerchantAcquisitionTest.php`, `tests/Feature/DataImportExportTest.php` (explicit locale in tests that depend on English text). New: `tests/Feature/CorporateLocalizationTest.php` (21 tests).

---

## DECISION-068 — OneMember Platform Admin (RELEASE-3A)

- **Date:** 2026-07-05
- **Status:** Approved
- **Decision:**
  1. **Admin area is separate from the merchant portal** and lives at `/admin` on `app.onemember.co`. It will move to `admin.onemember.co` in a future sprint once domain routing is extended.
  2. **Admin flag is a boolean on the users table** (`is_admin BOOLEAN DEFAULT FALSE`). No separate admins table — OneMember staff register as normal users and are promoted via Artisan tinker. This keeps the auth system unified and avoids a second session mechanism.
  3. **EnsureUserIsAdmin middleware** aborts 403 for any non-admin user (including merchants and unauthenticated requests that pass the `auth` middleware). The middleware stack is: `auth → verified → admin`.
  4. **No admin UI for self-promotion** — the first admin must be created via `php artisan tinker` running `User::where('email', '...')->update(['is_admin' => true])`. This is intentional: preventing privilege escalation from within the app.
  5. **Admin views are English-only** for this sprint. Translation keys are not required in admin Blade files — Thai SME merchants do not access the admin area.
  6. **Admin layout is desktop-first** — a fixed left sidebar (240px) on `md+`, no sidebar on mobile. Tablet is usable. The merchant mobile app is completely unaffected.
- **Reason:** OneMember needs internal visibility into platform health without exposing merchant data to other merchants. The `/admin` path is the simplest safe implementation that can be promoted to a subdomain later without code changes (only route group domain config changes).
- **Impact:** New: `database/migrations/2026_07_05_000001_add_is_admin_to_users_table.php`, `app/Http/Middleware/EnsureUserIsAdmin.php`, `app/View/Components/AdminLayout.php`, `app/Http/Controllers/Admin/DashboardController.php`, `app/Http/Controllers/Admin/MerchantController.php`, `resources/views/layouts/admin.blade.php`, `resources/views/admin/dashboard.blade.php`, `resources/views/admin/merchants/index.blade.php`, `resources/views/admin/merchants/show.blade.php`, `tests/Feature/AdminTest.php` (21 tests). Modified: `app/Models/User.php` (is_admin cast + fillable), `database/factories/UserFactory.php` (is_admin default), `bootstrap/app.php` (admin middleware alias), `routes/web.php` (admin route group).

---

## DECISION-069 — Member Notification Emails (MVP-006)

- **Date:** 2026-07-05
- **Status:** Approved
- **Decision:**
  1. Members now receive three transactional emails: points-earned (after each purchase), reward-redeemed (after each redemption), and birthday greeting (when the birthday bonus is awarded).
  2. All member emails follow CTO-003: dispatched as events (`MemberPointsEarned`, `MemberRewardRedeemed`, `MemberBirthdayBonusAwarded`), handled by `MemberEmailSubscriber`, queued Mailables — never sent from controllers.
  3. Emails are sent only when (a) the member has an email address and (b) the merchant has not disabled the `member_notifications` email preference (default: enabled).
  4. Email locale follows the merchant's saved locale setting (default Thai) via `Mail::locale()`.
  5. Member emails are branded with the merchant's logo and name, "Powered by OneMember" footer — consistent with the merchant-branding pattern used in trial emails.
- **Reason:** Members previously received zero emails — a documented pilot limitation. Points/reward/birthday notifications close the loyalty feedback loop and drive repeat visits.
- **Impact:** New: 3 events, 3 mailables, `app/Listeners/MemberEmailSubscriber.php`, 3 email views, `tests/Feature/MemberNotificationEmailTest.php` (7 tests). Modified: `PurchaseController`, `RedemptionController`, `ProcessBirthdayRewards` (event dispatch), `lang/en/email.php`, `lang/th/email.php`.

---

*New decisions must be appended above this line in the format shown.*
