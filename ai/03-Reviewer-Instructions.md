# Sprint Reviewer — Instructions

## Purpose

Every completed sprint must be reviewed before production deployment. The primary reviewer is the ChatGPT CTO. The Product Owner gives final deployment approval.

This document defines what a good review looks like and what must be checked.

---

## Reviewer Checklist

### 1. Objective Alignment
- [ ] Does the implementation match the stated business objective?
- [ ] Were all items in the sprint scope delivered?
- [ ] Were non-scope items respected (nothing extra was added)?

### 2. Code Quality
- [ ] No `dd()`, `dump()`, `var_dump()`, or debug output in committed code
- [ ] No hardcoded secrets, API keys, or passwords
- [ ] No `APP_DEBUG=true` dependencies
- [ ] No inline SQL or raw queries without `DB::statement()` and binding
- [ ] No commented-out dead code committed

### 3. Security
- [ ] All new routes use appropriate middleware (`auth`, `verified`, etc.)
- [ ] All new form inputs are validated (Form Request or inline `validate()`)
- [ ] CSRF protection present on all forms
- [ ] No user-supplied data rendered unescaped (`{!! !!}` must be justified)
- [ ] No developer tools accessible in production paths
- [ ] No new secrets or credentials in codebase

### 4. Database
- [ ] Migrations have a working `down()` method
- [ ] No mass-assignment vulnerabilities (`$fillable` or `$guarded` correct)
- [ ] Soft deletes used where data must be recoverable
- [ ] Foreign key constraints defined

### 5. UI / Branding
- [ ] All new pages use `x-app-layout` or `x-guest-layout`
- [ ] Only OneMember brand colors used (`#1A2E5A`, `#FF1585`, `#F0F0F4`, `#1A1A2E`, `#FFFFFF`)
- [ ] Bootstrap 5 only — no Tailwind or other frameworks
- [ ] Responsive on mobile (375px) and desktop (1280px+)
- [ ] Official OneMember logo used where applicable

### 6. Localization
- [ ] All user-visible strings use `__()` or `@lang()`
- [ ] New lang keys added to both `lang/en/` and `lang/th/`
- [ ] No hardcoded English text in Blade views

### 7. Testing
- [ ] `php artisan test` passes — zero failures
- [ ] Test count increased from the previous sprint
- [ ] New features have Feature tests
- [ ] New service methods have Unit tests
- [ ] Happy path and failure paths are tested

### 8. Documentation
- [ ] Relevant `docs/` files updated if spec required it
- [ ] Architecture decisions added to `docs/08-Product-Decisions.md` if applicable

### 9. Commit Hygiene
- [ ] Commit message matches sprint ID and name
- [ ] No debug files, `.env` files, or generated assets committed
- [ ] No merge conflicts left in files

---

## Review Output Format

Save completed reviews as `reviews/REVIEW-[SPRINT-ID].md`:

```markdown
# Review — Sprint [ID]: [Name]

**Date:** YYYY-MM-DD
**Reviewer:** ChatGPT CTO
**Commit:** [hash]

## Rating: [✅ Approved / ⚠️ Approved with notes / 🔄 Revision required / ❌ Rejected]

## Summary
[One paragraph assessment.]

## Checklist Results
[Paste checklist with ✅ / ❌ / ⚠️ next to each item]

## Issues Found
[Numbered list. Empty if none.]

## Required Actions Before Deploy
[Empty if Approved. List specific fixes if Revision required.]

## Notes for Next Sprint
[Observations about tech debt, patterns, or opportunities.]
```

---

## Common Review Failure Patterns

| Pattern | What to look for |
|---|---|
| Scope creep | Files modified that aren't in the sprint spec |
| Test theatre | Tests that always pass regardless of implementation |
| Security regression | New unprotected routes, missing CSRF, raw output |
| Branding drift | New colors, fonts, or layout patterns not in the design system |
| Silent failures | Try/catch blocks that swallow exceptions without logging |
| Over-engineering | Abstractions with only one caller, premature generalization |
| Hardcoded values | Magic numbers, hardcoded IDs, or environment-specific strings |
