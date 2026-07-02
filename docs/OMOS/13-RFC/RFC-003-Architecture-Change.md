# RFC-003 — Architecture Change Template

| Field | Value |
|---|---|
| **Status** | Template |
| **Author** | ChatGPT CTO |
| **Date Opened** | 2026-07-02 |
| **Date Closed** | — |
| **Outcome** | Template — not a real RFC |
| **Related Documents** | [RFC-001-Template.md](./RFC-001-Template.md), [12-ADR/README.md](../12-ADR/README.md), [CTO-Decisions.md](../CTO-Decisions.md) |

---

## Purpose

This template is for proposing changes to established architectural decisions — changes to existing ADRs, modifications to the technology stack, or new architectural patterns that conflict with existing standards.

Use this template when:
- Proposing to change an existing ADR (e.g., adding a new framework, changing the database driver)
- Introducing a new architectural pattern that does not exist in the current codebase
- Proposing to change the deployment infrastructure
- Any change that would affect more than 30% of the codebase

Every architecture change RFC, if accepted, produces a new ADR that either supersedes the existing one or adds to the existing ADR system.

---

## Template

```markdown
# RFC-[NNN] — [Architecture Change Title]

| Field | Value |
|---|---|
| **Status** | Open |
| **Author** | ChatGPT CTO |
| **Date Opened** | YYYY-MM-DD |
| **Date Closed** | — |
| **Outcome** | Pending |
| **Related Documents** | [ADR being changed or superseded], [affected standards documents], [CTO-Decisions.md] |

---

## Summary

[2–4 sentences. What architectural change is being proposed and why?]

---

## Current State

[Describe the current architecture that would change.
Reference the ADR that governs the current approach.
Be specific about what currently exists.]

**Governed by:** ADR-[NNN] — [Title]

---

## Proposed Change

[Describe the new architecture.
What replaces what? What is added? What is removed?
How does the new architecture differ from the current one?]

---

## Motivation

**Why the current approach is no longer sufficient:**
[Specific problem with the current architecture that this change addresses.
Do not propose a change just because a newer technology exists.]

**What this change enables:**
[What becomes possible after this change that was not possible before.]

**Cost of not changing:**
[What happens if we stay with the current architecture?]

---

## Migration Plan

**Phase 1 — Preparation:**
[What needs to happen before the change can begin.]

**Phase 2 — Migration:**
[How existing code, data, or configuration is migrated to the new approach.
How long will this take? Can it be done incrementally?]

**Phase 3 — Validation:**
[How we confirm the migration is complete and correct.
What tests prove the new architecture works?]

**Rollback plan:**
[What we do if the migration fails partway through.
Is rollback possible? How long does it take?]

---

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| [Risk 1] | High/Med/Low | High/Med/Low | [Mitigation] |
| [Risk 2] | High/Med/Low | High/Med/Low | [Mitigation] |

---

## Impact Assessment

### Files/components affected
- [List specific files, models, controllers, or layers that change]

### Breaking changes
- [What breaks immediately when this change is made]
- [What existing tests will fail and need to be updated]

### New infrastructure required
- [New servers, services, or packages required]

### Security implications
- [Any new attack surface or security considerations]

---

## Open Questions

- [ ] [Question — Owner — Due Date]

---

## Decision

*(Filled in after review)*

**Resolution:** [Accepted / Rejected / Withdrawn]

**New ADR required:** [ADR-NNN — Title]  
**Existing ADR superseded:** [ADR-NNN — or None]

**Next steps:**
- [ ] Write ADR-NNN
- [ ] Create migration sprint specification
- [ ] Update affected standards documents
```
