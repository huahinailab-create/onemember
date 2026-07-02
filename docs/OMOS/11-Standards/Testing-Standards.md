# Testing Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Coding-Standards.md](./Coding-Standards.md), [05-Quality-Gates.md](./05-Quality-Gates.md), [Deployment-Standards.md](./Deployment-Standards.md) |

---

## Purpose

Standards for writing and organising tests in the OneMember test suite.

---

## Standards

### Test Runner
```bash
php artisan test
```
All tests must pass before any commit. Zero failures allowed.

### Test Structure
- `tests/Feature/` — HTTP-level tests (routes, controllers, full request-response cycle)
- `tests/Unit/` — Service class tests, model accessor tests, helper function tests
- `tests/Feature/Auth/` — Authentication and verification flow tests
- `tests/Feature/DevTools/` — Developer tools tests

### Test Rules
- Use `RefreshDatabase` trait on all Feature tests that touch the database
- Use `actingAs($user)` for authenticated routes — never bypass auth
- Do not mock the database — use factories and real queries
- Do not use `Http::fake()` for internal routes
- Test the happy path AND at least one failure path per feature
- Test both authenticated and unauthenticated access on protected routes

### Minimum Test Requirements per Sprint Type

| Sprint Type | Minimum |
|---|---|
| Feature sprint | 3 tests (happy path, failure, edge case) |
| Bug fix sprint | 1 regression test proving the bug is fixed |
| UI-only sprint | 1 render test per new page (assertOk, assertSee) |
| Security sprint | 2 tests (blocked access, allowed access) |
| Documentation sprint | 0 new tests (all existing tests must still pass) |

### Naming Convention
```php
public function test_[what_is_being_tested]_[expected_outcome](): void
// Examples:
public function test_unverified_user_redirected_to_verification_notice(): void
public function test_campaign_show_returns_500_when_settings_is_null(): void
public function test_merchant_settings_returns_empty_array_when_null_in_db(): void
```

### Factory Usage
- Always use model factories for test data
- Factories must produce valid, complete records
- Use `->create()` for DB-persisted records, `->make()` for in-memory only
- Use `->state()` for variations (e.g., `User::factory()->unverified()->create()`)
