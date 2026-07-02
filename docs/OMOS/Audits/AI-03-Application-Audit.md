# AI-03 — OneMember Application Audit

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Complete |
| **Audit Date** | 2026-07-02 |
| **Sprint** | AI-03 |
| **Audited By** | Claude Sonnet 4.6 |
| **Related Documents** | [CTO-Decisions.md](../CTO-Decisions.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md), [02-Product/Parking-Lot.md](../02-Product/Parking-Lot.md) |

---

## Executive Summary

OneMember Phase 1 is **functionally solid and production-ready for its core loyalty use case**. The application has a mature feature set covering merchant registration, email verification, loyalty campaigns (points + stamps), member management, rewards, redemptions, CSV import/export, Stripe billing, customer self-service portal, Merchant Intelligence AI, and a comprehensive Developer Tools suite.

The codebase follows the event-driven architecture, multi-tenant scoping, and Bootstrap 5 standards established in OMOS. The test suite covers 324 tests across 25 test files with good coverage of critical paths.

**Three areas require attention before the next feature sprint:**

1. **Brand colours are not correctly applied.** Bootstrap `--bs-primary` is never overridden to `#1A2E5A`. Buttons, badges, and sidebar use Bootstrap's default blue (`#2563eb`) instead of OneMember Deep Navy. The PWA `theme-color` meta tag also uses the wrong colour.

2. **Three navigation items point to `coming-soon` pages.** Rewards, Transactions, and Reports in the sidebar lead to placeholder pages. This is expected for Phase 1 but should be documented and communicated to merchants.

3. **The model naming mismatch (LoyaltyProgram vs Campaign) creates long-term maintainability risk.** The database table is `loyalty_programs`, the model is `LoyaltyProgram`, but the UI calls these "Campaigns" and routes use `campaigns.*`. This inconsistency will cause confusion as the codebase grows.

---

## Application Health Score

**72 / 100**

| Category | Score | Notes |
|---|---|---|
| Core Functionality | 90/100 | All Phase 1 loyalty features work |
| Security | 85/100 | Strong posture; CSP uses unsafe-inline (documented) |
| Testing | 80/100 | 324 tests; missing coverage in some merchant flow areas |
| Brand Compliance | 50/100 | Wrong primary colour; sidebar and PWA not brand-compliant |
| Localization | 75/100 | Good structure; Thai translations exist but currency fallback is hardcoded 'THB' |
| UX Completeness | 60/100 | 3 nav items are stubs; no pagination on some lists |
| Technical Debt | 70/100 | LoyaltyProgram/Campaign naming split; nullable JSON on LoyaltyProgram model |
| Architecture | 85/100 | Event-driven, multi-tenant, clean separation of concerns |
| Performance | 70/100 | Basic indexes exist; N+1 risk on dashboard; no caching layer |
| Documentation | 95/100 | OMOS is comprehensive; code comments are minimal (by design) |

---

## Completed Features

### ✅ Authentication
- Registration with email + password
- Email verification (queued, polling fix from BUG-001)
- Login / logout
- Password reset via email
- Password confirmation for sensitive actions
- Profile update and account deletion

### ✅ Onboarding Wizard
- 5-step wizard: Welcome → Business Info → Business Settings → Loyalty Preference → Quick Start → Finish
- Skip onboarding option with session flag
- Redirects new merchants automatically post-registration

### ✅ Loyalty Campaigns
- Create, edit, archive campaigns
- Two programme types: Points and Stamp Card
- Campaign status lifecycle: draft → active → paused → archived
- Campaign settings (spend amount, points, stamps required, expiry, birthday)
- Configure campaign (separate from basic update)
- Soft deletes with `withTrashed()` on route binding

### ✅ Member Management
- Create, view, edit, archive members
- Member code (auto-generated) and public UUID (for portal)
- Points balance, lifetime points
- Activity timeline with filterable transaction types
- Eligible rewards card on member show
- CSV import with preview and mapping
- CSV export (members, transactions, redemptions)

