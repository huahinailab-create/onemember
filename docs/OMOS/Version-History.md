# Version History

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [CurrentSprint.md](./CurrentSprint.md), [SprintReview.md](./SprintReview.md), [CEO-Decisions.md](./CEO-Decisions.md) |

---

## Purpose

Version History tracks every significant release and documentation milestone for the OneMember platform and OMOS. It provides a chronological record that allows any team member to understand what existed at any point in time.

**Application versions** track the deployed software.  
**OMOS versions** track the governance documentation.

---

## Application Version History

| Version | Release Date | Sprint | Summary | Approved By |
|---|---|---|---|---|
| 0.1.0 | — | Pre-sprint | Initial Laravel application — basic loyalty programme, member management, registration | — |
| 0.2.0 | — | Sprint 6.7 | Merchant Intelligence: AI health score, trend insights, opportunity recommendations | Product Owner |
| 0.3.0 | — | DEV-01 | Developer Tools module: seed data, factory reset, feature flags, audit log | Product Owner |
| 0.4.0 | — | DEV-02 | Developer Productivity Suite: enhanced dev tools, test data management | Product Owner |
| 0.4.1 | 2026-07-02 | BUG-001 | Email verification stale-tab fix: JS polling, verification status endpoint | Product Owner |
| 0.4.2 | 2026-07-02 | BUG-002 | Dashboard broken links fix: settings 500 error, campaign show Blade ParseError | Product Owner |

---

## OMOS Documentation Version History

| Version | Date | Sprint | Summary | Approved By |
|---|---|---|---|---|
| OMOS 0.1.0 | 2026-07-02 | AI-01 | AI development system: ai/, backlog/, sprints/, reviews/, roadmap/ folder structures and 18 documentation files | Product Owner |
| OMOS 0.2.0 | 2026-07-02 | AI-02A | OMOS foundation: 17-folder docs/OMOS/ structure, 43 files including standards, ADR/RFC systems, roadmap, CurrentSprint | Product Owner |
| OMOS 0.3.0 | 2026-07-02 | AI-02B1+B2 | Executive and product foundation: Vision, Mission, Values, Principles, Personas, Journeys, Revenue Model, Glossary, Parking Lot, EXECUTE.md | AI CTO |
| OMOS 0.4.0 | 2026-07-02 | AI-02C | Self-driving foundation: Version History, CEO/CTO decision logs, Known Constraints, Assumptions, Knowledge Base, first ADRs, RFC templates | AI CTO |

---

## How to Update This Document

After every sprint completion:

1. Add a row to the appropriate table (Application or OMOS)
2. Version number follows semver: `MAJOR.MINOR.PATCH`
   - MAJOR: platform phase change (Phase 1 → Phase 2)
   - MINOR: new module, significant feature, or OMOS structural change
   - PATCH: bug fix, documentation update, minor improvement
3. "Approved By" must match the sprint review approval record in `SprintReview.md`

This document is updated by Claude Developer as part of the sprint completion report. The Product Owner confirms the version number is appropriate.
