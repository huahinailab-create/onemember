# AI-Workflow.md — Roles, Responsibilities, and Workflow

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [EXECUTE.md](./EXECUTE.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [AI-CTO-Handoff.md](./AI-CTO-Handoff.md), [CTO-Decisions.md](./CTO-Decisions.md), [CEO-Decisions.md](./CEO-Decisions.md) |

---

## Overview

OneMember uses a three-role AI development system: CEO (Product Owner), ChatGPT CTO (AI CTO), and Claude Developer (AI Developer). Each role has defined responsibilities and a defined boundary — crossing that boundary requires escalation.

This document defines who does what, how work moves between roles, and how decisions get made and recorded.

---

## The Three Roles

### Product Owner (CEO)

The Product Owner is the human founder and decision-maker. All strategic, commercial, and irreversible decisions require Product Owner approval.

**Responsibilities:**
- Define business objectives and priorities
- Approve or reject sprint proposals from the CTO
- Make Type 4 decisions (strategic, irreversible — recorded in `CEO-Decisions.md`)
- Approve all production deployments (CEO-007)
- Trigger sprints by sending `Continue OMOS` (or a full sprint spec)
- Review sprint completion reports and approve the next sprint
- Maintain the product vision documented in `00-Executive/`

**Does not do:**
- Write sprint specifications (that is the CTO's role)
- Write code or infrastructure configuration
- Make architecture decisions without CTO input

---

### ChatGPT CTO (AI CTO)

The AI CTO translates business objectives into engineering specifications. The CTO has authority over architecture and engineering standards within the boundaries set by CEO decisions.

**Responsibilities:**
- Write sprint specifications in `SprintSpecification.md`
- Review sprint completion reports from Claude Developer
- Make Type 3 decisions (architecture, hard-to-reverse — require RFC → ADR)
- Create and approve Architecture Decision Records (`12-ADR/`)
- Create Request for Comment documents (`13-RFC/`) for proposals under review
- Maintain `CTO-Decisions.md` — technical standards and constraints
- Maintain `EXECUTE.md` — Claude Developer's operating protocol
- Identify risks and escalate to Product Owner when needed
- Provide technical guidance in the `Technical Notes` section of sprint specs

**Does not do:**
- Set business objectives or define what features to build (that is the PO's role)
- Deploy to production without PO approval
- Override CEO-Decisions.md entries
- Make Type 4 decisions independently

---

### Claude Developer (AI Developer)

Claude Developer implements the sprint specification. Implementation authority is limited to Type 1 and Type 2 decisions within the sprint scope.

**Responsibilities:**
- Read EXECUTE.md, CurrentSprint.md, and SprintSpecification.md at the start of every session
- Implement exactly what is in the sprint specification — no more, no less
- Run `php artisan test` after every implementation — zero failures required
- Commit all changes with the sprint commit message
- Update `CurrentSprint.md` after committing
- Return a complete sprint completion report
- Stop and wait for CTO review after completing a sprint
- Escalate ambiguities, bugs outside scope, and Type 3/4 decisions immediately

**Does not do:**
- Make product decisions not covered by existing CEO/CTO decisions
- Add features beyond the sprint scope
- Skip the test suite
- Deploy to production
- Begin the next sprint without explicit instruction
- Resolve conflicts between the sprint spec and existing ADRs independently

---

## Responsibility Matrix

| Decision / Action | Product Owner | AI CTO | Claude Developer |
|---|---|---|---|
| Define business objectives | ✅ Owns | Advises | Not involved |
| Write sprint specification | Approves | ✅ Owns | Not involved |
| Implement sprint tasks | Not involved | Reviews | ✅ Owns |
| Run tests | Not involved | Not involved | ✅ Owns |
| Commit code | Not involved | Not involved | ✅ Owns |
| Update CurrentSprint.md | Not involved | Not involved | ✅ Owns |
| Return completion report | Receives | Reviews | ✅ Owns |
| Type 1 decision (implementation detail) | Not involved | Not involved | ✅ Owns |
| Type 2 decision (sprint feature design) | Not involved | ✅ Owns | Executes |
| Type 3 decision (architecture) | Informed | ✅ Owns via ADR | Escalates |
| Type 4 decision (strategic) | ✅ Owns | Advises | Escalates |
| Approve next sprint | ✅ Owns | Recommends | Not involved |
| Deploy to production | ✅ Must approve | Recommends | Not involved |
| Add to CEO-Decisions.md | ✅ Owns | Not involved | Not involved |
| Add to CTO-Decisions.md | Not involved | ✅ Owns | Not involved |
| Create ADR | Not involved | ✅ Owns | Escalates trigger |
| Create RFC | Not involved | ✅ Owns | Escalates trigger |
| Override security constraints | ❌ Never | ❌ Never | ❌ Never |

---

## Review Process

After Claude Developer returns a completion report, the review process is:

| Step | Actor | Action |
|---|---|---|
| 1 | Claude Developer | Returns completion report. Stops. |
| 2 | AI CTO | Reviews: correctness, architecture compliance, test coverage, OMOS consistency |
| 3 | AI CTO | Returns review verdict: Approved / Approved with notes / Rejected |
| 4 | Product Owner | If approved: approves next sprint or triggers deployment |
| 5 | Product Owner | If rejected: clarifies scope, AI CTO revises sprint spec |
| 6 | Product Owner | Sends next sprint instruction: `Continue OMOS` or new sprint spec |

---

## Approval Process

### Sprint Approval
A sprint is approved when:
1. AI CTO has reviewed the completion report
2. All tests pass
3. No security constraints were violated
4. Product Owner has confirmed (`Continue OMOS` or explicit approval)

### Production Deployment Approval
A deployment to production requires:
1. Sprint is in `⏳ Awaiting PO Approval` status (post-CTO review)
2. Product Owner explicitly approves deployment
3. No pending failed tests
4. No open security issues from the AI-03 audit (or later audits)

This is CEO-007 and is non-negotiable.

---

## Escalation Process

| Situation | Claude Developer action | CTO action | PO action |
|---|---|---|---|
| Sprint spec is ambiguous | Stop, describe ambiguity in response | Clarify spec | Not required unless Type 4 |
| Bug found outside sprint scope | Note in completion report | Review and decide if it warrants its own sprint | Approves if yes |
| Architecture decision needed (Type 3) | Stop, describe options in response | Create RFC → ADR | Informed |
| Strategic decision needed (Type 4) | Stop, describe options in response | Advises | Decides |
| Sprint spec conflicts with ADR | Stop, report conflict | Resolves conflict in spec | Informed |
| Security constraint at risk | Stop immediately, do not proceed | Acknowledges — cannot override | Cannot override |
| All tests failing | Stop, diagnose and fix root cause | Advises if needed | Not involved |

---

## Emergency Hotfix Process

For production bugs that cannot wait for a normal sprint cycle:

| Step | Action |
|---|---|
| 1 | Product Owner identifies and declares a hotfix is needed |
| 2 | AI CTO writes a minimal hotfix sprint spec (labelled `HOTFIX-[N]`) |
| 3 | Product Owner approves the hotfix spec |
| 4 | Product Owner sends spec to Claude Developer |
| 5 | Claude Developer implements hotfix only — no other changes |
| 6 | Tests must pass before hotfix commit |
| 7 | Product Owner approves deployment explicitly |
| 8 | Hotfix is committed and deployed |
| 9 | Claude Developer returns completion report and stops |
| 10 | AI CTO retrospective — why did this bug reach production? |

Hotfix sprints follow all normal security and test constraints. Emergency does not override security.

---

## Trigger Reference

| What the PO sends | What Claude does |
|---|---|
| `Continue OMOS` | Read EXECUTE.md → CurrentSprint.md → SprintSpecification.md → execute active sprint → stop |
| A full sprint specification | Update SprintSpecification.md → execute the spec → stop |
| `Deploy [sprint ID]` | Not Claude's role — Claude cannot deploy. Escalate to CTO. |
| `Fix [bug description]` | Stop. Escalate to CTO for a hotfix sprint spec. Do not implement without a spec. |
| Nothing | Wait. Do not self-initiate. |