### ✅ Rewards
- Create, view, edit, archive rewards per campaign
- Reward types (free item, discount, custom)
- Quantity limits (`quantity_available`, `quantity_redeemed`)
- Points required threshold
- Status lifecycle: active/inactive/archived

### ✅ Transactions and Redemptions
- Record purchases (earn points/stamps)
- Record redemptions (redeem rewards)
- Transaction types: earn, redeem, birthday, adjust, expire
- Transaction history with pagination and type filtering on member show

### ✅ Customer Self-Service Portal
- Public-facing portal page per member (UUID-based URL)
- QR code generation for members
- Portal toggle (enable/disable per member)
- QR regeneration
- Disabled portal page
- Portal card view

### ✅ Settings
- Merchant profile update (name, contact, address, social links)
- Business preferences (timezone, currency, date format, locale)
- Counter Mode toggle
- Data management (import/export)
- Email notification preferences

### ✅ Subscription and Billing
- Stripe integration (subscription, checkout, webhook)
- Trial period (configurable days, Professional-tier features during trial)
- Plan management (upgrade, downgrade, cancel, resume)
- Billing portal redirect to Stripe
- Trial banner and subscription limit warnings
- Subscription success page
- Feature gating via `canUseFeature()`

### ✅ Merchant Intelligence
- Health score (0–100, 5 bands)
- Insights (rule-based, up to 5 displayed)
- Opportunities (actionable recommendations)
- Weekly summary calculation
- Cached results (prevents repeated expensive queries)

### ✅ Developer Tools (20 sub-modules)
- Dashboard, Quick Actions, Mail Inspector, Queue Inspector
- Log Viewer, Performance, Env Inspector, Feature Flags
- Users, Members, Merchants management (with full state control)
- Demo data generation, Demo Reset
- Database commands (guarded), Danger Zone (nuke/truncate)
- Storage management, Queue management
- Double-guarded: `APP_ENV !== 'production'` AND `DEV_TOOLS_ENABLED=true`

### ✅ Infrastructure
- Health check endpoint (`/up` → JSON)
- Security headers (X-Frame-Options, CSP, HSTS, etc.)
- SetLocale middleware (reads from merchant settings)
- Tenant isolation (merchant_id scoping throughout)
- Event-driven email (10 mailable types, all queued)
- Audit logging (AuditLog model, DeveloperAction model)
- Security event logging (SecurityLogger service)
- Analytics tracking (AnalyticsService — currently internal)

---

## Incomplete Features

### 🔲 Rewards Index Page (stub)
`/rewards` → `coming-soon` view. The nav link is live but the page is a placeholder. No global rewards management interface exists.

### 🔲 Transactions Index Page (stub)
`/transactions` → `coming-soon` view. Transaction history is accessible per-member but there is no merchant-level transaction log or search.

### 🔲 Reports (stub)
`/reports` → `coming-soon` view. No analytics reporting pages exist beyond the dashboard metrics.

### 🔲 Birthday Bonus Automation
`BirthdayReward` model exists. Birthday reward events and `birthday_enabled` campaign settings are referenced but no scheduled job processes birthday rewards automatically. The `BirthdayReward` model has relationships but no background job creates transactions for it.

### 🔲 Point Expiry Scheduling
Campaign settings support `expiration_type` and `expiration_duration` but there is no scheduled Artisan command or Job that processes point expiry. `TransactionType::Expire` exists but is never triggered automatically.

### 🔲 Win-back / Automated Campaigns
No automated outreach system exists. Merchants cannot schedule "haven't visited in 45 days" campaigns. This is the most impactful missing feature for merchant retention.

### 🔲 Member Notifications (Email to Members)
No system sends points-earned, reward-available, or birthday emails to members. All email infrastructure is merchant-facing (billing, security). Member-facing notification emails are not yet built.

### 🔲 Subscription Enforced Limits
`SubscriptionService::usageSummary()` and `canCreateCampaign()` exist. The member limit check is calculated but enforcement (blocking member creation when limit is reached) was not verified to be wired up everywhere in the flow.

