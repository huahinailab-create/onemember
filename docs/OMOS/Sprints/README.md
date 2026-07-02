# Sprints — Sprint Library

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [../CurrentSprint.md](../CurrentSprint.md), [../Sprint-Lifecycle.md](../Sprint-Lifecycle.md), [../NextSprintTemplate.md](../NextSprintTemplate.md), [Backlog.md](./Backlog.md) |

---

## Purpose

This folder is the sprint library. Every sprint that has been planned, executed, or is currently active has a file here.

Each sprint file is the authoritative specification for that sprint. `CurrentSprint.md` references the active sprint — this folder holds the detail.

---

## How This Folder Works

| State | Where it lives |
|---|---|
| Sprint being planned | This folder — status `🔲 Planning` |
| Sprint in progress | This folder — status `🔄 In Progress` — referenced from `CurrentSprint.md` |
| Sprint awaiting review | This folder — status `⏳ Awaiting CTO Review` |
| Sprint complete | This folder — status `✅ Complete` |
| Sprint cancelled | This folder — status `⛔ Cancelled` |

`CurrentSprint.md` always contains a reference to the active sprint file. Read the sprint file for the full specification.

---

## Sprint File Naming Convention

| Sprint Type | Convention | Example |
|---|---|---|
| MVP feature sprint | `MVP-NNN-Title.md` | `MVP-001-Merchant-Experience-Polish.md` |
| Bug fix | `BUG-NNN-Title.md` | `BUG-001-Email-Verification-Fix.md` |
| OMOS governance | `OMOS-N-Title.md` | `OMOS-1.1-Operational-Readiness.md` |
| Hotfix | `HOTFIX-N-Title.md` | `HOTFIX-1-Stripe-Webhook-Fix.md` |
| Architecture | `ARCH-N-Title.md` | `ARCH-1-ADR-Naming-Convention.md` |
| Audit | `AUDIT-N-Title.md` | `AUDIT-1-Application-Audit.md` |

---

## Active Sprints

| Sprint ID | File | Status |
|---|---|---|
| MVP-001 | [MVP-001-Merchant-Experience-Polish.md](./MVP-001-Merchant-Experience-Polish.md) | 🔲 Planning |

---

## Completed Sprint Archive

| Sprint ID | Title | Commit | Completed |
|---|---|---|---|
| AI-OMOS-BOOTSTRAP | OMOS Operational | `17e0d40` | 2026-07-02 |
| OMOS-1.1 | Operational Readiness | TBD | 2026-07-02 |
| AI-03 | Application Audit | `f8d6ac8` | 2026-07-02 |
| AI-02C | OMOS Self-Driving Foundation | `965075d` | 2026-07-02 |
| AI-02B1+B2 | Executive and Product Foundation | `67f669f` | 2026-07-02 |
| AI-02A | OneMember Operating System Foundation | `eeb9744` | 2026-07-02 |
| AI-01 | AI Development System | `09948b9` | 2026-07-02 |
| DEV-02 | Developer Productivity Suite | — | — |
| DEV-01 | Developer Tools | `962a82f` | — |
| BUG-002 | Dashboard Broken Links | `056495f` | — |
| BUG-001 | Email Verification Flow Fix | `a26e761` | — |
| Sprint 6.7 | Merchant Intelligence | `73e1af2` | — |

---

## Backlog

See [Backlog.md](./Backlog.md) for all planned but not-yet-scheduled sprints.
