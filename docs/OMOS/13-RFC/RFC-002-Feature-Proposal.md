# RFC-002 — Feature Proposal Template

| Field | Value |
|---|---|
| **Status** | Template |
| **Author** | ChatGPT CTO |
| **Date Opened** | 2026-07-02 |
| **Date Closed** | — |
| **Outcome** | Template — not a real RFC |
| **Related Documents** | [RFC-001-Template.md](./RFC-001-Template.md), [02-Product/MVP-Strategy.md](../02-Product/MVP-Strategy.md), [00-Executive/Decision-Framework.md](../00-Executive/Decision-Framework.md) |

---

## Purpose

This is the template for proposing a new product feature that is significant enough to require deliberation before a sprint spec is written.

Use this template when:
- A feature affects multiple modules or crosses architectural boundaries
- The correct approach is not obvious and input from both PO and CTO is needed
- A feature has privacy, security, or regulatory implications that need evaluation
- The feature is in the Parking Lot and is being reconsidered

Do NOT use this template for routine sprint features. Those go directly into the sprint specification.

---

## Template

```markdown
# RFC-[NNN] — [Feature Name] Proposal

| Field | Value |
|---|---|
| **Status** | Open |
| **Author** | [Product Owner / ChatGPT CTO] |
| **Date Opened** | YYYY-MM-DD |
| **Date Closed** | — |
| **Outcome** | Pending |
| **Related Documents** | [Parking-Lot.md if applicable], [relevant Product Bible section], [ADRs affected] |

---

## Summary

[What feature is being proposed? 2–4 sentences.]

---

## User Story

As a [merchant / customer / staff member],  
I want to [do something],  
So that [I achieve some outcome].

**Acceptance criteria:**
- [ ] [Measurable criterion 1]
- [ ] [Measurable criterion 2]
- [ ] [Measurable criterion 3]

---

## Motivation

**Problem it solves:**
[What specific problem does this feature solve? Which persona does it serve?
Reference the Merchant Personas or Customer Personas documents.]

**Why now:**
[Why is this the right time to build this feature?
What has changed (market, user feedback, technical readiness) that makes this timely?]

**Impact on North Star Metric:**
[How will this feature affect the North Star Metric (customer joins ≤ 30 seconds)?]

---

## Feature Description

[Detailed description of how the feature works from the user's perspective.
Include:
- Entry point (how does the user find/start this feature?)
- Core flow (step by step what happens)
- Edge cases (what happens when things go wrong?)
- Success state (what does success look like for the user?)
]

---

## Technical Considerations

**Affected models / tables:**
- [Model 1 — what changes]

**New tables required:**
- [Table name — purpose]

**Architecture concerns:**
- [Concern 1 — how to handle]

**ADRs that apply:**
- [ADR-004 (Laravel) — relevant because...]

---

## Alternatives Considered

### Alternative A — [Name]
[Description and why not proposed]

---

## Impact Assessment

### Systems affected
- [List]

### Breaking changes
- [None / List]

### Security considerations
- [List or "None identified"]

### Privacy considerations
- [List or "None identified"]

### Localisation requirements
- [Any new strings, date formats, currency considerations]

---

## Feature Score (Decision Framework)

| Criterion | Score (0-2) | Rationale |
|---|---|---|
| Merchant Value | | |
| Customer Impact | | |
| NSM Alignment | | |
| Architecture Fit | | |
| Maintainability | | |
| Data Responsibility | | |
| Strategic Fit | | |
| **Total** | | |

---

## Open Questions

- [ ] [Question — Owner — Due Date]

---

## Decision

*(Filled in after review)*

**Resolution:** [Accepted / Rejected / Withdrawn]

**Next steps:**
- [ ] Write sprint specification
- [ ] Write ADR-NNN if architectural decision required
```
