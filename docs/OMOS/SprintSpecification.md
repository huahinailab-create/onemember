# Sprint Specification

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | — |
| **Status** | — |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [CurrentSprint.md](./CurrentSprint.md), [EXECUTE.md](./EXECUTE.md), [SprintReview.md](./SprintReview.md) |

---

## Purpose

This file holds the active sprint specification. It is replaced at the start of each sprint with the specification for that sprint.

The sprint specification is the contract between the ChatGPT CTO (who writes it) and Claude Developer (who implements it). It is specific enough that implementation should not require interpretation.

---

## Current Sprint Specification

> **No sprint is currently active.**
>
> The last completed sprint was **AI-OMOS-BOOTSTRAP**.
> See [CurrentSprint.md](./CurrentSprint.md) for status.
>
> The next sprint specification will be written here when the Product Owner and ChatGPT CTO define the next sprint.
> Use [NextSprintTemplate.md](./NextSprintTemplate.md) as the template.

---

## Sprint Specification Template

When a new sprint is being prepared, the ChatGPT CTO replaces the content of this file with the following structure:

```markdown
# Sprint [ID] — [Title]

## Objective
[One paragraph. What will exist after this sprint that does not exist now? Why does it matter?]

## Scope
[Explicit list of what IS included in this sprint]

## Out of Scope
[Explicit list of what is NOT included — prevents scope creep]

## Tasks

| # | Task | File(s) | Notes |
|---|---|---|---|
| 1 | [Task description] | [file path] | [implementation notes] |

## Definition of Done

- [ ] All tasks completed
- [ ] `php artisan test` passes with zero failures
- [ ] All new features have regression tests
- [ ] No hardcoded secrets
- [ ] All new UI strings use __() helpers
- [ ] Committed with sprint ID in message
- [ ] Completion report returned to Product Owner

## Related Documents
[Links to ADRs, standards, or product docs relevant to this sprint]

## Technical Notes
[Any specific implementation guidance from the CTO]

## Risks
[Known risks or unknowns that may affect implementation]
```
