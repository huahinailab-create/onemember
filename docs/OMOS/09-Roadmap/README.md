# Roadmap — Concepts and Documents

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Long-term-Roadmap.md](./Long-term-Roadmap.md), [../Sprints/Backlog.md](../Sprints/Backlog.md), [../02-Product/Release-Philosophy.md](../02-Product/Release-Philosophy.md) |

---

## Three Concepts: Roadmap vs Sprint vs Release

These three concepts are often confused. This document defines them clearly and explains how they connect.

---

### Roadmap

The roadmap describes the **strategic direction over 2–5 years**.

It answers: *What will OneMember look like in each phase of development?*

The roadmap is written at the phase level — Phase 1, Phase 2, Phase 3, Phase 4. It defines what modules exist at the end of each phase and the exit criteria to move from one phase to the next. It does not define individual features, and it does not define timelines in weeks or months.

**The roadmap changes when strategy changes.** It is not a commitment to build specific features in a specific order — it is a direction.

**Documents:**
- [Long-term-Roadmap.md](./Long-term-Roadmap.md) — the 4-phase platform evolution narrative

---

### Sprint

A sprint describes **specific technical work to be done in a single session**.

It answers: *What is Claude Developer implementing right now, and exactly how?*

A sprint is a precise technical specification — task list, file paths, acceptance criteria, commit message. It is scoped to what one developer (Claude Developer) can implement in one session.

Sprints are derived from the roadmap but are not part of it. The roadmap says "build loyalty campaign engine in Phase 1". The sprint says "create `CampaignController`, `LoyaltyProgram` model, `campaigns` routes, migration — file paths and acceptance criteria listed below".

**Sprints change every session.** They are the operational layer of the roadmap.

**Documents:**
- [../CurrentSprint.md](../CurrentSprint.md) — the active sprint status and reference
- [../Sprints/](../Sprints/) — the sprint library (all sprint specifications)
- [../Sprints/Backlog.md](../Sprints/Backlog.md) — planned but not yet active sprints

---

### Release

A release describes **what goes to production and when**.

It answers: *What is the merchant-facing change in this deployment?*

A release can contain one sprint or multiple sprints. Releases are approved by the Product Owner and deployed by the Product Owner. Claude Developer cannot create a release.

Releases are named by their user-visible impact: "Merchant Experience Polish v1", "Birthday Automation launch", "Customer Wallet beta". They are not the same as sprint IDs.

**Releases are the public-facing layer of the sprint.** A sprint produces a commit. A release deploys one or more commits to production.

**Documents:**
- [../02-Product/Release-Philosophy.md](../02-Product/Release-Philosophy.md) — when and how we release

---

## How They Connect

```
Roadmap (Phase 1: Merchant Foundation)
    └── Sprint Backlog (MVP-001, MVP-002, MVP-003...)
            └── Active Sprint (MVP-001: Merchant Experience Polish)
                    └── Commit (17e0d40)
                            └── Release (v0.5.0: Brand Fix + LoyaltyProgram Bug)
```

The roadmap sets the destination. The sprint backlog defines the steps. Each sprint is one step. A release groups steps into a production deployment.

---

## Roadmap Documents in This Folder

| File | Purpose |
|---|---|
| [Long-term-Roadmap.md](./Long-term-Roadmap.md) | 4-phase platform evolution (Merchant Foundation → Customer Wallet → Regional Commerce → Regional OS) |
