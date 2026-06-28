# 12 — SaaS Architecture

> **Last updated:** 2026-06-28  
> **Status:** Reflects current production codebase as of Sprint 5.1  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md)

---

## 1. Technology Stack

| Layer | Technology | Version | Decision |
|-------|-----------|---------|---------|
| Language | PHP | 8.3+ | — |
| Framework | Laravel | 13.x | — |
| Frontend UI | Bootstrap | 5.3 | DECISION-002 |
| Frontend JS | Alpine.js | 3.x | DECISION-002 |
| Auth scaffold | Laravel Breeze | Latest | — |
| Asset bundler | Vite | 8.x | — |
| Icon library | Bootstrap Icons | Latest | DECISION-002 |
| Database (dev) | SQLite | 3.x | DECISION-003 |
| Database (prod) | MySQL | 8.x | DECISION-003 |
| Fonts | Figtree (bunny.net) | — | — |

---

## 2. Multi-Tenancy Model

OneMember uses a **single-database, shared-schema** multi-tenancy pattern.

Each tenant (merchant) is identified by a `merchant_id` foreign key on every domain table. Tenant isolation is enforced at the application layer:

1. Every authenticated user has exactly one `merchants` record (via `users.id → merchants.user_id`).
2. Every controller resolves the current merchant from `$request->user()->merchant`.
3. Ownership is verified with `abort_unless($campaign->merchant_id === $merchant->id, 403)` before any read/write.

There is no row-level security at the database layer. All isolation is in Laravel application code.

---

## 3. Domain Model

```
users
  └── merchants (1:1 via user_id)
       ├── loyalty_programs (1:N)  ← "Campaigns" in UI (DECISION-034)
       │    └── rewards (1:N)
       └── members (1:N)
            ├── transactions (1:N)   ← immutable ledger
            └── redemptions (1:N)    ← via transactions.id
```

### 3.1 Key Models

| Model | Table | Soft Deletes | Key Enums |
|-------|-------|-------------|-----------|
| `User` | `users` | No | — |
| `Merchant` | `merchants` | No | `MerchantStatus` |
| `Member` | `members` | Yes | `MemberStatus` |
| `LoyaltyProgram` | `loyalty_programs` | Yes | `CampaignStatus`, `LoyaltyProgramType` |
| `Reward` | `rewards` | Yes | `RewardType`, `RewardStatus` |
| `Transaction` | `transactions` | No | `TransactionType` |
| `Redemption` | `redemptions` | No | `RedemptionStatus` |
| `AuditLog` | `audit_logs` | No | — |
| `BirthdayReward` | `birthday_rewards` | No | `BirthdayRewardType` |

### 3.2 PHP Enums (app/Enums/)

All domain types are modelled as PHP 8.1+ backed enums:

- `CampaignStatus` — Active, Paused, Archived
- `LoyaltyProgramType` — Points, Stamps
- `MemberStatus` — Active, Inactive, Archived
- `MerchantStatus` — Active, Inactive, Suspended
- `RewardType` — FreeItem, Discount, Custom, (+ birthday types)
- `RewardStatus` — Active, Paused, Archived
- `TransactionType` — Earn, Redeem, Adjustment, Expiry
- `RedemptionStatus` — Pending, Used, Expired, Cancelled
- `BirthdayRewardType` — Points, Stamps, Custom

---

## 4. Request Lifecycle

```
Browser Request
  → Laravel Router (routes/web.php)
  → auth middleware (Breeze session)
  → Form Request validation (app/Http/Requests/)
  → Controller (app/Http/Controllers/)
    → Eloquent Model (app/Models/)
      → SQLite (dev) / MySQL (prod)
  → Blade View (resources/views/)
    → Bootstrap 5 + Alpine.js + Bootstrap Icons
  → Vite-bundled CSS/JS (public/build/)
```

---

## 5. Directory Structure

```
app/
  Enums/              — PHP 8.1+ backed enums for all domain types
  Http/
    Controllers/      — Thin controllers; delegate to models
    Requests/         — All validation via Form Requests (DECISION)
  Models/             — Eloquent models with relationships, casts, scopes

resources/
  views/
    layouts/
      app.blade.php       — Main admin layout (sidebar + topbar)
      wizard.blade.php    — Distraction-free onboarding layout
    dashboard.blade.php
    members/
    campaigns/
    onboarding/
    settings/

routes/
  web.php             — All application routes (no API routes yet)
  auth.php            — Breeze authentication routes

database/
  migrations/         — Timestamped migrations (chronological)

docs/                 — Product and architecture documentation
```

---

## 6. Frontend Architecture

### 6.1 Layout System

Two Blade layouts:

| Layout | File | Usage |
|--------|------|-------|
| Admin app | `layouts/app.blade.php` | All authenticated pages with sidebar |
| Wizard | `layouts/wizard.blade.php` | Onboarding wizard (distraction-free) |

`layouts/app.blade.php` uses the Laravel Blade **component slot** pattern (`$slot`).  
`layouts/wizard.blade.php` uses the **`@extends` / `@section`** pattern for six onboarding steps.

### 6.2 Bootstrap 5 Conventions

