# Requests for Comment (RFC)

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [12-ADR/README.md](../12-ADR/README.md), [ai/01-CTO-Instructions.md](../../../ai/01-CTO-Instructions.md), [ai/04-Sprint-Workflow.md](../../../ai/04-Sprint-Workflow.md) |

---

## Purpose

A Request for Comment (RFC) is a proposal for a significant change that needs review before a decision is made. RFCs are used when:

- A proposed change affects multiple parts of the system
- The best approach is not yet clear and input is needed
- A change to an established standard or architectural rule is being proposed
- A new feature area requires defining how it fits the product before building it

An RFC is not an ADR. An RFC is the deliberation; an ADR is the final decision. A successful RFC leads to an ADR (or a sprint spec, or both).

---

## When to Open an RFC

Open an RFC when:

- Proposing a new product area (e.g., "How should we design the customer wallet consent model?")
- Suggesting a change to an architectural rule in `ai/07-Architecture-Rules.md`
- Proposing a new standard in `docs/OMOS/11-Standards/`
- A sprint spec has dependencies that haven't been resolved
- The Product Owner or CTO disagrees on an approach and wants structured deliberation

Do NOT use an RFC for:
- Routine sprint work — use a sprint spec
- Bug fixes — use a BUG sprint
- Documentation-only work — just update the document

---

## Naming Convention

```
RFC-[NNN]-[kebab-case-title].md
```

Numbers are sequential, zero-padded to three digits.

Examples:
- `RFC-001-Customer-Wallet-Consent-Model.md`
- `RFC-002-PromptPay-Integration-Architecture.md`
- `RFC-003-Point-Expiry-Job-Design.md`

---

## RFC Template

Every RFC must use this exact structure:

```markdown
# RFC-[NNN] — [Title]

| Field | Value |
|---|---|
| **Status** | [Open / Under Review / Accepted / Rejected / Withdrawn] |
| **Author** | [Name or role] |
| **Date Opened** | YYYY-MM-DD |
| **Date Closed** | YYYY-MM-DD or — |
| **Outcome** | [ADR-NNN / Sprint ID / No action / Rejected] |
| **Related Documents** | [links] |

---

## Summary

[2–4 sentences. What is being proposed and why now?]

---

## Motivation

[Why does this need to change or be decided?
What problem does it solve? What happens if we don't decide?]

---

## Proposed Solution

[Describe the proposed approach in enough detail to evaluate it.
Include diagrams, schemas, or API sketches if helpful.]

---

## Alternatives Considered

### Alternative A — [Name]
[Description and why it was not chosen as the proposal]

### Alternative B — [Name]
[Description and why it was not chosen as the proposal]

---

## Impact Assessment

### Systems affected
- [List affected services, models, routes, views]

### Breaking changes
- [List any backwards-incompatible changes]

### Migration plan
- [How to transition from the current state to the proposed state]

### Security considerations
- [Any security implications of this change]

---

## Open Questions

- [ ] [Question 1 — who needs to answer it]
- [ ] [Question 2 — who needs to answer it]

---

## Decision

*(Filled in after review)*

**Resolution:** [Accepted / Rejected / Withdrawn]

**Rationale:** [Why this decision was made]

**Next steps:** [ADR to write / Sprint to create / No action]
```

---

## RFC Approval Workflow

```
1. Author (CTO or Product Owner) opens RFC by creating the file
2. RFC Status = "Open"
3. Product Owner and CTO discuss (via conversation, not in the file)
4. Questions and answers are added to the Open Questions section
5. A decision is made:
   - Accepted → write ADR, create sprint spec if needed
   - Rejected → document why, close RFC
   - Withdrawn → author takes it back, document why
6. RFC Status updated to final state
7. RFC remains in 13-RFC/ folder permanently (never deleted)
```

---

## RFC Status Definitions

| Status | Meaning |
|---|---|
| `Open` | RFC filed, under discussion |
| `Under Review` | Formal review in progress |
| `Accepted` | Decision made: implement as proposed (or with modifications noted in Decision section) |
| `Rejected` | Decision made: do not implement |
| `Withdrawn` | Author cancelled the RFC before a decision was reached |

---

## RFC Index

| RFC | Title | Status | Opened | Outcome |
|---|---|---|---|---|
| [RFC-001](./RFC-001-Template.md) | RFC Template Reference | Active | 2026-07-02 | Template |
| [RFC-002](./RFC-002-Feature-Proposal.md) | Feature Proposal Template | Template | 2026-07-02 | Template |
| [RFC-003](./RFC-003-Architecture-Change.md) | Architecture Change Template | Template | 2026-07-02 | Template |