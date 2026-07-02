# Sprint-Lifecycle.md — Sprint Phases and Gates

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [EXECUTE.md](./EXECUTE.md), [AI-Workflow.md](./AI-Workflow.md), [Definition-of-Ready.md](./Definition-of-Ready.md), [Definition-of-Done.md](./Definition-of-Done.md), [NextSprintTemplate.md](./NextSprintTemplate.md), [CurrentSprint.md](./CurrentSprint.md) |

---

## Overview

Every sprint at OneMember passes through eight phases. Each phase has an entry condition, defined actions, and an exit gate. No phase may be skipped.

```
Planning → Execution → Testing → Review → Approval → Deployment → Retrospective → Archive
```

---

## Phase 1 — Planning

**Who:** Product Owner + AI CTO  
**Entry condition:** Previous sprint is `Complete` and archived, or this is the first sprint.

### Actions

| Actor | Action |
|---|---|
| Product Owner | Defines the business objective for the next sprint |
| AI CTO | Reviews CurrentSprint.md and all relevant OMOS documents |
| AI CTO | Writes the sprint specification using `NextSprintTemplate.md` |
| AI CTO | Verifies spec against existing ADRs, CTO-Decisions, and CEO-Decisions |
| AI CTO | Fills in `SprintSpecification.md` with the complete spec |
| Product Owner | Reviews the sprint specification |
| Product Owner | Approves or requests changes |

### Exit Gate — Definition of Ready

See [Definition-of-Ready.md](./Definition-of-Ready.md). A sprint may not enter Execution until it meets the Definition of Ready.

### CurrentSprint.md Status
`🔲 Planning`

---

## Phase 2 — Execution

**Who:** Claude Developer  
**Entry condition:** Sprint meets Definition of Ready; Product Owner has sent `Continue OMOS` or equivalent.

### Actions

| Actor | Action |
|---|---|
| Claude Developer | Reads EXECUTE.md, CurrentSprint.md, SprintSpecification.md, all related documents |
| Claude Developer | Outputs session start checklist confirming readiness |
| Claude Developer | Implements each task in the sprint specification |
| Claude Developer | Does not add features, refactors, or changes outside the sprint scope |
| Claude Developer | Notes out-of-scope findings for the completion report |

### Exit Gate
All sprint tasks implemented. Ready for testing.

### CurrentSprint.md Status
`🔄 In Progress`

---

## Phase 3 — Testing

**Who:** Claude Developer  
**Entry condition:** All sprint tasks implemented.

### Actions

| Actor | Action |
|---|---|
| Claude Developer | Runs `php artisan test` — full test suite, no filters |
| Claude Developer | If failures: diagnoses root cause, fixes root cause, reruns |
| Claude Developer | Repeats until zero failures |
| Claude Developer | Does not delete or skip failing tests |
| Claude Developer | Records test count and pass/fail in completion report |

### Exit Gate
`php artisan test` passes with zero failures.

### CurrentSprint.md Status
`🔄 In Progress` (remains in progress until commit)

---

## Phase 4 — Review

**Who:** AI CTO  
**Entry condition:** Claude Developer has returned the completion report and stopped.

### Actions

| Actor | Action |
|---|---|
| Claude Developer | Commits all changes, updates CurrentSprint.md, returns completion report, ⛔ stops |
| AI CTO | Reviews completion report |
| AI CTO | Verifies: architecture compliance, OMOS consistency, test coverage, security |
| AI CTO | Checks: no forbidden actions taken, no scope creep, no ADR violations |
| AI CTO | Returns verdict: Approved / Approved with notes / Rejected with reason |

### Exit Gate
AI CTO verdict is `Approved` or `Approved with notes`.

If `Rejected`: AI CTO specifies what must change. Product Owner decides whether to re-run the sprint or abandon it.

### CurrentSprint.md Status
`⏳ Awaiting CTO Review`

---

## Phase 5 — Approval

**Who:** Product Owner  
**Entry condition:** AI CTO has approved the sprint.

### Actions

| Actor | Action |
|---|---|
| Product Owner | Reviews CTO approval and completion report |
| Product Owner | Decides: Deploy / Hold / Request changes |
| Product Owner | If deploying: proceeds to Deployment phase |
| Product Owner | If holding: sprint is `Approved, Not Deployed` — remains in history |
| Product Owner | Approves or defines the next sprint |