### 🔲 Counter Mode UI
`CounterModeController` and the toggle route exist. The settings toggle exists. But no Counter Mode interface (the simplified staff-facing view) was found in the Blade views beyond the toggle itself.

---

## Critical Issues

**None** — no issues preventing the application from functioning.

---

## High Priority Improvements

### H-001 — Bootstrap Primary Colour Not Overridden (Brand Non-Compliance)

**Area:** Brand / CSS  
**File:** `resources/css/app.css`

`--bs-primary` and `--bs-primary-rgb` are never set to OneMember Deep Navy (`#1A2E5A`). Bootstrap's default primary blue (`#2563eb` / `38 99 235`) is used throughout for `.btn-primary`, `.badge.bg-primary`, `.text-primary`, `.border-primary`. The sidebar uses `--om-sidebar-bg: #1e293b` (Slate 800) rather than `#1A2E5A`.

**Fix required:**
```css
:root {
    --bs-primary:        #1A2E5A;
    --bs-primary-rgb:    26,46,90;
    --om-sidebar-bg:     #1A2E5A;
    --om-sidebar-hover:  #1e3a6e;
    --om-sidebar-border: #243d6b;
}
```
Also: the PWA `theme-color` meta tag uses `#1d4ed8` (wrong). Should be `#1A2E5A`.
**Also:** Accent colour `#FF1585` is not defined in CSS as a custom property. It should be `--om-accent: #FF1585` and used for CTA variants.

---

### H-002 — `LoyaltyProgram` Model Named `Campaign` in Routes/UI (Naming Split)

**Area:** Architecture / Maintainability  
**Files:** Routes, Controllers, Views, Model

The model is `LoyaltyProgram` / table is `loyalty_programs`, but all routes use `campaigns.*`, all controllers type-hint `LoyaltyProgram $campaign`, and all UI labels say "Campaign". This creates a persistent cognitive split between "what the code calls it" and "what the product calls it."

**Options (require ADR):**
- Option A: Rename model to `Campaign`, table to `campaigns` (breaking migration, high risk)
- Option B: Create `Campaign` as a type alias or accessor on `LoyaltyProgram` (maintains DB schema)
- Option C: Accept the split permanently and document it in the Glossary as `LoyaltyProgram = Campaign` (lowest cost, lowest disruption)

**Recommendation:** Document Option C in the Glossary and CTO-Decisions (cost of renaming exceeds benefit at current scale). Create ADR-007.

---

### H-003 — Birthday Automation Not Wired to Scheduled Jobs

**Area:** Incomplete Feature  
**Files:** `app/Models/BirthdayReward.php`, `app/Console/Commands/ProcessExpiredTrials.php`

`ProcessExpiredTrials` handles trial expiry. There is no equivalent command for birthday rewards. The `birthday_enabled` campaign setting and `BirthdayReward` model exist but birthdays are never processed automatically.

**Fix required:** Create `ProcessBirthdayRewards` Artisan command + schedule it daily. This is the single highest merchant value feature that is mostly built but not connected.

---

### H-004 — Point Expiry Not Automated

**Area:** Incomplete Feature  
**Files:** Campaign settings (`expiration_type`, `expiration_duration`), `TransactionType::Expire`

Merchants can configure point expiry, members expect it to work, but no job processes expiry. Points never expire regardless of settings.

**Fix required:** Create `ProcessPointExpiry` Artisan command + schedule it daily.

---

### H-005 — LoyaltyProgram Settings JSON Column Uses Default Array Cast

**Area:** Technical Debt / Bug Risk  
**File:** `app/Models/LoyaltyProgram.php`

```php
// LoyaltyProgram.php line ~30
protected $casts = [
    ...
    'settings' => 'array',  // ← default array cast, NOT the null-safe Attribute accessor
];
```

The `Merchant` model was fixed in BUG-002 with a custom `Attribute` accessor that returns `[]` for NULL. `LoyaltyProgram` still uses the default `'array'` cast. If any `loyalty_programs.settings` row is NULL, accessing `$campaign->settings['key']` will raise `ErrorException`. This is the same class of bug as BUG-002.

