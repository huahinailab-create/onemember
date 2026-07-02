# Engineering-Backlog.md — Technical Work Queue

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Sprints/Backlog.md](./Sprints/Backlog.md), [Audits/AI-03-Application-Audit.md](./Audits/AI-03-Application-Audit.md), [CTO-Decisions.md](./CTO-Decisions.md), [Known-Constraints.md](./Known-Constraints.md) |

---

## Purpose

The Engineering Backlog tracks technical work that is not a product feature but is necessary for the health, correctness, and maintainability of the codebase.

This is separate from:
- **Sprint Backlog** (`Sprints/Backlog.md`) — product feature sprints
- **Parking Lot** (`02-Product/Parking-Lot.md`) — product ideas under evaluation

Items here are reviewed by the AI CTO every sprint cycle and incorporated into sprint specifications when they become high priority or are bundled with related product work.

---

## Priority Legend

| Priority | Meaning |
|---|---|
| 🔴 Critical | Production risk — must be fixed before next release |
| 🟠 High | Significant reliability or security risk |
| 🟡 Medium | Technical debt that will compound if left |
| 🟢 Low | Quality of life or future-proofing |
| ⬜ Deferred | Noted, no current timeline |

---

## Technical Debt

### TD-001 — LoyaltyProgram Nullable JSON (CTO-008)

| Field | Value |
|---|---|
| **Priority** | 🔴 Critical |
| **Scheduled Sprint** | MVP-001 |
| **Source** | AI-03 Audit H-005 |

`LoyaltyProgram.settings` uses `'settings' => 'array'` in `$casts`. If the DB column is NULL, accessing `$campaign->settings['key']` raises `ErrorException`. This is the same class of bug as BUG-002 (which fixed `Merchant.settings`).

**Fix:** Apply the CTO-008 null-safe `Attribute` accessor pattern (same as `Merchant::settings()`).

---

### TD-002 — LoyaltyProgram/Campaign Naming Split

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | MVP-007 (planned) |
| **Source** | AI-03 Audit H-002 |

The model is `LoyaltyProgram` / table is `loyalty_programs`, but all routes use `campaigns.*`, controllers use `$campaign`, and UI says "Campaigns". This split is intentional but undocumented as an ADR.

**Fix:** Create ADR-007 permanently documenting Option C (accept the split, document it). No code changes required.

---

### TD-003 — MerchantBrandingService Instantiated in Blade

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | Deferred |
| **Source** | AI-03 Audit TD-005 |

`new \App\Services\MerchantBrandingService(...)` in a Blade view bypasses the DI container. Should be moved to a View Composer registered in `AppServiceProvider`.

---

### TD-004 — MerchantProfileController Legacy Redirect

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | Deferred |
| **Source** | AI-03 Audit L-002 |

`MerchantProfileController::update()` is a legacy redirect endpoint. Should be removed after confirming no external links use it.

---

### TD-005 — Currency Hardcoded Fallback

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | Deferred — before regional expansion |
| **Source** | AI-03 Audit TD-002 |

`$merchant->currency ?? 'THB'` appears in 6+ view files. Should be `config('app.default_currency', 'THB')` or a merchant default set in the DB. Acceptable for now as Thailand-first, but must be resolved before Malaysia expansion.

---

## Security

### SEC-001 — Stripe Webhook Signature Verification

| Field | Value |
|---|---|
| **Priority** | 🟠 High |
| **Scheduled Sprint** | MVP-001 or separately |
| **Source** | AI-03 Audit SEC-005 |

`SubscriptionController::webhook()` must verify the `Stripe-Signature` header using `\Stripe\Webhook::constructEvent()`. This must be confirmed before any production billing goes live. Add a test asserting that requests with invalid signatures are rejected.

---

### SEC-002 — CSP unsafe-inline

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | Deferred — requires Alpine.js refactor |
| **Source** | AI-03 Audit M-005 |

`script-src` and `style-src` both include `'unsafe-inline'` due to inline Alpine.js configurations and inline `style=""` attributes. A future sprint should:
1. Move Alpine.js config out of inline `<script>` tags
2. Replace inline `style=""` with CSS classes
3. Enable a stricter CSP

