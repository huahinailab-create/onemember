# NextSprintTemplate.md — Standard Sprint Specification Template

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [SprintSpecification.md](./SprintSpecification.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [Definition-of-Ready.md](./Definition-of-Ready.md), [Definition-of-Done.md](./Definition-of-Done.md), [EXECUTE.md](./EXECUTE.md) |

---

## Purpose

This template is the standard format for every sprint specification at OneMember. The AI CTO fills in this template and places the result in `SprintSpecification.md` before each sprint.

Using this template consistently means:
- Claude Developer always knows where to find each piece of information
- The Definition of Done is always explicit
- Scope is always bounded — preventing scope creep
- Risks and dependencies are always considered before implementation begins

---

## How to Use This Template

1. Copy the **Sprint Specification** section below
2. Fill in every field — do not leave sections blank (use "None" if not applicable)
3. Replace the contents of `SprintSpecification.md` with the filled-in spec
4. Update `CurrentSprint.md` — set Sprint ID and Status to `🔲 Planning`
5. Product Owner reviews and approves
6. Sprint is `✅ Ready` — send `Continue OMOS` to trigger execution

---

## Sprint Specification Template

```markdown
# Sprint [ID] — [Title]

| Field | Value |
|---|---|
| **Sprint ID** | [e.g. AI-04] |
| **Title** | [Short descriptive title] |
| **Type** | [Feature / Bug Fix / Documentation / Audit / Architecture / Hotfix] |
| **Priority** | [Critical / High / Medium / Low] |
| **Estimated Effort** | [1 session / 2 sessions / etc.] |
| **Approved By** | [Product Owner name/handle] |
| **Approval Date** | [YYYY-MM-DD] |
| **CTO** | ChatGPT CTO |
| **Developer** | Claude Sonnet 4.6 |

---

## Business Objective

[One paragraph. Answer: What will exist after this sprint that does not exist now? 
Why does this matter to merchants or the business? What metric does this improve?
Do not describe implementation — describe the outcome.]

---

## Background

[Context the developer needs to understand the problem. 
Link to relevant OMOS documents, ADRs, or bug reports.
Reference any relevant previous sprints.
Maximum 3–5 paragraphs.]

---

## Scope

[Explicit list of what IS included in this sprint. Be specific.]

- [ ] Task 1: [Description] — File(s): [path]
- [ ] Task 2: [Description] — File(s): [path]
- [ ] Task 3: [Description] — File(s): [path]

---

## Out of Scope

[Explicit list of what is NOT included in this sprint. 
This section prevents scope creep and implicit assumptions.]

- [Item 1] — [Why it is excluded / when it will be addressed]
- [Item 2] — [Why it is excluded / when it will be addressed]

---

## Requirements

[Detailed requirements for each task. More specific than the Scope section.
Include exact field names, route names, method signatures if relevant.]

### Requirement 1 — [Task Name]

[Detail]

### Requirement 2 — [Task Name]

[Detail]

---

## Acceptance Criteria

[Verifiable conditions that confirm the sprint is complete.
Each criterion must be objectively testable — either it passes or it does not.]

- [ ] [Criterion 1]
- [ ] [Criterion 2]
- [ ] [Criterion 3]
- [ ] `php artisan test` passes with zero failures
- [ ] No hardcoded secrets
- [ ] All new UI strings use `__()` helpers (if UI changes)
- [ ] Bootstrap 5 only — no Tailwind (ADR-005)
- [ ] All email sent via Events/Listeners — not directly from controllers (CTO-003)
- [ ] All new models with JSON columns use the null-safe Attribute accessor pattern (CTO-008)

---

## Risks

[Known risks or unknowns that may affect implementation.
For each risk: describe it, rate likelihood (Low/Med/High), and describe mitigation.]

| Risk | Likelihood | Mitigation |
|---|---|---|
| [Risk 1] | [Low/Med/High] | [What to do if it materialises] |
| [Risk 2] | [Low/Med/High] | [What to do if it materialises] |

---

## Dependencies

[Other sprints, external systems, or decisions that must exist before this sprint can complete.]

| Dependency | Type | Status |
|---|---|---|
| [ADR-XXX] | Architecture Decision | [Approved / Pending] |
| [Previous Sprint] | Sprint | [Complete / Pending] |
| [External API] | Third Party | [Available / TBD] |

---

## Testing Requirements

[Specific tests that must be written as part of this sprint, beyond the default test suite.]

- [ ] Test: [Describe what must be tested and why]
- [ ] Test: [Edge case to cover]
- [ ] Regression: [Existing test that may be affected — verify it still passes]

---

## Definition of Done

This sprint is complete when ALL of the following are true:

- [ ] All tasks in the Scope section are implemented
- [ ] All Acceptance Criteria are met
- [ ] `php artisan test` passes with zero failures
- [ ] All new tests written and passing
- [ ] No hardcoded secrets or credentials
- [ ] Code committed with the sprint commit message
- [ ] `CurrentSprint.md` updated — status set to `⏳ Awaiting CTO Review`
- [ ] Completion report returned to Product Owner
- [ ] Claude Developer has stopped and is waiting for CTO review

---

## Commit Message

```
Sprint [ID] — [Title]

[Optional: 1–2 sentence summary of what changed and why]

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

---

## Deliverables

[What the completion report must include, beyond the standard EXECUTE.md protocol.]

- [Deliverable 1 — e.g. "Health score with breakdown by category"]
- [Deliverable 2 — e.g. "Full list of files audited"]
- [Deliverable 3 — e.g. "Recommended next sprint based on findings"]

---

## Related Documents

[Links to OMOS documents the developer must read before implementing this sprint.]

- [Document 1](./path/to/document.md) — [Why it is relevant]
- [Document 2](./path/to/document.md) — [Why it is relevant]

---

## Technical Notes

[CTO guidance on implementation approach, patterns to use, patterns to avoid.
Reference specific CTO-Decisions where relevant.]

- Follow CTO-008 pattern for all nullable JSON columns
- Follow CTO-009 for all `@json()` usage in Blade
- Use the `abort_unless()` pattern for all tenant isolation checks
- All routes must use named route helpers (`route('name')`) — no hardcoded URLs
```

---

## Quick Sprint Type Reference

Different sprint types have different standard requirements:

| Sprint Type | Testing | Migration | UI | Email |
|---|---|---|---|---|
| Feature | New tests required | May have migration | Blade views | Via Events only |
| Bug Fix | Regression test required | Rarely | Minimal | Via Events only |
| Documentation | No tests required | Never | Never | Never |
| Audit | No tests required | Never | Never | Never |
| Architecture | Tests for new patterns | May have migration | Never | Never |
| Hotfix | Regression test required | Avoid in hotfix | Minimal | Via Events only |
