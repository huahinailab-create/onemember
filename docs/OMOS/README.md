# OMOS — OneMember Operating System

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [CurrentSprint.md](./CurrentSprint.md), [EXECUTE.md](./EXECUTE.md), [AI-Workflow.md](./AI-Workflow.md) |

---

## What Is OMOS?

OMOS (OneMember Operating System) is the single source of truth for everything that governs how OneMember is built, run, and grown.

It is not a static document. It is a living system — a structured library of decisions, standards, strategies, and processes that every person and AI working on OneMember must read before acting.

**OMOS answers the question: "How do we do things at OneMember?"**

---

## Why It Exists

Every growing product eventually runs into the same failure mode: decisions made months ago are forgotten, repeated badly, or contradicted by new work. Standards drift. New contributors (human or AI) make assumptions that conflict with established choices.

OMOS exists to prevent that. It captures the reasoning behind every significant decision so that:

- The Product Owner can delegate with confidence.
- The ChatGPT CTO can make architecture recommendations grounded in the actual product strategy.
- Claude Developer can implement without ambiguity.
- Future team members can onboard without re-litigating settled questions.

---

## How OMOS Relates to the Sprint System

OMOS operates on two layers:

- **Governance layer** — `EXECUTE.md`, `AI-Workflow.md`, `Sprint-Lifecycle.md`, `Definition-of-Ready.md`, `Definition-of-Done.md` define HOW we work: roles, process, quality gates.
- **Content layer** — `00-Executive/` through `16-Appendix/` define WHAT we know and have decided: product strategy, technical standards, brand, architecture.

```
Governance (EXECUTE.md, AI-Workflow.md, Sprint-Lifecycle.md...)  → HOW we work
Content (00-Executive/, 02-Product/, 12-ADR/...)                  → WHAT we know
```

Every sprint spec written by the AI CTO should reference relevant content-layer documents. Claude Developer must read the cited documents before implementing.

---

## How OMOS Relates to the Product Bible

The Product Bible (`docs/OMOS/02-Product/Product-Bible.md`) is the most important single document in OMOS. It defines what OneMember is, who it serves, and how each product area works.

All other OMOS documents either support the Product Bible or derive from it:

- Architecture decisions serve the product requirements in the Bible.
- Brand standards express the identity defined in the Bible.
- The Roadmap prioritises the features the Bible describes.

When the Product Bible changes, all related OMOS documents must be reviewed for consistency.

---

## How to Use CurrentSprint.md

`docs/OMOS/CurrentSprint.md` is the **live sprint board**. It shows the active sprint status, previous sprint, and sprint history.

**Before starting a sprint:** The AI CTO writes the spec in `SprintSpecification.md` using `NextSprintTemplate.md`. The Product Owner approves, then sends `Continue OMOS`.

**During a sprint:** Claude Developer executes tasks as defined in `SprintSpecification.md`.

**After a sprint:** Claude Developer updates `CurrentSprint.md` — sets status to `⏳ Awaiting CTO Review`, records the commit hash, and stops.

See [Sprint-Lifecycle.md](./Sprint-Lifecycle.md) for the complete 8-phase workflow.

---

## Folder Map

