# RFC-001 — RFC Template Reference

| Field | Value |
|---|---|
| **Status** | Active |
| **Author** | ChatGPT CTO |
| **Date Opened** | 2026-07-02 |
| **Date Closed** | — |
| **Outcome** | Template — not a real RFC |
| **Related Documents** | [README.md](./README.md), [12-ADR/README.md](../12-ADR/README.md), [00-Executive/Decision-Framework.md](../00-Executive/Decision-Framework.md) |

---

## Purpose

This file is the reference template for all future RFCs. When creating a new RFC:

1. Copy this file
2. Rename it `RFC-[NNN]-[Kebab-Case-Title].md`
3. Replace all `[placeholder]` text with real content
4. Set Status to `Open`
5. Add to the RFC Index in `README.md`

---

## Template

```markdown
# RFC-[NNN] — [Title]

| Field | Value |
|---|---|
| **Status** | Open |
| **Author** | [ChatGPT CTO / Product Owner] |
| **Date Opened** | YYYY-MM-DD |
| **Date Closed** | — |
| **Outcome** | Pending |
| **Related Documents** | [links to relevant ADRs, product docs, sprint specs] |

---

## Summary

[2–4 sentences. What is being proposed and why now? What decision needs to be made?]

---

## Motivation

[Why does this need to change or be decided?
What problem does it solve? What happens if we do not decide?
What is the cost of inaction?]

---

## Proposed Solution

[Describe the proposed approach in enough detail to evaluate it.
Include diagrams, schemas, or API sketches if helpful.
Be specific about what would change and what would stay the same.]

---

## Alternatives Considered

### Alternative A — [Name]
[Description]
**Why not chosen as the proposal:** [Reason]

### Alternative B — [Name]
[Description]
**Why not chosen as the proposal:** [Reason]

---

## Impact Assessment

### Systems affected
- [List affected services, models, routes, views, tables]

### Breaking changes
- [List any backwards-incompatible changes]
- [None] if there are no breaking changes

### Migration plan
- [How to transition from the current state to the proposed state]
- [What happens to existing data]

### Security considerations
- [Any security implications of this change]
- [None identified] if there are no security concerns

### Privacy considerations
- [Any privacy or data handling implications]

---

## Open Questions

- [ ] [Question 1 — who needs to answer it and by when]
- [ ] [Question 2 — who needs to answer it and by when]

---

## Decision

*(Filled in after review is complete)*

**Resolution:** [Accepted / Rejected / Withdrawn]

**Rationale:** [Why this decision was made — specific, not vague]

**Next steps:**
- [ADR-NNN to write]
- [Sprint spec to create]
- [No further action required]
```

---

## RFC Checklist Before Submitting

- [ ] Summary is 2–4 sentences and clearly states what is being proposed
- [ ] Motivation explains WHY this matters and what the cost of inaction is
- [ ] At least 2 alternatives are considered and rejected with reasons
- [ ] Impact assessment covers all affected systems
- [ ] Open Questions are specific and have owners
- [ ] Status is set to `Open`
- [ ] Added to RFC Index in README.md