**Fix required:** Apply CTO-Decisions CTO-008 pattern to `LoyaltyProgram::settings()`.

---

## Medium Priority Improvements

### M-001 — No Member-Facing Email Notifications

Members receive no emails. There is no "you earned 50 points", "your reward is ready", or "happy birthday" email to members. The email infrastructure (Events, Listeners, Mailable pattern) is ready; the specific Mailables for member notifications do not exist.

**Priority:** Required before full Phase 1 merchant value claim. Members who get no feedback after joining have lower retention.

---

### M-002 — Dashboard N+1 Risk

**File:** `app/Http/Controllers/DashboardController.php`

`$recentActivity` eager-loads `member` and `loyaltyProgram`. However `$topMembers` and `$activeCampaigns` do not have their relationships pre-loaded. If views access relationships on these collections, N+1 queries will occur.

The `withCount()` on `$activeCampaigns` is correct. Verify no additional relationship access happens in `dashboard.blade.php` without eager loading.

---

### M-003 — Sidebar Navigation Missing Core Sections

The sidebar navigation (in `resources/views/layouts/app.blade.php`) includes only: Dashboard, Members, Campaigns, Rewards (stub), Transactions (stub), Reports (stub), Settings.

**Missing navigation items:**
- Subscription management (`/subscription`) — accessible only via settings or a direct URL
- Data import/export — nested under settings, not prominent

---

### M-004 — No Pagination on Members Index

**File:** `resources/views/members/index.blade.php`

Member index is not paginated in the controller (`MemberController::index` was not fully reviewed but should be checked). At 500+ members, loading all records on one page will cause performance issues.

---

### M-005 — CSP Uses `unsafe-inline`

**File:** `app/Http/Middleware/SecurityHeaders.php`

The CSP includes `'unsafe-inline'` for both `script-src` and `style-src`. This is documented in the middleware comment with a note that it's required due to inline scripts and `style=""` attributes throughout templates.

This weakens XSS protection. A future sprint should move Alpine.js configurations out of inline scripts and eliminate inline `style=""` attributes where possible to enable a stricter CSP.

---

### M-006 — No Automated Win-back / Drip Campaigns

The highest-value merchant-facing feature for the roadmap. Merchants who see automated "hasn't visited in 45 days" member targeting are dramatically more likely to retain their subscription. This requires:
1. A scheduled job scanning for inactive members
2. An email notification to the merchant (not the member) or a dashboard alert
3. A future member-facing notification

---

### M-007 — CounterMode Has No UI

`CounterModeController::toggle()` and the settings toggle exist. The setting is saved. But there is no Counter Mode interface — a simplified staff-facing view for recording sales without full merchant dashboard access. The feature is partially built but unusable.

---

### M-008 — `ProcessExpiredTrials` Command — No Tests

**File:** `app/Console/Commands/ProcessExpiredTrials.php`

The trial expiry command exists but no Feature test verifies it actually marks trials as expired correctly. This is production code that runs on a schedule with no test coverage.

---

## Low Priority Improvements

### L-001 — Welcome Page is Generic Laravel

`resources/views/welcome.blade.php` is the public marketing landing page. It appears to still be the Laravel default or a minimal placeholder. This is the first page unregistered visitors see.

### L-002 — `MerchantProfileController` Is a Legacy Redirect Target

`MerchantProfileController::update()` is still registered as a route (`merchant.profile.update`) and a redirect exists from `merchant.profile.edit` to `settings`. The old controller should be removed after confirming no external links point to it.

### L-003 — Font CDN External Dependency

`https://fonts.bunny.net` is loaded in the layout. This is an external CDN dependency. If `fonts.bunny.net` is unavailable, the font falls back to system sans-serif. Consider self-hosting Figtree for production reliability.

### L-004 — No `lang/th/` Completeness Verification

Both `lang/en/` and `lang/th/` exist with 18 files each. However, no test verifies that every English key has a corresponding Thai translation. Missing Thai keys silently fall back to English (correct behaviour) but are invisible without an audit.