This is a meaningful refactor across all Blade views. Do not bundle with small sprints.

---

### SEC-003 — Stripe Webhook Idempotency

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | Deferred |
| **Source** | AI-03 Audit L-006 |

Stripe retries webhooks on failure. If `customer.subscription.updated` fires twice, duplicate actions may occur. Store processed webhook event IDs in a `stripe_webhook_events` table and check before processing.

---

## Performance

### PERF-001 — Dashboard Caching

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | Deferred — after 1,000+ merchants |
| **Source** | AI-03 Audit PERF-001, PERF-002 |

Dashboard executes 8+ queries per page load (member count, campaign count, transactions, top members, etc.). Add `Cache::remember()` with 5-minute TTL for: active member count, active campaign count, subscription usage summary.

`MerchantIntelligenceService` already uses `Cache::remember()` — replicate this pattern.

---

### PERF-002 — Member Index Pagination

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | Before 500-member merchants go live |
| **Source** | AI-03 Audit M-004 |

`MemberController::index` may not be paginated. At 500+ members per merchant, loading all records on one page will be slow. Add `->paginate(50)` and a `<x-pagination>` Blade component.

---

## Testing

### TEST-001 — Campaign CRUD HTTP Tests

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | MVP-005 |
| **Source** | AI-03 Audit — Missing tests |

No Feature tests for: campaign create, campaign update, campaign configure, campaign pause, campaign archive. These are core merchant actions with no test coverage.

---

### TEST-002 — Member CRUD HTTP Tests

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | MVP-005 |
| **Source** | AI-03 Audit — Missing tests |

No Feature tests for: member create, member update, member archive, purchase recording, redemption recording.

---

### TEST-003 — Reward CRUD HTTP Tests

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | MVP-005 |
| **Source** | AI-03 Audit — Missing tests |

No Feature tests for: reward create, reward update, reward archive.

---

### TEST-004 — Onboarding Wizard HTTP Tests

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | MVP-005 |
| **Source** | AI-03 Audit — Missing tests |

No Feature tests for the 5-step onboarding wizard. New merchant registration flow is completely untested beyond auth tests.

---

### TEST-005 — ProcessExpiredTrials Command Tests

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | MVP-005 |
| **Source** | AI-03 Audit M-008 |

`ProcessExpiredTrials` Artisan command has no tests. This is production code that runs on a schedule.

---

### TEST-006 — Thai Translation Completeness

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | Before Thai merchant launch |
| **Source** | AI-03 Audit L-004 |

No test verifies that every English key in `lang/en/` has a corresponding Thai key in `lang/th/`. Missing Thai keys silently fall back to English. Add a test that compares both language file key sets.

---

## Refactoring

### REF-001 — Authorization: abort_unless → Policies

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | Deferred — Phase 2 |
| **Source** | AI-03 Audit SEC-007 |

All authorization is handled via `abort_unless()` inline checks. Laravel Policies would be more maintainable as the codebase grows. Not a priority at current team size but should be migrated before the codebase exceeds 30 controllers.

---

## Infrastructure

### INFRA-001 — Font Self-Hosting

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | Deferred |
| **Source** | AI-03 Audit L-003 |

`https://fonts.bunny.net` is an external CDN. If unavailable, the font falls back to system sans-serif. Self-host the Figtree font for production reliability.

---

## Developer Experience

### DX-001 — Migration down() Audit

| Field | Value |
|---|---|
| **Priority** | 🟡 Medium |
| **Scheduled Sprint** | Before next migration-heavy sprint |
| **Source** | AI-03 Audit DB-003 |

Per Deployment Standards, migration `down()` methods must be tested before production. No current audit exists. Before any sprint that includes migrations, the CTO should verify `down()` works correctly.

---

### DX-002 — Factory Coverage

| Field | Value |
|---|---|
| **Priority** | 🟢 Low |
| **Scheduled Sprint** | MVP-005 |
| **Source** | AI-03 Audit DB-004 |

No factories exist for: `BirthdayReward`, `AuditLog`, `DeveloperAction`. These limit test coverage for billing events and audit features.