- Cards for all content panels
- Bootstrap grid (`row`, `col-*`, `g-3`) for responsive layouts
- `btn-primary` / `btn-outline-primary` / `btn-sm` for actions
- `badge` + status colour coding for enums
- `alert-success` / `alert-danger` for flash messages
- `nav-tabs` + `tab-pane` for tabbed interfaces
- `progress` for wizard progress bars
- `list-group` for ranked items

### 6.3 Alpine.js Usage

Alpine.js is used for lightweight client-side interactivity only:

| Pattern | Used In |
|---------|---------|
| `x-data`, `:class`, `x-show` | Sidebar collapse, conditional fields |
| `x-cloak` | Hide un-initialised elements (`[x-cloak] { display: none !important; }` in app.css) |
| `x-model` | Form field binding for reactive show/hide |
| Inline `x-data="{expType: '...'}"` | Preferences tab — expiry type conditional fields |

### 6.4 Tab Activation Pattern

Tabs in multi-tab views (Settings, Member profile) are activated via URL param:

```javascript
document.addEventListener('DOMContentLoaded', function () {
    var tab = new URLSearchParams(window.location.search).get('tab') || 'profile';
    var el  = document.getElementById('tab-' + tab);
    if (el) bootstrap.Tab.getOrCreateInstance(el).show();
});
```

Redirect-with-errors always appends `?tab=X` so validation errors re-open the correct tab.

---

## 7. Data Patterns

### 7.1 Campaign Configuration (`settings` JSON column)

Campaign business rules are stored in `loyalty_programs.settings` as a JSON object. The schema varies by `LoyaltyProgramType`:

**Points campaign settings:**
```json
{
  "spend_amount": 100,
  "points_awarded": 1,
  "expiration_type": "never|months|years",
  "expiration_duration": 12,
  "birthday_enabled": true,
  "birthday_valid_days_before": 7,
  "birthday_valid_days_after": 7
}
```

**Stamps campaign settings:**
```json
{
  "stamps_required": 10,
  "reward_description": "Complete your stamp card to claim your reward."
}
```

### 7.2 Merchant Settings (`settings` JSON column)

Merchant preferences are stored in `merchants.settings`:
```json
{
  "date_format": "d/m/Y",
  "default_expiration_type": "never|months|years",
  "default_expiration_duration": 12,
  "default_birthday_enabled": true,
  "onboarding_step": 5
}
```

### 7.3 Transaction Ledger (Immutable)

`transactions` has no `updated_at` column and no edit/delete routes. Every financial event creates a new row. This is an intentional immutable audit trail.

### 7.4 Redemption Pattern

Redemption is immediate (DECISION-035):
1. `Transaction` created first (`type = Redeem`) — FK is non-nullable.
2. `Redemption` created with `transaction_id`, `status = Used`, `redeemed_at = now()`.

### 7.5 Soft Delete + Route Binding

Soft-deleted records (Members, LoyaltyPrograms, Rewards) are accessible via `resolveRouteBinding` override:
```php
public function resolveRouteBinding($value, $field = null)
{
    return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
}
```

---

## 8. Authentication & Onboarding

### 8.1 Authentication

Laravel Breeze handles:
- Login / Logout (session-based)
- Registration
- Password reset (email-based)
- Email verification

### 8.2 Onboarding Wizard State

Onboarding state is tracked by two fields on `merchants`:

| Field | Type | Meaning |
|-------|------|---------|
| `settings['onboarding_step']` | integer | Furthest step reached (0–5) |
| `onboarding_completed_at` | timestamp nullable | null = not complete; set = wizard done |

Dashboard guard:
```php
if ((!$merchant || is_null($merchant->onboarding_completed_at)) && !session('onboarding_skipped')) {
    return redirect()->route('onboarding.index');
}
```

---

## 9. Route Structure

| Prefix | Controller | Purpose |
|--------|-----------|---------|
| `/dashboard` | `DashboardController` | KPI dashboard |
| `/onboarding/*` | `OnboardingController` | 6-step wizard (12 routes) |
| `/members/*` | `MemberController` | Member CRUD + purchase/redemption |
| `/campaigns/*` | `CampaignController` | Campaign CRUD + configure/pause |
| `/campaigns/{campaign}/rewards/*` | `RewardController` | Reward CRUD (nested) |
| `/settings*` | `SettingsController` | 4-tab settings workspace |
| `/profile` | `ProfileController` | Breeze user profile (name/email) |
| `/reports` | closure | Coming soon placeholder |
| `/transactions` | closure | Coming soon placeholder |
| `/rewards` | closure | Coming soon placeholder |

All routes behind `auth` middleware. No public API routes exist yet.

---

## 10. Production Infrastructure (Planned)

| Component | Target | Notes |
|-----------|--------|-------|
| Web server | Nginx or Laravel Octane | TBD |
| PHP-FPM | PHP 8.3 | — |
| Database | MySQL 8.x | DECISION-003 |
| Cache | Redis | Sessions, cache |
| Queues | Redis + `queue:work` | For email, scheduled jobs |
| Storage | S3 or local disk | Logo uploads |
| Email | Mailgun or SES | Transactional mail |
| Payments | Stripe | Subscription billing |
| Error tracking | Sentry or Telescope | TBD |
| SSL | Let's Encrypt | Auto-renewing |
