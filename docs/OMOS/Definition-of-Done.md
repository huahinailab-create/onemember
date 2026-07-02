# Definition-of-Done.md — Sprint Completion Requirements

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Definition-of-Ready.md](./Definition-of-Ready.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md), [EXECUTE.md](./EXECUTE.md), [CTO-Decisions.md](./CTO-Decisions.md), [CEO-Decisions.md](./CEO-Decisions.md) |

---

## What Is the Definition of Done?

The Definition of Done is the complete, non-negotiable checklist that a sprint must satisfy before Claude Developer returns the completion report.

A sprint is **Done** only when every item in this checklist is true. Claude Developer must verify all items before committing.

Sprint specifications may add sprint-specific criteria to this checklist. Sprint-specific criteria appear in the sprint's own Definition of Done section. The items here are the permanent baseline that applies to every sprint.

---

## Universal Definition of Done

These items apply to every sprint without exception.

### Implementation
- [ ] All tasks listed in the Scope section are complete
- [ ] No tasks outside the Scope section were implemented
- [ ] All Acceptance Criteria in the sprint specification are met
- [ ] No hardcoded secrets, API keys, or credentials in any file
- [ ] No `dd()`, `dump()`, `var_dump()`, or other debug output in committed code

### Testing
- [ ] `php artisan test` passes with zero failures
- [ ] All tests from previous sprints still pass (no regressions)
- [ ] New tests written for all new features (where specified in sprint)
- [ ] Tests were not deleted or skipped to make the suite pass
- [ ] `--filter` was not used to run a subset of tests

### Standards
- [ ] Bootstrap 5 only — no Tailwind CSS classes introduced (ADR-005)
- [ ] All email sent via Events/Listeners — never from controllers directly (CTO-003)
- [ ] All nullable JSON model columns use the null-safe Attribute accessor pattern (CTO-008)
- [ ] All new `@json()` Blade calls pass a single pre-assigned variable (CTO-009)
- [ ] All new queries are scoped to `merchant_id` where applicable (CTO-005)
- [ ] All new UI strings use `__()` helpers — no hardcoded English in Blade views
- [ ] All new routes are named and added to the correct route group (`['auth', 'verified']` for merchant routes)

### Security
- [ ] No security headers weakened or removed
- [ ] Email verification remains enabled
- [ ] DevTools remain double-gated (env + flag)
- [ ] No new developer endpoints accessible in production
- [ ] No new cross-tenant data access paths introduced

### Git
- [ ] All changes committed in a single commit (or minimal commits for large sprints)
- [ ] Commit message matches the format: `Sprint [ID] — [Title]\n\nCo-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>`
- [ ] No `--no-verify` used on any commit
- [ ] No unrelated changes committed alongside sprint changes

### OMOS and Process
- [ ] `CurrentSprint.md` updated — sprint status set to `⏳ Awaiting CTO Review`
- [ ] `CurrentSprint.md` commit hash field updated with the actual commit hash
- [ ] Completion report returned — includes all required sections (Summary, Files Created, Files Updated, Tests, Commit Hash, Recommendations)
- [ ] Claude Developer has stopped — no further actions taken after the completion report

---

## Sprint-Type Specific Requirements

Different sprint types have additional items that must be satisfied.

### Feature Sprints (AI-NN, new functionality)
- [ ] New Feature tests written covering the happy path
- [ ] New tests covering at least one failure/rejection case
- [ ] If new Blade views: responsive layout verified (mobile and desktop)
- [ ] If new Blade views: all strings use `__()` helpers

### Bug Fix Sprints (BUG-NNN)
- [ ] A regression test has been written that fails on the original bug and passes after the fix
- [ ] The root cause (not just the symptom) has been fixed
- [ ] The fix does not change behaviour beyond the scope of the bug

### Architecture Sprints
- [ ] An ADR has been created or updated to document the decision
- [ ] Migration `down()` methods exist and were manually verified to be safe
- [ ] No existing routes or API contracts were broken without an ADR documenting the change

### Documentation / Audit Sprints (AI-OMOS-*, AI-03-type)
- [ ] No code changes (documentation-only sprints)
- [ ] All documents have the standard metadata header (Owner, Version, Status, Last Updated, Related Documents)
- [ ] `php artisan test` still passes (even though no code was changed — verify no side effects)

### Hotfix Sprints (HOTFIX-N)
- [ ] Minimal change — only the exact fix, nothing else
- [ ] Regression test added
- [ ] `php artisan test` passes with zero failures
- [ ] Product Owner approval received before commit

---

## Not-Done Conditions

A sprint is explicitly **NOT Done** if any of the following are true, regardless of how much work was completed:

| Condition | Reason |
|---|---|
| Any test is failing | Quality gate (CTO-006) |
| Any hardcoded secret or credential in code | Security (CEO-006) |
| Email verification was disabled or weakened | Security (CEO-006) |
| DevTools accessible in production | Security (CEO-006) |
| A task outside Scope was implemented | Governance — unreviewed code |
| `CurrentSprint.md` not updated | Process — CTO cannot review without status |
| Completion report not returned | Process — CTO cannot review without report |
| Commit message is wrong format | Process — prevents audit trail |
| Any ADR was violated without flagging it | Governance — architectural integrity |

If any Not-Done condition is true, Claude Developer must fix it before marking the sprint complete.

---

## The Completion Report Template

Claude Developer must return a completion report in this format:

```
## Sprint [ID] — Completion Report

**Sprint:** [ID] — [Title]
**Commit:** [hash]
**Tests:** [N] passed / 0 failed

---

### Summary
[2–4 sentences. What was built, why it matters, what changed.]

---

### Files Created
| File | Description |
|---|---|
| [path] | [What it is and why it was created] |

---

### Files Updated
| File | Change |
|---|---|
| [path] | [What changed and why] |

---

### Tests
- Before: [N] tests
- After: [N] tests
- New tests: [List test names or describe coverage added]
- All passing: Yes

---

### Recommendations
[Any Type 3/4 decisions encountered that should become ADRs or RFCs.
Any out-of-scope issues worth a future sprint.
Any risks identified during implementation.]

---

⛔ Sprint [ID] complete. Awaiting CTO review.
```
