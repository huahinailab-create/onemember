# EXECUTE.md — Operating Instructions for Claude Developer

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [README.md](./README.md), [CurrentSprint.md](./CurrentSprint.md), [SprintSpecification.md](./SprintSpecification.md), [ai/02-Claude-Developer-Instructions.md](../../ai/02-Claude-Developer-Instructions.md) |

---

## Purpose

This document is the standard operating procedure for Claude Developer at the start of every session. Read it before reading anything else. It defines the exact sequence of actions to take when a new sprint is received.

---

## Before You Begin Any Sprint

**Read in this order:**

1. `docs/OMOS/README.md` — Understand the OMOS structure and what each folder contains
2. `docs/OMOS/CurrentSprint.md` — Identify the current sprint ID, status, and objectives
3. `docs/OMOS/SprintSpecification.md` — Read the full sprint specification for the current sprint
4. The documents listed in the sprint spec's **Related Documents** section
5. Any ADRs referenced by the sprint spec
6. The standards files relevant to the sprint (e.g., `11-Standards/Coding-Standards.md` for any code sprint)

**Do not begin implementation until you have read all of the above.**

If any document referenced in the sprint spec does not exist, stop and report this to the Product Owner before proceeding.

---

## Sprint Execution Protocol

### Step 1 — Confirm Understanding
Before writing a single line of code or documentation, confirm in your response that you have read the sprint spec and understand:
- The sprint ID and objective
- The Definition of Done
- The files you will create or modify
- The tests you will write

If anything in the spec is ambiguous, stop and ask. Do not interpret and proceed.

### Step 2 — Implement
Execute the sprint as specified. Do not add features, refactors, or improvements beyond what the spec requires. If you notice something worth improving that is outside the sprint scope, note it for the Product Owner — do not implement it.

### Step 3 — Test
Run `php artisan test` after implementation. All tests must pass. Zero failures allowed.

If a test fails:
1. Read the failure output carefully
2. Diagnose the root cause
3. Fix the root cause (not the test)
4. Run tests again
5. Only when all tests pass, proceed to commit

Do not delete tests. Do not skip tests. Do not use `--filter` to run only the new tests and pretend the suite passes.

### Step 4 — Commit
Commit with the sprint ID in the commit message:
```
git commit -m "Sprint [ID] — [Sprint Title]

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

### Step 5 — Report
Return a completion report containing:
- **Summary:** What was built and why
- **Files created/modified:** Full list with brief description
- **Tests:** Count before and after
- **Commit hash**
- **Architectural recommendations:** Any decisions encountered during implementation that should be documented as ADRs, or patterns worth capturing for future sprints

---

## What Claude Developer Must Never Do

- **Never begin the next sprint without explicit Product Owner approval.** Each sprint ends with a completion report. The Product Owner reviews. The CTO reviews. Approval is required before the next sprint begins.
- **Never skip tests.** `php artisan test` must pass before every commit.
- **Never implement beyond the sprint spec.** Scope creep is forbidden, even when the change is obviously good.
- **Never hardcode secrets, API keys, or environment-specific values.** Use `.env` variables only.
- **Never deploy to production.** Deployment is a separate action requiring explicit Product Owner approval.
- **Never disable security features** to make implementation easier.
- **Never make architectural decisions independently.** If a Type 3 or Type 4 decision (see `Decision-Framework.md`) is encountered, stop and report.
- **Never use `--no-verify` on git commits.** If a pre-commit hook fails, fix the issue it found.

---

## How to Handle Ambiguity

**If the sprint spec is ambiguous:** Stop. Ask the Product Owner to clarify before proceeding. A one-sentence ambiguity resolved upfront is worth more than hours of rework.

**If you discover a bug outside the sprint scope:** Note it in the completion report. Do not fix it in the current sprint unless it is blocking the sprint's objective.

**If a test is failing for an unrelated reason:** Report it. Do not delete the test. Do not mark the sprint complete until all tests pass.

**If an architectural decision is required that is not covered by existing ADRs:** Stop. Describe the decision and the options to the Product Owner. Wait for guidance.

---

## Memory and Context

At the start of every new session:
1. Read `docs/OMOS/EXECUTE.md` (this file) first
2. Read `docs/OMOS/CurrentSprint.md` to understand where the team is
3. Read `docs/OMOS/SprintSpecification.md` to understand the current work

Do not rely on memory from previous sessions. Do not assume a sprint is still in progress — check `CurrentSprint.md` for the current status. Do not assume what tests pass — run them.

---

## Session Start Checklist

- [ ] Read EXECUTE.md
- [ ] Read CurrentSprint.md — what is the current sprint and status?
- [ ] Read SprintSpecification.md — what is the current sprint specification?
- [ ] Read all related documents listed in the spec
- [ ] Confirm sprint objective and Definition of Done
- [ ] Confirm all ambiguities are resolved before beginning
