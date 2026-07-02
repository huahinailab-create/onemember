# Architecture Decision Records (ADR)

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [ai/07-Architecture-Rules.md](../../../ai/07-Architecture-Rules.md), [13-RFC/README.md](../13-RFC/README.md), [docs/08-Product-Decisions.md](../../08-Product-Decisions.md) |

---

## Purpose

Architecture Decision Records (ADRs) document every significant technical decision made in the OneMember project. Each ADR records:

- **What** was decided
- **Why** it was decided (context, options considered, rationale)
- **What the consequences are** (trade-offs accepted)

ADRs are immutable. Once approved, an ADR is never edited — it is either still active, or it is superseded by a newer ADR. This means you can always trace back to any historical decision and understand why it was made at the time.

---

## When to Write an ADR

Write an ADR when:

- Choosing a technology, library, or service that will affect multiple parts of the system
- Making a database schema choice that will be hard to change later
- Deciding on an architectural pattern (e.g., event-driven email, multi-tenant data model)
- Choosing NOT to do something that might seem obvious (document why)
- Reversing a previous decision

Do NOT write an ADR for:
- Implementation details that can be changed by a single developer in a single sprint
- Decisions that are already covered by `ai/07-Architecture-Rules.md` (those are standing rules, not decisions to be re-made)
- Bug fixes

---

## Naming Convention

```
ADR-[NNN]-[kebab-case-title].md
```

Numbers are sequential, zero-padded to three digits. Never reuse a number.

Examples:
- `ADR-001-Laravel-As-Application-Framework.md`
- `ADR-002-Resend-As-Email-Provider.md`
- `ADR-003-Database-Session-Driver.md`
- `ADR-004-Queued-Email-Verification.md`

---

## ADR Template

Every ADR must use this exact structure:

```markdown
# ADR-[NNN] — [Title]

| Field | Value |
|---|---|
| **Status** | [Proposed / Approved / Superseded / Deprecated] |
| **Date** | YYYY-MM-DD |
| **Author** | [ChatGPT CTO / Product Owner] |
| **Supersedes** | [ADR-NNN / None] |
| **Superseded by** | [ADR-NNN / None] |
| **Related Documents** | [links] |

---

## Context

[Describe the situation and the problem that needs a decision.
What forces are at play? What constraints exist?
What are the consequences of not deciding?]

## Decision

[State the decision clearly and unambiguously.
"We will use X" or "We will not use Y."]

## Options Considered

### Option A — [Name]
[Description]
**Pros:** ...
**Cons:** ...

### Option B — [Name]
[Description]
**Pros:** ...
**Cons:** ...

### Option C — [Name] *(chosen)*
[Description]
**Pros:** ...
**Cons:** ...

## Rationale

[Explain why the chosen option was selected over the alternatives.
Reference the context, constraints, and principles that drove the decision.]

## Consequences

### Positive
- [What gets better]

### Negative
- [What gets harder or what we give up]

### Risks
- [What could go wrong and how we mitigate it]

## Validation

[How will we know if this decision was correct? What are the early warning signs that it was wrong?]
```

---

## ADR Lifecycle

```
[Proposed]
    │
    │  Product Owner and CTO review
    ▼
[Approved] ────────────────────────────────────┐
    │                                           │
    │  A better option is found, or            │
    │  circumstances change significantly       │
    ▼                                           │
[Superseded] ← superseded by ADR-NNN           │
                                               │
[Deprecated] ← decision is no longer relevant  │
                (technology removed, etc.)      │
```

---

## ADR Status Definitions

| Status | Meaning |
|---|---|
| `Proposed` | Written and under review. Not yet in force. |
| `Approved` | Reviewed and accepted by Product Owner and CTO. In force immediately. |
| `Superseded` | Replaced by a newer ADR. Link to replacement required. The old ADR is kept for history. |
| `Deprecated` | No longer relevant (e.g., the technology was removed from the project). Kept for history. |

---

## ADR Index

| ADR | Title | Status | Date |
|---|---|---|---|
| ADR-001 | *(To be written — Laravel as application framework)* | Proposed | — |
| ADR-002 | *(To be written — Resend as email transport)* | Proposed | — |
| ADR-003 | *(To be written — Database session driver)* | Proposed | — |
| ADR-004 | *(To be written — Queued email verification)* | Proposed | — |
| ADR-005 | *(To be written — Single-database multi-tenancy)* | Proposed | — |
| ADR-006 | *(To be written — Bootstrap 5 as frontend framework)* | Proposed | — |

> **Note:** The decisions captured here are derived from `ai/07-Architecture-Rules.md` and prior sprint work. Full ADR documents will be written in Sprint AI-02B or a dedicated architecture sprint.
