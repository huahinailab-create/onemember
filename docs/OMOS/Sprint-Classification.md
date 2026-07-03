# Sprint-Classification.md — Risk-Based Approval Model

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-03 |
| **Introduced In** | OMOS 1.2 |
| **Related Documents** | [EXECUTE.md](./EXECUTE.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [AI-Workflow.md](./AI-Workflow.md) |

---

## Purpose

Every sprint is classified before execution. The classification determines whether Claude Developer may proceed autonomously after completion, or must stop and wait for review.

This model replaces the mandatory "Awaiting CTO Review" gate that existed in OMOS 1.0–1.1.

---

## Classification Rules

### TYPE A — Autonomous

Claude Developer completes the sprint and proceeds automatically to the next approved sprint. No CTO or PO approval required.

**A sprint is Type A when ALL of the following are true:**

| Condition | Check |
|---|---|
| All acceptance criteria pass | ✓ |
| All tests pass (`php artisan test` — zero failures) | ✓ |
| No breaking changes to existing behaviour | ✓ |
| No architecture changes | ✓ |
| No security impact | ✓ |
| No payment or billing changes | ✓ |
| No production deployment | ✓ |

**Typical Type A sprint examples:**

- UI / UX improvements
- Branding and visual polish
- Localisation (lang files, translations)
- Documentation and OMOS governance updates
- Tests and test coverage
- Refactoring (no behaviour change)
- Bug fixes (no schema or architecture change)
- Performance improvements (no schema change)
- Landing pages and marketing assets

**On completion of a Type A sprint, Claude Developer:**

1. Marks the sprint `✅ Complete` (auto-approved)
2. Archives the sprint in `CurrentSprint.md` Sprint History
3. Updates `Product-State.md`
4. Updates `CurrentSprint.md` — activates the next approved sprint
5. Continues OMOS automatically — reads and executes the next sprint spec

---

### TYPE B — CTO Decision Required

Claude Developer stops after the sprint and generates a CTO Decision Request. The next sprint does not begin until the CTO reviews and approves.

**A sprint is Type B when it includes ANY of the following:**

| Trigger | Examples |
|---|---|
| New product capability | New module, new user-facing feature |
| Database schema changes | New migration, column changes, index changes |
| Authentication or authorisation changes | Middleware, guards, policies, roles |
| API changes | New endpoints, changed contracts, breaking changes |
| Architecture changes | New patterns, new service layers, ADR-level decisions |
| Multi-tenancy changes | Scoping rules, merchant isolation |
| Payment or billing changes | Stripe, subscription plans, webhooks |
| Security changes | CSP, CORS, input handling, token management |
| Major UX redesign | Structural changes to navigation or information architecture |
| Roadmap changes | Phase transitions, backlog reprioritisation |

**On completion of a Type B sprint, Claude Developer:**

1. Marks the sprint `⏳ Awaiting CTO Review`
2. Generates a CTO Decision Request (see template below)
3. ⛔ STOPS — waits for explicit CTO approval

---

### TYPE C — CEO Approval Required

Claude Developer stops and generates a CEO Decision Request. Neither the CTO nor Claude Developer may unblock this — only the Product Owner.

**A sprint is Type C when it affects ANY of the following:**

| Trigger | Examples |
|---|---|
| Pricing | Changing plan prices, trial length, billing model |
| Legal or compliance | Terms of service, data retention, PDPA |
| Merchant contracts | SLA changes, plan feature inclusions |
| Public launch | Going live, removing beta restrictions |
| Production deployment | Any deployment to the production environment |
| Budget | Third-party subscriptions, infrastructure costs |
| Data privacy policy | Member data handling, export, deletion |

**On encountering a Type C trigger, Claude Developer:**

1. Stops immediately — before implementing if possible
2. Generates a CEO Decision Request
3. ⛔ STOPS — waits for explicit PO approval

---

## Classification Procedure

At the start of every sprint, before writing any code:

1. Read the sprint spec completely
2. Check every task against the Type B trigger list
3. Check every task against the Type C trigger list
4. If any Type C trigger is present → classify Type C
5. Else if any Type B trigger is present → classify Type B
6. Else → classify Type A

Report the classification in the session start checklist:

```
[ ] Sprint classification: TYPE A — Autonomous
    Reason: UI/UX, branding, localization — no schema, auth, or architecture changes
```

or:

```
[ ] Sprint classification: TYPE B — CTO Decision Required
    Reason: includes database migration (new `member_tiers` table)
```

---

## CTO Decision Request Template

Generate this when a Type B sprint is complete:

```
## CTO Decision Request — Sprint [ID]

**Sprint:** [ID] — [Title]
**Commit:** [hash]
**Classification:** Type B

### What was built
[2–3 sentence summary]

### Why CTO review is required
[Specific Type B trigger(s) present in this sprint]

### Questions for CTO
[Any architectural decisions made that the CTO should validate]

### Tests
[Count] tests, all passing.

### Recommended next sprint
[Next sprint ID and title from backlog]
```

---

## CEO Decision Request Template

Generate this when a Type C trigger is encountered:

```
## CEO Decision Request

**Context:** [Sprint ID or situation]
**Classification:** Type C

### Decision required
[Exactly what the PO needs to decide]

### Options
1. [Option A] — [implication]
2. [Option B] — [implication]

### Recommendation
[Claude Developer's recommendation, if any]

### Blocked sprint
[Which sprint cannot proceed until this is resolved]
```

---

## Override Rule

If the Product Owner explicitly classifies a sprint as a different type in their instructions, that classification takes precedence. Example: "Execute this as Type A" overrides the default classification even if a Type B trigger is technically present.

This override must be explicit — Claude Developer does not infer overrides.