```
docs/OMOS/
├── README.md                  ← You are here. Start here.
├── EXECUTE.md                 ← Claude Developer operating protocol. Read every session.
├── CurrentSprint.md           ← Active sprint board. Always read first.
├── SprintSpecification.md     ← Active sprint specification.
├── AI-Workflow.md             ← Roles, responsibilities, review and approval process.
├── AI-CTO-Handoff.md          ← How AI CTO hands work to Claude Developer.
├── Sprint-Lifecycle.md        ← All 8 sprint phases with entry/exit gates.
├── NextSprintTemplate.md      ← Standard template for all sprint specifications.
├── Definition-of-Ready.md     ← Checklist: when a sprint may begin execution.
├── Definition-of-Done.md      ← Checklist: when a sprint is complete.
├── CEO-Decisions.md           ← Strategic decisions made by the Product Owner.
├── CTO-Decisions.md           ← Technical standards set by the AI CTO.
├── Known-Constraints.md       ← Technical, business, and security constraints.
├── Assumptions.md             ← Documented product and technical assumptions.
├── SprintReview.md            ← Sprint review history.
│
├── Audits/                    ← Application audit reports
│   └── AI-03-Application-Audit.md
│
├── Knowledge/                 ← Research, interviews, experiments, ideas
│
├── 00-Executive/              ← Vision, Mission, North Star, Principles
├── 01-Company/                ← Culture, team, hiring, values in practice
├── 02-Product/                ← Product Bible, feature specs, UX principles
├── 03-Business/               ← Business model, pricing, partnerships
├── 04-Technology/             ← Technology choices, integrations, third parties
├── 05-Engineering/            ← Coding standards, PR process, testing philosophy
├── 06-Operations/             ← Deployment, monitoring, incident response, SLAs
├── 07-Brand/                  ← Visual identity, voice, design system
├── 08-Security/               ← Security policy, threat model, compliance
├── 09-Roadmap/                ← Product roadmap, phases, milestones
├── 10-Architecture/           ← System architecture, data models, integrations
├── 11-Standards/              ← All coding, API, DB, UI, testing standards
├── 12-ADR/                    ← Architecture Decision Records
├── 13-RFC/                    ← Requests for Comment (proposals in review)
├── 14-Research/               ← Market research, competitor analysis, user research
├── 15-Legal/                  ← Terms, privacy, compliance, PDPA
└── 16-Appendix/               ← Glossary, reference tables, archived drafts
```

---

## Recommended Reading Order

**For the Product Owner (first time):**
1. `00-Executive/Vision.md`
2. `00-Executive/Mission.md`
3. `00-Executive/North-Star-Metric.md`
4. `02-Product/Product-Bible.md`
5. `09-Roadmap/Roadmap.md`

**For the ChatGPT CTO (before writing a sprint spec):**
1. `CurrentSprint.md` — read the previous sprint context
2. `02-Product/Product-Bible.md` — confirm the feature fits the product
3. `10-Architecture/` — check architectural constraints
4. `12-ADR/` — check prior decisions
5. `11-Standards/` — confirm the spec respects all standards

**For Claude Developer (before implementing):**
1. `EXECUTE.md` — read the protocol
2. `CurrentSprint.md` — identify the active sprint
3. `SprintSpecification.md` — read the full sprint spec
4. Any specific documents cited in the sprint spec's Related Documents section

---

## How to Update OMOS

### Adding a new document
1. Create the file in the correct folder.
2. Use the standard metadata header (Owner, Version, Status, Last Updated, Related Documents).
3. Add a link to it in this README's Folder Map section.
4. If it is an architecture decision, use the ADR format in `12-ADR/`.
5. If it is a proposal under review, use the RFC format in `13-RFC/`.

### Updating an existing document
1. Increment the version number (semantic: `1.0.0` → `1.1.0` for minor, `2.0.0` for breaking change).
2. Update the Last Updated date.
3. If the document supersedes a decision, update the relevant ADR status to `Superseded`.

### What NOT to do
- Do not delete documents — mark them `Deprecated` or `Superseded`.
- Do not make undocumented changes to standards — open an RFC first.
- Do not edit OMOS documents during a sprint without flagging it as a scope change.

---

## Document Status Definitions

| Status | Meaning |
|---|---|
| `Draft` | Being written, not yet authoritative |
| `Review` | Complete draft, awaiting Product Owner approval |
| `Active` | Approved and in force |
| `Superseded` | Replaced by a newer document (link to replacement) |
| `Deprecated` | No longer applicable, kept for historical reference |
| `Archived` | Moved out of active use, stored in `16-Appendix/` |
