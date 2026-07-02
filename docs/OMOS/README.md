# OMOS — OneMember Operating System

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [ai/00-Roles.md](../../ai/00-Roles.md), [ai/04-Sprint-Workflow.md](../../ai/04-Sprint-Workflow.md), [CurrentSprint.md](./CurrentSprint.md) |

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

## How OMOS Relates to the AI Development System

The `ai/` folder contains the **operating rules for the AI development team** — how sprints are written, how roles work, what quality gates must pass.

OMOS is the **content** those rules operate on — the product strategy, technical decisions, brand standards, and architecture that every sprint must respect.

```
ai/          → HOW we work (process, roles, workflow)
docs/OMOS/   → WHAT we know and have decided (content, standards, strategy)
```

Every sprint spec written by the ChatGPT CTO should reference relevant OMOS documents. Claude Developer must read the cited OMOS documents before implementing.

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

`docs/OMOS/CurrentSprint.md` is the **live sprint board**. It contains exactly one sprint at a time — the sprint currently in progress.

**Before starting a sprint:** The ChatGPT CTO fills in the template and the Product Owner approves it.

**During a sprint:** Claude updates the Tasks section as work progresses.

**After a sprint:** Claude marks the sprint complete and the file is archived to `sprints/` before the next sprint begins.

This file is always the first thing Claude reads at the start of a session.

---

## Folder Map

```
docs/OMOS/
├── README.md               ← You are here. Start here.
├── CurrentSprint.md        ← Active sprint. Always read first.
│
├── 00-Executive/           ← Vision, Mission, North Star, Principles
├── 01-Company/             ← Culture, team, hiring, values in practice
├── 02-Product/             ← Product Bible, feature specs, UX principles
├── 03-Business/            ← Business model, pricing, partnerships
├── 04-Technology/          ← Technology choices, integrations, third parties
├── 05-Engineering/         ← Coding standards, PR process, testing philosophy
├── 06-Operations/          ← Deployment, monitoring, incident response, SLAs
├── 07-Brand/               ← Visual identity, voice, design system
├── 08-Security/            ← Security policy, threat model, compliance
├── 09-Roadmap/             ← Product roadmap, phases, milestones
├── 10-Architecture/        ← System architecture, data models, integrations
├── 11-Standards/           ← All coding, API, DB, UI, testing standards
├── 12-ADR/                 ← Architecture Decision Records
├── 13-RFC/                 ← Requests for Comment (proposals in review)
├── 14-Research/            ← Market research, competitor analysis, user research
├── 15-Legal/               ← Terms, privacy, compliance, PDPA
└── 16-Appendix/            ← Glossary, reference tables, archived drafts
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
1. `CurrentSprint.md` — the active sprint spec
2. `11-Standards/Coding-Standards.md`
3. `11-Standards/Bootstrap-Standards.md`
4. `07-Brand/Brand-Standards.md`
5. `11-Standards/Testing-Standards.md`
6. Any specific documents cited in the sprint spec

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
