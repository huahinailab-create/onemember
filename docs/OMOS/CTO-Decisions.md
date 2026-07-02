# CTO Decision Log

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [CEO-Decisions.md](./CEO-Decisions.md), [12-ADR/README.md](./12-ADR/README.md), [11-Standards/](./11-Standards/), [Known-Constraints.md](./Known-Constraints.md) |

---

## Purpose

This document records significant technical, architectural, and engineering governance decisions made by the ChatGPT CTO. It complements CEO-Decisions.md (business decisions) and the ADR system (formal architecture records).

Entries here capture decisions that are too operational for an ADR but too significant to leave undocumented — engineering standards, tooling choices, process decisions, and architectural guidance that shapes every sprint.

---

## Decision Log

### CTO-001 — Laravel as the Application Framework

| Field | Value |
|---|---|
| **Decision ID** | CTO-001 |
| **Date** | Pre-OMOS |
| **Status** | Approved |
| **Related** | [ADR-004](./12-ADR/ADR-004-Laravel-Architecture.md) |

**Decision:** OneMember is built on Laravel (currently 13.x). PHP 8.3+ minimum. No framework migration will be considered before Phase 3.

**Reason:** Laravel provides a comprehensive, well-documented, convention-over-configuration environment suited to a small team building a SaaS product. Its built-in features (queues, events, migrations, Eloquent ORM, Breeze auth scaffold) accelerate development without custom infrastructure. The community is large and the ecosystem is mature.

**Impact:** All backend code is Laravel-native. No Node.js, no Symfony, no custom PHP. Third-party packages must be Laravel-compatible.

---

### CTO-002 — Bootstrap 5 as the Only Frontend Framework

| Field | Value |
|---|---|
| **Decision ID** | CTO-002 |
| **Date** | Pre-OMOS |
| **Status** | Approved |
| **Related** | [ADR-005](./12-ADR/ADR-005-Bootstrap-5-Standard.md), [11-Standards/Bootstrap-Standards.md](./11-Standards/Bootstrap-Standards.md) |

**Decision:** Bootstrap 5 is the only permitted CSS framework. Tailwind CSS is explicitly not permitted. No mixing of frameworks. Alpine.js is permitted for lightweight interactivity.

**Reason:** Bootstrap 5 provides a consistent, responsive design system that a single developer can maintain without a dedicated designer. It integrates with Laravel Blade naturally. Tailwind requires a build pipeline and class management that adds complexity without proportional benefit at this stage.

**Impact:** All new UI must use Bootstrap 5 utility classes and components. Code reviewers reject Blade views that include Tailwind classes.

---

### CTO-003 — Event-Driven Email Architecture

| Field | Value |
|---|---|
| **Decision ID** | CTO-003 |
| **Date** | Pre-OMOS |
| **Status** | Approved |
| **Related** | [ADR-004](./12-ADR/ADR-004-Laravel-Architecture.md) |

**Decision:** Controllers never send email directly. All email is dispatched via Laravel Events and handled by queued Listeners. Email notifications extend `VerifyEmail` or `Mailable` with `ShouldQueue`.

**Reason:** Direct email in controllers couples the HTTP response time to email delivery. If the mail server is slow, the user waits. If it fails, the request fails. Queued events decouple these concerns and make the system more reliable and testable.

**Impact:** `Mail::send()` in a controller is a code review failure. Every email path must have a corresponding Event, Listener, and queued Mailable.

---

### CTO-004 — Database Session Driver in Production

| Field | Value |
|---|---|
| **Decision ID** | CTO-004 |
| **Date** | Pre-OMOS |
| **Status** | Approved |
| **Related** | [Known-Constraints.md](./Known-Constraints.md) |

**Decision:** `SESSION_DRIVER=database` in production. `QUEUE_CONNECTION=database` in production. `sync` queue driver is used in tests only.

**Reason:** Database sessions are auditable, shareable across multiple application servers (future), and do not require Redis infrastructure at the current scale. The database queue is reliable and visible — jobs can be inspected and retried.

**Impact:** Session and queue tables must exist and be migrated before production deployment. `php artisan queue:work` must be running as a daemon in production.

---

### CTO-005 — Single-Database Multi-Tenancy

| Field | Value |
|---|---|
| **Decision ID** | CTO-005 |
| **Date** | Pre-OMOS |
| **Status** | Approved |
| **Related** | [ADR-004](./12-ADR/ADR-004-Laravel-Architecture.md), [11-Standards/Database-Standards.md](./11-Standards/Database-Standards.md) |

**Decision:** All merchants share a single database. Every merchant-scoped table has a `merchant_id` foreign key. No separate databases or schemas per tenant.

**Reason:** Separate databases per tenant require infrastructure management that is expensive and operationally complex at this scale. Single-database multi-tenancy is simpler to back up, migrate, and manage. The security boundary is enforced in application code by scoping every query to `merchant_id`.

