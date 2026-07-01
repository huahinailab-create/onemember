# Quality Gates

Every sprint must pass all quality gates before it is considered complete. These gates apply to every sprint regardless of size.

---

## Gate 1 — Sprint Spec Completeness

Before implementation starts, the sprint spec must contain:

| Required Section | Description |
|---|---|
| Business Objective | What business value does this deliver? |
| Scope | Explicit list of what will be built |
| Non-Scope | Explicit list of what will NOT be built |
| Database Changes | Migrations, new tables, schema changes, or "None" |
| UI Requirements | Page layout, components, interactions |
| Branding Requirements | Colors, logo, Bootstrap 5 confirmation |
| Security Requirements | Auth, validation, CSRF, rate limits |
| Localization Requirements | New lang keys, supported languages |
| Tests Required | Named or described tests |
| Documentation Required | Docs to create or update |
| Deployment Notes | Commands to run post-deploy |
| Rollback Notes | How to undo this sprint |
| Definition of Done | Checklist |

**Gate fails if:** Any required section is missing or says "TBD".

---

## Gate 2 — Tests Pass

```bash
php artisan test
```

**Required:** Zero failures. Zero errors. Zero unexpected warnings.

**Minimum test additions per sprint type:**

| Sprint Type | Minimum New Tests |
|---|---|
| Feature sprint | 3 (happy path, failure path, edge case) |
| Bug fix sprint | 1 regression test proving the bug is fixed |
| UI-only sprint | 1 render test per new page |
| Security sprint | 2 (unauthorized access blocked, authorized access allowed) |
| Documentation sprint | 0 (but existing tests must still pass) |

**Gate fails if:** Any test fails, or test count decreases from previous sprint.

---

## Gate 3 — Security Checklist

Every sprint that touches routes, controllers, or views must pass:

- [ ] All new routes have appropriate middleware (`auth`, `verified`, `signed`, etc.)
- [ ] All form inputs are validated (Form Request or inline `$request->validate()`)
- [ ] CSRF token present on all POST/PUT/PATCH/DELETE forms
- [ ] No user data rendered with `{!! !!}` unless explicitly justified
- [ ] No hardcoded secrets, API keys, or credentials
- [ ] No debug output (`dd()`, `dump()`, `var_dump()`) in committed code
- [ ] Developer tools routes return 404 in production

**Gate fails if:** Any item is unchecked.

---

## Gate 4 — Branding Compliance

Every sprint that creates or modifies UI must pass:

- [ ] Only OneMember brand colors used (see `ai/06-Branding-Rules.md`)
- [ ] No external CSS frameworks (no Tailwind, no Bulma, no Material UI)
- [ ] Bootstrap 5 utilities used consistently
- [ ] All new pages use `x-app-layout` or `x-guest-layout`
- [ ] Official OneMember logo used where logo is shown
- [ ] Responsive on 375px (mobile) and 1280px+ (desktop)
- [ ] No unsanctioned fonts or icon libraries

**Gate fails if:** Any item is unchecked.

---

## Gate 5 — Localization

Every sprint that adds or modifies user-visible text must pass:

- [ ] No hardcoded English strings in Blade views
- [ ] All strings use `__('namespace.key')` or `@lang('namespace.key')`
- [ ] New keys added to `lang/en/` files
- [ ] New keys added to `lang/th/` files (even if Thai translation is pending — use English as placeholder)

**Gate fails if:** Any Blade view contains a hardcoded string that should be translated.

---

## Gate 6 — Database Integrity

Every sprint that modifies the database must pass:

- [ ] Migration has a working `down()` method
- [ ] `php artisan migrate:rollback` tested locally
- [ ] All new foreign keys defined with appropriate `onDelete()` behaviour
- [ ] `$fillable` or `$guarded` updated on affected models
- [ ] No data loss possible from running the migration on existing data

**Gate fails if:** Migration is irreversible or could destroy existing data without a safety check.

---

## Gate 7 — Documentation

Every sprint that introduces a new architectural pattern, decision, or significant feature must pass:

- [ ] New architectural decision documented in `docs/08-Product-Decisions.md`
- [ ] New features documented in the relevant `docs/` file
- [ ] `ai/07-Architecture-Rules.md` updated if a new standing rule was established

**Gate fails if:** A significant decision was made with no record.

---

## Gate 8 — Commit Standards

- [ ] Commit message begins with the sprint ID
- [ ] Only sprint-scope files are staged
- [ ] No `.env`, `*.log`, `storage/logs/*`, or compiled assets committed
- [ ] No merge conflicts in any file

**Gate fails if:** Any item is unchecked.

---

## Escalation

If a gate fails and Claude cannot resolve it:

1. Claude reports the failing gate and root cause clearly.
2. Product Owner consults ChatGPT CTO.
3. CTO either updates the spec or provides a solution.
4. Claude implements the solution.
5. All gates are re-checked.

**There is no "ship it anyway" option.**