### Exit Gate
Product Owner has made a deployment decision.

### CurrentSprint.md Status
`⏳ Awaiting PO Approval` → `✅ Complete`

---

## Phase 6 — Deployment

**Who:** Product Owner (approves), AI CTO (advises), Claude Developer (cannot deploy)  
**Entry condition:** Product Owner has explicitly approved deployment.

### Actions

| Actor | Action |
|---|---|
| Product Owner | Approves deployment to staging or production |
| AI CTO | Confirms deployment checklist (no pending migrations, ENV vars set, queue workers running) |
| Product Owner | Triggers deployment via Forge / CI pipeline |
| Product Owner | Monitors deployment for errors |
| Product Owner | Confirms deployment successful |

### Deployment Checklist

- [ ] `php artisan test` passed on the commit being deployed
- [ ] All new ENV variables added to Forge environment
- [ ] Migrations reviewed — `down()` methods verified
- [ ] Queue workers restarted after deployment
- [ ] Health check endpoint (`/up`) returns 200 post-deploy
- [ ] Smoke test: login, dashboard, campaigns list — all load
- [ ] No 500 errors in logs for first 15 minutes

### Exit Gate
Deployment confirmed successful. No production errors in first 15 minutes.

### CurrentSprint.md Status
`✅ Complete`

---

## Phase 7 — Retrospective

**Who:** AI CTO + Product Owner  
**Entry condition:** Sprint is `Complete`. May be synchronous (immediate) or deferred (next planning session).

### Questions to Answer

| Question | Purpose |
|---|---|
| Did the sprint deliver its business objective? | Outcome check |
| Were there any surprises during implementation? | Process improvement |
| Did Claude Developer encounter any Type 3/4 decisions? | ADR/RFC triggers |
| Were any bugs found that the test suite missed? | Test coverage improvement |
| Did the Definition of Ready prevent wasted work? | Gate effectiveness |
| What would make the next sprint run more smoothly? | Spec quality improvement |
| Are there any OMOS documents that need updating based on what we learned? | Knowledge capture |

### Actions
- Record any new architectural decisions as ADRs
- Update relevant OMOS documents if standards changed
- Update the parking lot if new items were identified
- Archive the sprint specification

---

## Phase 8 — Archive

**Who:** Claude Developer (on next sprint's initialisation)  
**Entry condition:** Sprint is `Complete`.

### Actions

| Actor | Action |
|---|---|
| Claude Developer | Records sprint in the Sprint History table in `CurrentSprint.md` |
| Claude Developer | Updates `SprintReview.md` with the sprint summary |
| AI CTO | Optionally copies sprint spec to `16-Appendix/Sprints/[SPRINT-ID].md` for historical reference |

---

## Sprint Status Definitions

| Status | Phase | Meaning |
|---|---|---|
| `🔲 Planning` | Planning | Sprint is being defined. Spec is not yet final. |
| `✅ Ready` | Planning → Execution | Sprint meets Definition of Ready. Awaiting PO trigger. |
| `🔄 In Progress` | Execution + Testing | Claude Developer is implementing. |
| `⏳ Awaiting CTO Review` | Review | Completion report returned. Waiting for AI CTO review. |
| `⏳ Awaiting PO Approval` | Approval | CTO approved. Waiting for Product Owner deployment decision. |
| `✅ Complete` | Archive | Sprint approved, committed, and (if applicable) deployed. |
| `❌ Blocked` | Any | Sprint cannot proceed due to unresolved dependency or decision. |
| `⛔ Cancelled` | Any | Sprint cancelled before completion. Reason in SprintReview.md. |

---

## Sprint Duration Guidance

OneMember does not use fixed-duration sprints. Sprint scope defines duration.

| Sprint Type | Typical Effort | Examples |
|---|---|---|
| Documentation | 1 session | OMOS updates, ADR writing, standards |
| Bug fix | 1 session | BUG-001, BUG-002 |
| Feature (small) | 1–2 sessions | Single controller, single model |
| Feature (medium) | 2–3 sessions | Multi-controller feature with tests |
| Audit | 1 session | AI-03 Application Audit |
| Architecture | 2–4 sessions | New module, schema changes |
| Bootstrap / Setup | 1–2 sessions | Infrastructure, tooling, OMOS updates |

**Rule:** If a sprint scope requires more than one session to estimate, it is too large. Break it into two sprints.