### L-005 — `RedemptionStatus` Enum Exists but Redemption Status Not Used in UI

`app/Enums/RedemptionStatus.php` exists. Redemptions appear to be stored without a status field being prominently displayed or filterable in the UI.

### L-006 — Stripe Webhook Has No Idempotency Check

`SubscriptionController::webhook()` handles Stripe webhooks. Standard practice is to record processed webhook event IDs to prevent duplicate processing. A Stripe retry of a `customer.subscription.updated` event could trigger duplicate actions.

---

## Technical Debt

### TD-001 — LoyaltyProgram/Campaign Naming (see H-002)
The most significant long-term technical debt item.

### TD-002 — Settings THB Hardcoded as Default Fallback
Throughout views, `$merchant->currency ?? 'THB'` is used as a fallback. While `'THB'` is correct for Thailand-first, this hardcoded string should be `config('app.default_currency', 'THB')` or set as a merchant default in the DB so it can be changed per region.

### TD-003 — `LoyaltyProgram->settings` Nullable JSON Risk (see H-005)
The default `'array'` cast does not protect against NULL values from the database.

### TD-004 — No `down()` Migration Audit
Migrations have not been audited for working `down()` methods. The standard (CTO-006 / Deployment Standards) requires testable rollback. This should be verified before any production data migration.

### TD-005 — `MerchantBrandingService` Instantiated in Blade with `new`
```php
// resources/views/layouts/app.blade.php
$__branding = new \App\Services\MerchantBrandingService(Auth::user()?->merchant);
```
Service instantiation in Blade views bypasses the DI container and makes the service untestable in that context. Should be moved to a view composer or the layout's `@php` block avoided in favour of a View Composer registered in `AppServiceProvider`.

---

## UX Recommendations

### UX-001 — Stub Pages Should Explain Phase and Expected Date
The three `coming-soon` pages (Rewards, Transactions, Reports) show a generic placeholder. They should explain: "This feature is planned for Phase 1 Q3 2026" or similar, so merchants understand it is coming, not broken.

### UX-002 — Subscription Page Discoverability
The subscription management page (`/subscription`) is not linked from the navigation. Merchants can only reach it via: the trial banner, a settings link, or by typing the URL. Add a "Subscription" link to the sidebar under Account.

### UX-003 — Dashboard Empty State Improvement
When a merchant has no members and no campaign, the dashboard shows empty state cards. The CTA hierarchy could be clearer: "Create your first Campaign" should be the single primary action, not one of four equal options.

### UX-004 — Member Show Page Rewards Card Placement
The eligible rewards card appears at the bottom of a long page. For Counter Mode scenarios, it should be near the top alongside the Points Balance.

### UX-005 — No Confirmation on Archive Actions
Archiving a member, campaign, or reward has no confirmation dialog. Accidental archives are possible, especially on mobile. A confirmation modal (Bootstrap) should guard these actions.

---

## Security Observations

### SEC-001 — DevTools Double Guard is Correct ✅
`DevToolsAccess` middleware correctly checks both `APP_ENV !== 'production'` and `DEV_TOOLS_ENABLED=true`. Returns 404 (not 403) to avoid fingerprinting. Correct.

### SEC-002 — All Merchant Routes Behind `['auth', 'verified']` ✅
All sensitive routes are correctly guarded. No routes inadvertently expose merchant data to unauthenticated users.

### SEC-003 — Tenant Isolation via `abort_unless()` Pattern ✅
`abort_unless($resource->merchant_id === $request->user()->merchant?->id, 403)` is consistently applied on resource show/edit/delete actions. `TenantIsolationTest` validates this.

### SEC-004 — CSP `unsafe-inline` (See M-005)
Not a critical issue but should be addressed in a future sprint.

### SEC-005 — Stripe Webhook Signature Verification — Unconfirmed
`SubscriptionController::webhook()` was not fully reviewed. Stripe webhooks must verify the `Stripe-Signature` header using `\Stripe\Webhook::constructEvent()`. This must be confirmed before production billing.

