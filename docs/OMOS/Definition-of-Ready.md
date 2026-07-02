# Definition-of-Ready.md — Sprint Entry Requirements

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Definition-of-Done.md](./Definition-of-Done.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [NextSprintTemplate.md](./NextSprintTemplate.md), [SprintSpecification.md](./SprintSpecification.md), [EXECUTE.md](./EXECUTE.md) |

---

## What Is the Definition of Ready?

A sprint is **Ready** when it has all the information Claude Developer needs to implement it without making product or architecture decisions independently.

A sprint that is NOT Ready will cause Claude Developer to stop mid-implementation and ask for clarification — wasting both time and context. The Definition of Ready prevents this by ensuring completeness before the sprint starts.

The AI CTO must verify the Definition of Ready before publishing a sprint specification. The Product Owner approves the sprint once this checklist is satisfied.

---

## Definition of Ready Checklist

A sprint may not enter execution until every item below is checked.

### Business and Context
- [ ] **Sprint ID is assigned** — follows naming convention: `AI-[NN]`, `HOTFIX-[N]`, `BUG-[NNN]`
- [ ] **Business Objective is written** — one paragraph explaining what will exist that does not exist now, and why it matters
- [ ] **Background is complete** — provides context needed to understand the problem; links to relevant OMOS documents
- [ ] **Previous sprint is complete** — `CurrentSprint.md` shows the previous sprint as `✅ Complete` or `⏳ Awaiting CTO Review` (not `🔄 In Progress`)

### Scope and Boundaries
- [ ] **All tasks have specific file paths** — no task says "update the settings page" without naming the file
- [ ] **All tasks have verifiable outcomes** — not "improve UX" but "form redirects to `/campaigns` with success message"
- [ ] **Out of Scope is explicitly listed** — at minimum 1–2 items confirming what is NOT being done
- [ ] **No task requires a decision not covered by existing ADRs or CTO-Decisions** — if it does, the ADR/RFC must be created and approved first

### Requirements and Criteria
- [ ] **Requirements section is complete** — each task has enough detail that implementation requires no guessing
- [ ] **Acceptance Criteria are objectively testable** — pass/fail, not subjective
- [ ] **Testing Requirements are defined** — new tests that must be written are listed explicitly
- [ ] **Definition of Done is filled in** — sprint-specific criteria added on top of the standard checklist

### Architecture and Standards
- [ ] **No ADR violations** — spec does not require implementing anything that contradicts an existing ADR
- [ ] **No CEO-Decision violations** — spec does not require anything prohibited in `CEO-Decisions.md`
- [ ] **No new Type 3/4 decisions** — if the sprint requires a new architecture or strategic decision, an ADR/RFC must be in place before the sprint starts
- [ ] **Bootstrap 5 confirmed** — any UI work specifies Bootstrap 5 components, not Tailwind
- [ ] **Event-driven email confirmed** — any email work routes through Events/Listeners, not controllers

### Dependencies
- [ ] **All dependencies are resolved** — no task depends on an incomplete sprint, unmerged ADR, or external system that is not confirmed available
- [ ] **Migration safety confirmed** — if the sprint includes migrations, `down()` methods exist and have been verified

### Documentation
- [ ] **Related Documents list is complete** — every OMOS document Claude needs to read before implementing is listed
- [ ] **Commit message is pre-written** — the exact commit message is in the spec's Commit Message section

---

## What Happens If a Sprint Is Not Ready

If the AI CTO publishes a sprint specification that fails the Definition of Ready, Claude Developer must:

1. Output the specific items that are not satisfied
2. Stop — do not implement any part of the sprint
3. Wait for the CTO to update the spec

**Example output from Claude Developer:**
```
⛔ SPRINT NOT READY — AI-05

The following Definition of Ready items are not satisfied:

1. Task 3 ("update onboarding flow") does not specify which file(s)
2. Task 3 requires a migration — no down() method guidance provided  
3. No Related Documents listed — which OMOS docs should I read?

Awaiting spec update before implementation begins.
```

The sprint re-enters Planning until the spec is updated and the Definition of Ready is satisfied.