**Impact:** Every model touching merchant data must scope its queries. Code review must verify `merchant_id` scoping on every new query. Cross-tenant data leakage is the primary security risk of this architecture — tests must verify isolation.

---

### CTO-006 — Zero Test Failures Before Any Commit

| Field | Value |
|---|---|
| **Decision ID** | CTO-006 |
| **Date** | 2026-07-02 |
| **Status** | Approved |
| **Related** | [EXECUTE.md](./EXECUTE.md), [11-Standards/Testing-Standards.md](./11-Standards/Testing-Standards.md) |

**Decision:** `php artisan test` must pass with zero failures before any commit. No exceptions. No `--filter` to avoid failing tests. No deleting failing tests.

**Reason:** BUG-001 and BUG-002 both existed in production because they were not caught by the test suite. A failing test that is ignored is worse than no test — it gives false confidence. The test suite is only valuable if it is always green.

**Impact:** Claude Developer stops and diagnoses any failing test before committing. Sprint completion is not reported until all tests pass.

---

### CTO-007 — Resend as Email Provider

| Field | Value |
|---|---|
| **Decision ID** | CTO-007 |
| **Date** | Pre-OMOS |
| **Status** | Approved |
| **Related** | [11-Standards/Deployment-Standards.md](./11-Standards/Deployment-Standards.md) |

**Decision:** Resend is the production email provider (`MAIL_MAILER=resend`). `array` driver is used in tests. `log` driver may be used in local development.

**Reason:** Resend provides reliable transactional email delivery with a modern API, good deliverability, and a developer-friendly dashboard. It integrates with Laravel natively via the Resend Laravel package.

**Impact:** No other email provider may be configured in production without a CTO decision. `RESEND_API_KEY` must be provisioned in Forge before any production deployment that involves email.

---

### CTO-008 — Nullable JSON Columns Must Return Array in Accessors

| Field | Value |
|---|---|
| **Decision ID** | CTO-008 |
| **Date** | 2026-07-02 |
| **Status** | Approved |
| **Related** | [11-Standards/Database-Standards.md](./11-Standards/Database-Standards.md) |

**Decision:** Any Eloquent model with a JSON column must implement a custom `Attribute` accessor that coerces `null` to `[]`. The default `array` cast returns `null` for a `NULL` database column, which causes `ErrorException` on array access.

**Reason:** BUG-002 root cause. The `Merchant->settings` column was `NULL` in the database, the default cast returned `null`, and `$merchant->settings['key']` raised `ErrorException: Trying to access array offset on null`. Fixed by custom accessor.

**Impact:** Standard pattern for all JSON columns:
```php
protected function settings(): Attribute {
    return Attribute::make(
        get: fn ($v) => $v === null ? [] : (is_array($v) ? $v : (json_decode($v, true) ?? [])),
        set: fn ($v) => $v ? json_encode($v) : null,
    );
}
```
Code review must verify this pattern on any new nullable JSON column.

---

### CTO-009 — Blade @json() Must Receive Single Variable

| Field | Value |
|---|---|
| **Decision ID** | CTO-009 |
| **Date** | 2026-07-02 |
| **Status** | Approved |
| **Related** | [11-Standards/Coding-Standards.md](./11-Standards/Coding-Standards.md) |

**Decision:** `@json()` in Blade views must receive a single PHP variable, never a multiline array literal. Arrays must be assigned in a `@php` block first.

**Reason:** BUG-002 root cause. Laravel's `Blade::compileJson()` uses `explode(',', $expression)` to split arguments, treating ALL commas as argument separators. A multiline array literal passed as the first argument produces truncated, invalid PHP output.

**Impact:** Standard pattern:
```blade
@php $data = ['key' => $value, ...]; @endphp
<div x-data="component(@json($data))">
```
Code review must reject `@json(['key' => $value, 'key2' => $value2])`.

---

### CTO-010 — OMOS is the Single Source of Truth

| Field | Value |
|---|---|
| **Decision ID** | CTO-010 |
| **Date** | 2026-07-02 |
| **Status** | Approved |
| **Related** | [README.md](./README.md), [EXECUTE.md](./EXECUTE.md) |

**Decision:** `docs/OMOS/` is the single source of truth for all OneMember governance decisions, standards, architecture decisions, product definitions, and sprint management. Any decision that conflicts with OMOS must update OMOS before implementation.

**Reason:** Without a single source of truth, decisions are made in conversation context that is lost between sessions. OMOS ensures that every future Claude Developer session begins with the full context of all prior decisions.

**Impact:** Claude Developer reads EXECUTE.md and CurrentSprint.md at the start of every session. No implementation begins without confirming the sprint spec. Architectural decisions not covered by OMOS require a stop-and-ask protocol.