### SEC-006 — Rate Limiting on Auth Routes ✅
`throttle:6,1` is applied to `verification.verify` and `verification.send`. Login has `throttle:10,1` via `LoginRequest`. Correct.

### SEC-007 — No Authorization Policies (Intentional for Now)
Authorization is handled via `abort_unless()` inline checks rather than Laravel Policies. This is acceptable for a single-developer team but creates risk as the codebase grows. Consider migrating to Policies in a dedicated sprint.

---

## Performance Observations

### PERF-001 — No Cache Layer
No Redis or file-based caching is used beyond `MerchantIntelligenceService` which uses `Cache::remember()`. Dashboard queries are executed fresh on every page load.

**Recommendation:** Add cache to: active member count, campaign list, subscription usage summary. TTL of 5 minutes is appropriate.

### PERF-002 — Dashboard Executes 8+ DB Queries Per Load
Each of `$totalActiveMembers`, `$activeCampaignCount`, `$redeemedToday`, `$pointsIssuedToday`, `$recentActivity`, `$topMembers`, `$activeCampaigns` are separate queries. At current scale this is fine. At 10,000+ members per merchant, query time will increase.

### PERF-003 — `add_performance_indexes` Migration Exists ✅
`2026_06_29_000001_add_performance_indexes.php` was found — confirming indexes have been added to frequently queried columns. Good.

---

## Database Observations

### DB-001 — `loyalty_programs.settings` Not Null-Safe ✅ Risk
Standard `'array'` cast in `LoyaltyProgram`. See H-005.

### DB-002 — No `stamps_balance` Column — Points Used for Stamps
The `Member` model uses `total_points` for both points-based and stamp-based programmes. Stamps are stored as integer points. This works but creates confusion: a member in a stamp programme with `total_points: 7` has 7 stamps, not 7 monetary points. The field name does not match the semantic.

### DB-003 — Migrations Have No Explicit Down() Audit
Per Deployment Standards, `down()` should be tested before production migration. This should be a checklist item on every sprint that includes migrations.

### DB-004 — Factories Cover Only Core Models
7 factory files exist (User, Merchant, Member, LoyaltyProgram, Reward, Transaction, Redemption). No factories for: BirthdayReward, AuditLog, DeveloperAction. This limits test coverage for billing and audit features.

---

## Brand Compliance

| Item | Status | Issue |
|---|---|---|
| Official logo (SVG) | ✅ | `application-logo.blade.php` uses the correct brand SVG |
| Merchant logo via `MerchantBrandingService` | ✅ | Falls back to merchant name if no logo |
| Bootstrap 5 | ✅ | No Tailwind detected |
| `--bs-primary` override | ❌ | Not overridden — Bootstrap default blue used throughout |
| Sidebar background | ❌ | `#1e293b` (Slate-800) instead of `#1A2E5A` |
| PWA `theme-color` | ❌ | `#1d4ed8` (wrong) should be `#1A2E5A` |
| Body background | ⚠️ | `#f1f5f9` (Slate-50) instead of `#F0F0F4` (Cloud) |
| Accent `#FF1585` | ⚠️ | Not defined as CSS variable; not used in current UI |
| Responsive layout | ✅ | Mobile-responsive with collapsible sidebar |
| Bootstrap Icons only | ✅ | No Font Awesome or other icon libraries |
| Typography (Figtree) | ✅ | Correct font via `fonts.bunny.net` |

---

## Localization Readiness

| Item | Status | Notes |
|---|---|---|
| `__()` usage in views | ✅ Partial | Dashboard: 39 uses. Not all views equally covered. |
| `lang/en/` files | ✅ | 18 files covering all major feature areas |
| `lang/th/` files | ✅ | 18 files — parallel structure |
| Currency hardcoded | ⚠️ | `?? 'THB'` fallback in 6+ view files |
| Date formatting | ⚠️ | Not verified to use merchant timezone consistently |
| SetLocale middleware | ✅ | Reads from `merchant.settings['locale']` |
| Validation messages | ✅ | `lang/en/validation.php` and `lang/th/validation.php` exist |
| Hardcoded English strings | ⚠️ | Some Blade views have hardcoded strings — audit needed |

