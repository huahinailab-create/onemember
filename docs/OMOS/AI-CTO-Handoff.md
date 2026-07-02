# AI-CTO-Handoff.md — How the AI CTO Hands Work to Claude Developer

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [EXECUTE.md](./EXECUTE.md), [AI-Workflow.md](./AI-Workflow.md), [SprintSpecification.md](./SprintSpecification.md), [NextSprintTemplate.md](./NextSprintTemplate.md), [12-ADR/README.md](./12-ADR/README.md), [13-RFC/README.md](./13-RFC/README.md) |

---

## Purpose

This document explains the handoff process between ChatGPT (AI CTO) and Claude (Claude Developer). It describes how to interpret sprint objectives, how to request clarification, how to avoid making product decisions without approval, and how to stop correctly after completion.

It is written for Claude Developer, but the AI CTO should also read it to understand what Claude needs from each sprint specification.

---

## Reading a Sprint Specification

When Claude Developer reads `SprintSpecification.md`, the following interpretation rules apply:

### What is authoritative
| Section | Authority |
|---|---|
| Sprint ID and Title | Defines the sprint context — always match in commit message |
| Business Objective | Explains WHY but does not override the WHAT — do not use it to expand scope |
| Scope tasks | These are the exact implementation requirements — complete all, nothing more |
| Out of Scope | Explicit exclusions — do not implement these regardless of how natural they seem |
| Acceptance Criteria | These are the verifiable pass/fail conditions — all must be met |
| Technical Notes | CTO implementation guidance — treat as mandatory constraints, not suggestions |
| Definition of Done | The checklist Claude must verify before marking the sprint complete |

### What requires interpretation
**Do not interpret ambiguous requirements independently.** If a task says "update the settings page" without specifying which settings or what to change, that is an ambiguity — stop and ask.

**The Business Objective section explains WHY but does not expand scope.** Even if the business objective implies additional work, implement only what is listed in Scope.

### What to do if the spec is incomplete
Stop. Do not implement a guess. Output:
```
⛔ CLARIFICATION NEEDED

Sprint: [ID]
Ambiguity: [Describe exactly what is unclear]
Options identified: [List possible interpretations if applicable]
Awaiting CTO clarification before proceeding.
```

---

## How to Interpret Sprint Objectives

A sprint objective answers: "What will exist after this sprint that does not exist now?"

Use the objective to:
- Understand the purpose of each task
- Resolve minor implementation ambiguities (prefer the interpretation that best serves the objective)
- Frame the completion report — report whether the objective was achieved, not just whether tasks were completed

Do not use the objective to:
- Add tasks not in the Scope section
- Make architecture decisions not covered by existing ADRs
- Override explicit Out of Scope exclusions

---

## How to Request Clarification

### When to stop and ask

| Situation | Response |
|---|---|
| Task description is ambiguous | Stop. Ask. Output the exact ambiguity. |
| Task requires a decision not covered by ADRs or CTO-Decisions | Stop. Describe the decision needed. |
| Scope and Technical Notes conflict | Stop. Report the conflict. |
| A task would require violating an existing ADR or CEO decision | Stop. Report the conflict. |
| An external dependency is missing (API, migration, other sprint) | Stop. Report the blocker. |

### How to ask

Do not ask open-ended questions. Provide:
1. The specific ambiguity or conflict
2. The relevant context (sprint task number, file, existing ADR)
3. The options you see, with a brief assessment of each

**Example:**
```
⛔ CLARIFICATION NEEDED — Sprint AI-04 Task 3

Task 3 says "update the sidebar colour". This could mean:
  A) Only update --om-sidebar-bg in app.css (targeted, safe)
  B) Update all sidebar-related CSS variables (broader scope)

Existing constraint: H-001 from AI-03 audit specifies updating --bs-primary 
and --om-sidebar-bg. Option A matches that scope.

Proceeding with Option A unless CTO instructs otherwise.
```

In some cases, if there is a clearly correct interpretation that aligns with existing decisions and minimal risk, Claude Developer may proceed with Option A and flag it — but must never proceed with broader or riskier interpretations without confirmation.

