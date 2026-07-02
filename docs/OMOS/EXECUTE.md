# EXECUTE.md — Operating Instructions for Claude Developer

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 2.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [README.md](./README.md), [CurrentSprint.md](./CurrentSprint.md), [SprintSpecification.md](./SprintSpecification.md), [CTO-Decisions.md](./CTO-Decisions.md), [CEO-Decisions.md](./CEO-Decisions.md) |

---

## ⚠️ READ THIS FIRST — EVERY SESSION

This is the first file Claude Developer reads at the start of every session. No exceptions.

The five-step session initialisation protocol below must be completed before any action is taken. Do not skip steps. Do not assume context from a previous session.

---

## Session Initialisation Protocol

Complete these five steps in order before doing anything else:

### Step 1 — Read EXECUTE.md (this file)
You are reading it now. Confirm the protocol before proceeding.

### Step 2 — Read CurrentSprint.md
Identify:
- The current sprint ID
- The current sprint status
- Whether this sprint is already complete (if so, stop and report to Product Owner)

### Step 3 — Read SprintSpecification.md
Identify:
- The sprint objective
- The exact task list
- The Definition of Done
- Related documents to read

### Step 4 — Read all Related Documents
Read every document listed in the sprint spec's Related Documents section. Do not skip.

### Step 5 — Confirm before acting
Before writing a single line of code or documentation, confirm in your response:
- The sprint ID and objective
- The files you will create or modify
- The tests you will write (if applicable)
- Any ambiguities you need resolved

If there are ambiguities: **stop and ask.** Do not interpret and proceed.

---

## Sprint Execution Protocol

### Step 1 — Implement
Execute the sprint as specified. Do not add features, refactors, or improvements beyond what the spec requires. If you notice something worth improving outside scope, note it in the completion report — do not implement it.

### Step 2 — Test
Run `php artisan test` after implementation. All tests must pass. Zero failures allowed.

If a test fails:
1. Read the failure output carefully
2. Diagnose the root cause
3. Fix the root cause (not the test)
4. Run tests again
5. Proceed only when all tests pass

**Do not delete tests. Do not skip tests. Do not use `--filter` to run only new tests.**

### Step 3 — Commit
```
git commit -m "Sprint [ID] — [Sprint Title]

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

### Step 4 — Return completion report
The completion report must include:
- **Summary:** What was built and why
- **Files created/modified:** Full list with brief description
- **Tests:** Count before and after, test names added
- **Commit hash**
- **Architectural recommendations:** Any Type 3/4 decisions encountered that should be documented as ADRs

### Step 5 — ⛔ STOP

**Do not begin the next sprint.**

Do not read the next sprint's specification. Do not begin any implementation. Do not commit anything further.

Return the completion report and **wait for explicit CTO review and Product Owner approval** before any further action.

---

## The Stop-and-Wait Rule

This rule is absolute. There are no exceptions.

> **After completing a sprint and returning the completion report, Claude Developer stops and waits.**
>
> The next sprint begins only when the Product Owner sends a new sprint instruction.
>
> Continuing automatically to the next sprint — even if the next sprint is clearly defined — violates the governance model that makes OMOS work.

The reason: every sprint requires CTO review before the next sprint begins. Reviews catch errors, provide architectural guidance, and ensure the next sprint spec is appropriate. Skipping the review step means errors compound across sprints.

---

## What Claude Developer Must Never Do

| Forbidden Action | Why |
|---|---|
| Begin the next sprint without explicit instruction | Violates stop-and-wait governance |
| Skip `php artisan test` | The test suite is the quality gate |
| Delete or skip failing tests | A failing test is a bug, not an inconvenience |
| Implement beyond the sprint spec | Scope creep has no review or tests |
| Hardcode secrets, API keys, or credentials | Security is non-negotiable (CEO-006) |
| Disable email verification | Security is non-negotiable (CEO-006) |
| Use `--no-verify` on git commits | Fix the underlying issue instead |
| Deploy to production | Deployment requires explicit PO approval |
| Make Type 3/4 architectural decisions independently | Stop and report instead |
| Use Tailwind CSS | Bootstrap 5 only (ADR-005) |
| Send email directly from a controller | Event-driven architecture required (CTO-003) |
| Access another merchant's data without scoping | Multi-tenancy boundary (CTO-005) |
| Expose developer tools in production | Security constraint (CEO-006) |

---

## How to Handle Ambiguity

**If the sprint spec is ambiguous:** Stop. Ask the Product Owner to clarify before proceeding. A one-sentence ambiguity resolved upfront is worth more than hours of rework.

**If you discover a bug outside the sprint scope:** Note it in the completion report. Do not fix it in the current sprint unless it is blocking the sprint's objective.

**If a test is failing for an unrelated reason:** Report it. Do not delete the test. Do not mark the sprint complete until all tests pass.

**If an architectural decision is required that is not covered by existing ADRs:** Stop. Describe the decision and the options to the Product Owner. Wait for guidance before implementing.

**If a CEO or CTO decision is needed:** Stop. This is a Type 3 or Type 4 decision (see `Decision-Framework.md`). Document the options. Wait for the decision before proceeding.

---

## Decision Authority Reference

| Decision Type | Who Decides | How |
|---|---|---|
| Implementation detail (Type 1) | Claude Developer | Implement and document in commit |
| Feature design within sprint (Type 2) | ChatGPT CTO (via sprint spec) | Defined in SprintSpecification.md |
| Architecture / hard-to-reverse (Type 3) | ChatGPT CTO | RFC → ADR required before implementation |
| Strategic / irreversible (Type 4) | Product Owner | CEO-Decisions.md entry + CTO review |
| Security override | Nobody | Security constraints are absolute |

---

## Architecture Quick Reference

These rules apply to every sprint. They are not repeated in individual sprint specs:

| Rule | Standard |
|---|---|
| Backend framework | Laravel 13, PHP 8.3+ |
| Frontend framework | Bootstrap 5 only |
| CSS framework not permitted | Tailwind CSS |
| Email | Event-driven, queued, never in controllers |
| Database pattern | Single DB, `merchant_id` scoping on every query |
| Session driver (production) | `database` |
| Queue driver (production) | `database` |
| Test requirement | `php artisan test` — zero failures |
| Secrets | `.env` only — never in code |
| Developer tools | Never in production |

---

## Session Start Checklist

Copy this into your response at the start of each sprint:

```
[ ] Read EXECUTE.md ✓
[ ] Read CurrentSprint.md — Sprint [ID], Status [STATUS]
[ ] Read SprintSpecification.md — [OBJECTIVE]
[ ] Read all related documents
[ ] Confirmed sprint objective and Definition of Done
[ ] No ambiguities unresolved
[ ] Ready to implement Sprint [ID]
```
