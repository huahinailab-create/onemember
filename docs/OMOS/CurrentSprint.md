# CurrentSprint.md — Active Sprint Board

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [README.md](./README.md), [ai/04-Sprint-Workflow.md](../../ai/04-Sprint-Workflow.md), [sprints/](../../sprints/) |

---

> **Instructions for use:**
> This file holds exactly one sprint at a time — the sprint currently in progress.
> Before a new sprint begins, archive the completed sprint to `sprints/SPRINT-[ID]-[Name].md`.
> Then replace the content below with the new sprint spec.
> Claude Developer reads this file first at the start of every session.

---

## Sprint Template

Copy this template for every new sprint. Replace all `[bracketed]` values.

---

```markdown
# Sprint [ID] — [Name]

## Sprint Metadata

| Field | Value |
|---|---|
| **Sprint ID** | [e.g. SPRINT-012, BUG-004, AI-02B] |
| **Status** | [Draft / In Progress / Complete / Blocked] |
| **Owner** | [Product Owner name or role] |
| **Developer** | Claude (Sonnet 4.6) |
| **Reviewer** | ChatGPT CTO |
| **Started** | YYYY-MM-DD |
| **Target Completion** | YYYY-MM-DD |
| **Actual Completion** | YYYY-MM-DD or — |
| **Commit Hash** | — (filled after completion) |

---

## Business Objective

[One paragraph. What business value does this sprint deliver?
Who benefits and how? Why does it matter now?]

---

## Scope

What Claude will build in this sprint:

- [ ] [Task 1]
- [ ] [Task 2]
- [ ] [Task 3]

---

## Out of Scope

What Claude must NOT touch in this sprint:

- [Item 1 — explicitly excluded]
- [Item 2 — explicitly excluded]

---

## Tasks

Detailed breakdown (Claude updates status as work progresses):

| # | Task | Status | Notes |
|---|---|---|---|
| 1 | [Task description] | ⬜ Not started / 🔄 In progress / ✅ Done | — |

---

## Definition of Done

This sprint is complete when ALL of the following are true:

- [ ] [Condition 1]
- [ ] [Condition 2]
- [ ] `php artisan test` passes — zero failures
- [ ] Commit message matches sprint ID
- [ ] Product Owner has been given the completion report

---

## Risks

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| [Risk description] | Low / Medium / High | Low / Medium / High | [Mitigation plan] |

---

## Dependencies

- [External service, prior sprint, or decision that must be in place before this sprint can complete]

---

## Testing

Required tests for this sprint:

| Test Name | Type | Purpose |
|---|---|---|
| [test_name] | Feature / Unit / Browser | [What it verifies] |

---

## Deployment Notes

Commands to run after deployment:

```bash
# [command]
```

New environment variables required:

| Variable | Value | Environment |
|---|---|---|
| `VAR_NAME` | description | production / staging / all |

---

## Rollback Notes

If this sprint must be reverted:

```bash
# [rollback commands]
```

---

## Next Sprint

Suggested follow-on work after this sprint is approved:

- [Suggestion 1]
- [Suggestion 2]

---

## Completion Report

*(Filled in by Claude Developer after completing the sprint)*

**Summary:** [One paragraph]

**Files created:**
- `path/to/file`

**Files modified:**
- `path/to/file` — [what changed]

**Tests added:** [count]
**Total tests:** [count] — all passing

**Commit hash:** [hash]

**Out-of-scope observations:** [Optional notes on things noticed but not implemented]
```

---

## Current Active Sprint

*(Replace this section with the active sprint spec when a sprint begins.)*

**No sprint currently active.**

Last completed sprint: `AI-02A — OneMember Operating System Foundation` (`09948b9` → to be updated after AI-02A commit)

Next planned sprint: `AI-02B — Product Bible and Roadmap`