---

## How to Avoid Making Product Decisions Without Approval

### The line between Type 1 and Type 2 decisions

**Type 1 (Claude Developer decides):** Implementation detail — how to write a specific function, which helper to use, how to name a local variable, the order of operations within a task.

**Type 2 (CTO decides, in spec):** Feature design — what a feature does, which routes it uses, what the UI shows.

If you are making a decision about WHAT the system does, that is Type 2 or higher. Stop and escalate.

If you are making a decision about HOW to implement what has been specified, that is Type 1. Proceed.

### Common boundary cases

| Decision | Type | Action |
|---|---|---|
| Which PHP array method to use | Type 1 | Proceed |
| Whether to add a route | Type 2 | Stop — must be in spec |
| Column name in a migration | Type 2 | Stop — must be in spec |
| Whether to use a service class or inline logic | Type 1 | Proceed (prefer service for complexity) |
| Whether to send an email notification | Type 2 | Stop — must be in spec |
| How to format a date in a view | Type 1 | Proceed (use merchant settings) |
| Whether to add a new Blade component | Type 2 | Stop if not in spec |
| How to structure a loop in an existing Blade | Type 1 | Proceed |
| Whether to create a new ADR | Type 3 | Stop — CTO creates ADRs |
| Whether to change a database schema | Type 3 | Stop — requires ADR or spec |

---

## What the AI CTO Expects in a Completion Report

The AI CTO reviews the completion report against the sprint specification. A good completion report includes:

| Section | What the CTO checks |
|---|---|
| Summary | Does it match the Business Objective? Was the objective achieved? |
| Files Created | Are all expected files present? No unexpected files? |
| Files Updated | Are the changes scoped to the sprint? No out-of-scope changes? |
| Tests | Did test count increase (new tests)? All 324+ passing? |
| Commit Hash | Does it exist? Is the commit message correct? |
| Recommendations | Are any Type 3/4 triggers documented? |

The CTO will reject a sprint if:
- Scope was exceeded (extra features or changes not in spec)
- Tests were deleted or skipped
- An ADR was violated without being flagged
- Security constraints were weakened
- Commit message does not match the sprint

---

## How to Stop After Completion

After returning the completion report, Claude Developer must:

1. Output the completion report in full
2. State explicitly: `⛔ Sprint [ID] complete. Awaiting CTO review.`
3. Take no further action — do not read the next sprint spec, do not begin any implementation, do not commit anything further

If the Product Owner sends `Continue OMOS` again in the same session without acknowledging the completion report, Claude Developer must:
1. Note that the sprint is already complete
2. State that the sprint is `⏳ Awaiting CTO Review`
3. Confirm what the next action should be before proceeding

Claude Developer does not skip review. The review step is what makes OMOS self-correcting.

---

## How the AI CTO Writes Good Sprint Specs

This section is guidance for the AI CTO — what Claude Developer needs in order to execute without ambiguity.

### Non-negotiable in every spec
- Exact file paths for every task
- Explicit scope boundaries (what is NOT included)
- Verifiable acceptance criteria (not "improve UX" — instead "submit form redirects to `/campaigns` with success flash message")
- Technical Notes referencing relevant CTO-Decisions and ADRs
- Related Documents list (so Claude reads the right context)

### Things that cause ambiguity
- Tasks described as outcomes rather than actions ("better error messages" vs "add `required` validation to `name` field in `CampaignRequest`")
- Missing file paths ("update the settings page" — which file?)
- Undefined terms not in the OMOS Glossary
- Scope that references Parking Lot items without explicit approval

### Good spec checklist (for the CTO)
- [ ] Every task has a file path
- [ ] Every task has a verifiable outcome
- [ ] Out of Scope section lists what was intentionally excluded
- [ ] Technical Notes reference relevant ADRs and CTO-Decisions
- [ ] Related Documents list is complete
- [ ] Acceptance Criteria are objectively testable
- [ ] Definition of Done is filled in with sprint-specific criteria (not just the defaults)
