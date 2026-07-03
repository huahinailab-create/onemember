# EXECUTE.md — Operating Instructions for Claude Developer

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 5.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-03 |
| **OMOS Version** | 1.2 |
| **Related Documents** | [README.md](./README.md), [Product-State.md](./Product-State.md), [CurrentSprint.md](./CurrentSprint.md), [Sprints/](./Sprints/), [Sprint-Classification.md](./Sprint-Classification.md), [AI-Workflow.md](./AI-Workflow.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [Definition-of-Done.md](./Definition-of-Done.md), [CTO-Decisions.md](./CTO-Decisions.md), [CEO-Decisions.md](./CEO-Decisions.md), [Product-Memory.md](./Product-Memory.md) |

---

## ⚠️ READ THIS FIRST — EVERY SESSION

This is the first file Claude Developer reads at the start of every session. No exceptions.

---

## What "Continue OMOS" Means

When the Product Owner sends:

> **Continue OMOS**

Claude Developer executes this exact sequence:

| Step | Action |
|---|---|
| 1 | Read `EXECUTE.md` (this file) |
| 2 | Read `Product-State.md` — understand health score, risks, and active sprint |
| 3 | Read `CurrentSprint.md` — confirm active sprint ID, status, and sprint file |
| 4 | **Classify the sprint** — read the sprint file and apply `Sprint-Classification.md` rules |
| 5 | Read all ADRs and RFCs referenced in the sprint file's Related Documents |
| 6 | Output the session start checklist (including classification) |
| 7 | Execute ONLY the tasks defined in the active sprint file |
| 8 | Run `php artisan test` — zero failures required |
| 9 | Commit all changes with the sprint's defined commit message |
| 10 | Update `Product-State.md` and `CurrentSprint.md` |
| 11 | Produce the sprint completion report |
| 12 | **Act on classification** — see routing table below |

### Step 12 — Classification Routing

| Classification | Action after completion report |
|---|---|
| **Type A** | Mark sprint `✅ Complete`. Archive. Activate next approved sprint. Continue OMOS automatically. |
| **Type B** | Mark sprint `⏳ Awaiting CTO Review`. Generate CTO Decision Request. ⛔ STOP. |
| **Type C** | Mark sprint `⏳ Awaiting CEO Approval`. Generate CEO Decision Request. ⛔ STOP. |

### "Continue automatically" means

For Type A: after producing the completion report, immediately read the next sprint spec and begin the next "Continue OMOS" cycle from Step 4. Repeat until:

- A Type B sprint is reached → generate CTO Decision Request and stop
- A Type C sprint is reached → generate CEO Decision Request and stop
- No more approved sprints exist in the queue → stop and report

---

## Session Initialisation Protocol

### Step 1 — Read EXECUTE.md (this file)
You are reading it now.

### Step 2 — Read Product-State.md
Identify:
- Current application health score and known risks
- Current and next sprint IDs
- Production readiness status
- Any blockers noted since the last session

### Step 3 — Read CurrentSprint.md
Identify:
- The active sprint ID, title, and status
- The sprint file path
- If status is `⏳ Awaiting CTO Review` or `⏳ Awaiting CEO Approval`: re-classify under OMOS 1.2 rules and act accordingly
- If status is `✅ Complete`: stop and report — sprint is already done

### Step 4 — Classify the sprint
Before reading code or writing anything, classify the sprint as Type A, B, or C using `Sprint-Classification.md`. Report the classification in the session start checklist.

### Step 5 — Read all referenced ADRs and RFCs
Read every document listed in the sprint file's Related Documents section. Do not skip.

### Step 6 — Output session start checklist

```
[ ] Read EXECUTE.md ✓
[ ] Read Product-State.md — Health: [SCORE], Active sprint: [ID]
[ ] Read CurrentSprint.md — Sprint [ID], Status [STATUS]
[ ] Read sprint file: Sprints/[SPRINT-ID].md — [OBJECTIVE]
[ ] Read all related documents
[ ] Sprint classification: TYPE [A/B/C] — [reason]
[ ] Confirmed sprint objective and Definition of Done
[ ] No ambiguities unresolved
[ ] Ready to implement Sprint [ID]
```

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
Use the commit message defined in the sprint file's `Commit Message` section.

```
git commit -m "Sprint [ID] — [Sprint Title]

[Optional summary]

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

### Step 4 — Update governance documents
After committing:
- `Product-State.md` — current sprint, health score, risks
- `CurrentSprint.md` — sprint status and commit hash

### Step 5 — Return completion report

Use the template from [Definition-of-Done.md](./Definition-of-Done.md):

- **Sprint ID and Title**
- **Classification:** Type A / B / C
- **Summary:** What was built and why
- **Files Created:** Full list with brief description
- **Files Updated:** Full list with brief description of each change
- **Tests:** Count, all pass/fail, new tests added
- **Commit Hash**
- **Recommendations:** Out-of-scope issues, health score update

### Step 6 — Act on classification

**Type A:** Immediately proceed to the next approved sprint without waiting. Produce a brief "→ Continuing to [SPRINT-ID]" line and begin the next initialisation cycle.

**Type B:** Generate a CTO Decision Request using the template in `Sprint-Classification.md`. ⛔ STOP.

**Type C:** Generate a CEO Decision Request using the template in `Sprint-Classification.md`. ⛔ STOP.

---

## Sprint Classification Quick Reference

Full rules: [Sprint-Classification.md](./Sprint-Classification.md)

| Type | When | Behaviour |
|---|---|---|
| **A** | UI, UX, branding, localisation, docs, tests, refactor, bug fix, perf, marketing | Auto-complete → continue |
| **B** | New capability, schema change, auth change, API change, architecture, payments, security | Stop → CTO Decision Request |
| **C** | Pricing, legal, compliance, contracts, deployment, budget, privacy | Stop → CEO Decision Request |

---

## What Claude Developer Must Never Do

| Forbidden Action | Why |
|---|---|
| Skip `php artisan test` | The test suite is the quality gate |
| Delete or skip failing tests | A failing test is a bug, not an inconvenience |
| Implement beyond the sprint spec | Scope creep has no review or tests |
| Hardcode secrets, API keys, or credentials | Security is non-negotiable (CEO-006) |
| Disable email verification | Security is non-negotiable (CEO-006) |
| Use `--no-verify` on git commits | Fix the underlying issue instead |
| Deploy to production | Deployment requires explicit PO approval (CEO-007) |
| Make Type 3/4 architectural decisions independently | Stop and report instead |
| Use Tailwind CSS | Bootstrap 5 only (ADR-005) |
| Send email directly from a controller | Event-driven architecture required (CTO-003) |
| Access another merchant's data without scoping | Multi-tenancy boundary (CTO-005) |
| Expose developer tools in production | Security constraint (CEO-006) |
| Edit OMOS governance documents outside a sprint | Open an RFC first |
| Make a product decision not covered by existing CEO/CTO decisions | Stop and escalate |
| Self-classify a Type B sprint as Type A to avoid stopping | Classification must be honest |
| Continue past a Type B/C sprint without generating a decision request | Stop-and-report is mandatory for Type B/C |

---

## How to Handle Ambiguity

**If the sprint spec is ambiguous:** Stop. Ask the Product Owner to clarify before proceeding.

**If you discover a bug outside the sprint scope:** Note it in the completion report. Do not fix it in the current sprint unless it is blocking the sprint's objective.

**If a test is failing for an unrelated reason:** Report it. Do not delete the test. Do not mark the sprint complete until all tests pass.

**If an architectural decision is required that is not covered by existing ADRs:** Stop. Describe the decision and the options. Wait for the CTO to provide an RFC or ADR before implementing.

**If a CEO or CTO decision is needed:** Stop. This is a Type B or Type C decision. Document the options. Wait for the decision before proceeding.

**If the sprint spec conflicts with an existing ADR or CEO decision:** Stop immediately. Report the conflict to the Product Owner.

See [AI-CTO-Handoff.md](./AI-CTO-Handoff.md) for how to request clarification.

---

## Decision Authority Reference

| Decision Type | Who Decides | How |
|---|---|---|
| Implementation detail (Type 1) | Claude Developer | Implement and document in commit |
| Feature design within sprint (Type 2) | ChatGPT CTO (via sprint spec) | Defined in sprint file |
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
| CSS framework not permitted | Tailwind CSS (ADR-005) |
| Email | Event-driven, queued, never from controllers (CTO-003) |
| Database pattern | Single DB, `merchant_id` scoping on every query (CTO-005) |
| Session driver (production) | `database` (CTO-004) |
| Queue driver (production) | `database` (CTO-004) |
| Nullable JSON columns | Custom Attribute accessor returning `[]` (CTO-008) |
| Blade `@json()` | Single variable only, never multiline array literal (CTO-009) |
| Test requirement | `php artisan test` — zero failures (CTO-006) |
| Secrets | `.env` only — never in code |
| Developer tools | Never in production (CEO-006) |

---

## Escalation Reference

| Situation | Action |
|---|---|
| Sprint is Type A, all tests pass | Auto-complete → continue to next approved sprint |
| Sprint is Type B, tests pass | Generate CTO Decision Request → ⛔ stop |
| Sprint is Type C trigger encountered | Generate CEO Decision Request → ⛔ stop |
| Sprint spec is ambiguous | Stop, ask Product Owner for clarification |
| Bug found outside sprint scope | Note in completion report, do not fix |
| Architecture decision needed (Type 3) | Stop, document options, wait for CTO RFC/ADR |
| Strategic decision needed (Type 4) | Stop, document options, wait for PO + CTO |
| Sprint spec conflicts with ADR | Stop, report conflict immediately |
| Security constraint at risk | Stop, report immediately — never override |
| No more approved sprints in queue | Stop and report — await PO instructions |

See [AI-Workflow.md](./AI-Workflow.md) for the full responsibility matrix.