---

## Test Coverage

| Area | Tests | Coverage Assessment |
|---|---|---|
| Authentication | 7 files, ~50 tests | Strong — all auth flows covered |
| Email Verification | 11 tests (BUG-001) | Complete |
| Dashboard Links | 12 tests (BUG-002) | Complete for fixed bugs |
| Tenant Isolation | ~15 tests | Strong — cross-tenant access blocked |
| Security Headers | ~8 tests | Complete |
| Security Logging | ~20 tests | Comprehensive |
| Customer Portal | ~15 tests | Good coverage |
| Data Import/Export | ~25 tests | Comprehensive |
| Merchant Intelligence | ~20 tests | Good coverage |
| Merchant Branding | ~15 tests | Good coverage |
| Stripe Billing | ~20 tests | Good coverage |
| Mobile Experience | ~15 tests | UI responsiveness tested |
| Email Infrastructure | ~25 tests | Comprehensive |
| DevTools | 6 files, ~35 tests | Comprehensive |
| **Missing** | — | — |
| Birthday automation | ❌ | `BirthdayReward` model untested |
| Point expiry | ❌ | No expiry job tests |
| Member notifications | ❌ | No member email tests |
| `ProcessExpiredTrials` | ❌ | Command not tested end-to-end |
| Onboarding wizard | ❌ | No HTTP tests for onboarding flow |
| Campaign CRUD | ❌ | No HTTP tests for campaign create/update |
| Member CRUD | ❌ | No HTTP tests for member create/update |
| Reward CRUD | ❌ | No HTTP tests for reward create/update |
| Subscription enforcement | ❌ | Feature limits not tested end-to-end |

---

## Suggested Development Order

Based on merchant value, technical risk, and Phase 1 exit criteria:

| Priority | Item | Rationale |
|---|---|---|
| 1 | **H-001 — Brand colour fix** | Low effort, high visibility, blocks brand compliance |
| 2 | **H-005 — LoyaltyProgram nullable JSON** | Bug waiting to happen; same pattern as BUG-002 |
| 3 | **H-003 — Birthday automation** | ~80% built already; highest merchant retention feature |
| 4 | **H-004 — Point expiry automation** | Merchant-promised feature that silently doesn't work |
| 5 | **M-001 — Member notifications** | Merchants need to prove value to members |
| 6 | **M-007 — Counter Mode UI** | Feature is built but unusable |
| 7 | **UX-005 — Archive confirmation dialogs** | Low risk of data loss prevention |
| 8 | **CRUD tests (campaigns, members, rewards)** | Critical gaps in test coverage |
| 9 | **M-006 — Win-back campaign alerts** | Merchant retention cornerstone |
| 10 | **L-001 — Welcome page** | Public face of the product |

---

## Recommended Next Sprint

**Sprint AI-04 — Brand Fix + Critical Bug Prevention**

Scope:
1. Fix `--bs-primary` and `--bs-primary-rgb` in `app.css` to `#1A2E5A` / `26,46,90`
2. Fix sidebar `--om-sidebar-bg` to `#1A2E5A`
3. Fix PWA `theme-color` to `#1A2E5A`
4. Fix body background to `#F0F0F4`
5. Add `--om-accent: #FF1585` CSS variable
6. Apply CTO-008 null-safe pattern to `LoyaltyProgram::settings()`
7. Write regression tests for items 1–6
8. Document LoyaltyProgram/Campaign naming decision in ADR-007

This sprint is low-risk (CSS variables and one model accessor), high-value (brand compliance + bug prevention), and has clear, testable outcomes.

**Sprint AI-05 — Birthday + Expiry Automation** (after AI-04 approved)

Scope:
1. `ProcessBirthdayRewards` Artisan command
2. `ProcessPointExpiry` Artisan command
3. Schedule both in `routes/console.php`
4. Tests for both commands
5. Member notification emails (points earned, reward available)
