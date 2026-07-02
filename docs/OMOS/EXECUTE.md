# EXECUTE.md — Operating Instructions for Claude Developer

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 4.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [README.md](./README.md), [Product-State.md](./Product-State.md), [CurrentSprint.md](./CurrentSprint.md), [Sprints/](./Sprints/), [AI-Workflow.md](./AI-Workflow.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [Definition-of-Done.md](./Definition-of-Done.md), [CTO-Decisions.md](./CTO-Decisions.md), [CEO-Decisions.md](./CEO-Decisions.md), [Product-Memory.md](./Product-Memory.md) |

---

## ⚠️ READ THIS FIRST — EVERY SESSION

This is the first file Claude Developer reads at the start of every session. No exceptions.

---

## What "Continue OMOS" Means

When the Product Owner sends:

> **Continue OMOS**

Claude Developer executes this exact 12-step sequence and nothing more:

| Step | Action |
|---|---|
| 1 | Read `EXECUTE.md` (this file) |
| 2 | Read `Product-State.md` — understand the current health score, risks, and active sprint |
| 3 | Read `CurrentSprint.md` — confirm the active sprint ID, status, and sprint file reference |
| 4 | Read the active sprint file from `docs/OMOS/Sprints/[SPRINT-ID].md` |
| 5 | Read all ADRs and RFCs referenced in the sprint file's Related Documents |
| 6 | Execute ONLY the tasks defined in the active sprint file |
| 7 | Run `php artisan test` — zero failures required |
| 8 | Commit all changes with the sprint's defined commit message |
| 9 | Update `Product-State.md` — current sprint, health score if changed, risks updated |
| 10 | Update `CurrentSprint.md` — sprint status to `⏳ Awaiting CTO Review`, commit hash recorded |
| 11 | Produce the sprint completion report |
| 12 | ⛔ STOP — wait for CTO review and PO approval |

**Do not start the next sprint. Do not read the next sprint spec. Do not take any further action.**

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
- If status is `⏳ Awaiting CTO Review` or `✅ Complete`: **stop and report** — do not re-execute the sprint

### Step 4 — Read the active sprint file
Find the sprint specification in `docs/OMOS/Sprints/[SPRINT-ID].md`.
Identify the full task list, acceptance criteria, Definition of Done, and Related Documents.

### Step 5 — Read all referenced ADRs and RFCs
Read every document listed in the sprint file's Related Documents section. Do not skip.

### Step 6 — Confirm before acting

Before writing a single line of code or documentation, output the session start checklist:

```
[ ] Read EXECUTE.md ✓
[ ] Read Product-State.md — Health: [SCORE], Active sprint: [ID]
[ ] Read CurrentSprint.md — Sprint [ID], Status [STATUS]
[ ] Read sprint file: Sprints/[SPRINT-ID].md — [OBJECTIVE]
[ ] Read all related documents
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

### Step 4 — Update Product-State.md
After committing:
- Update `Current Sprint` section — set to the sprint just completed
- Update `Next Sprint` section — set to next known sprint from sprint backlog
- Update `Current Risks` — remove resolved risks, add any new risks discovered
- Update health score if the sprint changed it (e.g., after MVP-001 brand fix)

### Step 5 — Update CurrentSprint.md
After updating Product-State.md:
- Set sprint status to `⏳ Awaiting CTO Review`
- Record the commit hash
- Move current sprint to Previous Sprint
- Set Next Planned Sprint from sprint backlog

### Step 6 — Return completion report

Use the template from [Definition-of-Done.md](./Definition-of-Done.md):

- **Sprint ID and Title**
- **Summary:** What was built and why
- **Files Created:** Full list with brief description of each
- **Files Updated:** Full list with brief description of each change
- **Tests:** Count, all pass/fail, new tests added
- **Commit Hash**
- **Recommendations:** Any Type 3/4 decisions, out-of-scope issues, health score update

### Step 7 — ⛔ STOP

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
| Deploy to production | Deployment requires explicit PO approval (CEO-007) |
| Make Type 3/4 architectural decisions independently | Stop and report instead |
| Use Tailwind CSS | Bootstrap 5 only (ADR-005) |
| Send email directly from a controller | Event-driven architecture required (CTO-003) |
| Access another merchant's data without scoping | Multi-tenancy boundary (CTO-005) |
| Expose developer tools in production | Security constraint (CEO-006) |
| Edit OMOS governance documents outside a sprint | Open an RFC first |
| Make a product decision not covered by existing CEO/CTO decisions | Stop and escalate |

---

## How to Handle Ambiguity

**If the sprint spec is ambiguous:** Stop. Ask the Product Owner to clarify before proceeding.

**If you discover a bug outside the sprint scope:** Note it in the completion report. Do not fix it in the current sprint unless it is blocking the sprint's objective.

**If a test is failing for an unrelated reason:** Report it. Do not delete the test. Do not mark the sprint complete until all tests pass.

**If an architectural decision is required that is not covered by existing ADRs:** Stop. Describe the decision and the options. Wait for the CTO to provide an RFC or ADR before implementing.

**If a CEO or CTO decision is needed:** Stop. This is a Type 3 or Type 4 decision. Document the options. Wait for the decision before proceeding.

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
| Sprint spec is ambiguous | Stop, ask Product Owner for clarification |
| Bug found outside sprint scope | Note in completion report, do not fix |
| Architecture decision needed (Type 3) | Stop, document options, wait for CTO RFC/ADR |
| Strategic decision needed (Type 4) | Stop, document options, wait for PO + CTO |
| Sprint spec conflicts with ADR | Stop, report conflict immediately |
| Security constraint at risk | Stop, report immediately — never override |
| All tests passing, sprint complete | Update Product-State.md, update CurrentSprint.md, return completion report, ⛔ STOP |

See [AI-Workflow.md](./AI-Workflow.md) for the full responsibility matrix.
