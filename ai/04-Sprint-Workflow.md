# Sprint Workflow

## Overview

Every piece of work in OneMember follows this workflow. No step may be skipped.

---

## The 10-Step Workflow

```
Step 1 ── Product Owner defines business objective
Step 2 ── ChatGPT CTO writes sprint spec
Step 3 ── Sprint spec saved to sprints/SPRINT-XXX-Name.md
Step 4 ── Claude reads sprint spec and relevant ai/ docs
Step 5 ── Claude implements
Step 6 ── Claude runs php artisan test — all must pass
Step 7 ── Claude commits with sprint ID in message
Step 8 ── Product Owner sends summary to ChatGPT CTO
Step 9 ── ChatGPT CTO reviews → reviews/REVIEW-XXX.md
Step 10 ─ Product Owner approves → deploy to production
```

---

## Step-by-Step Detail

### Step 1 — Product Owner Defines Objective

The Product Owner writes a plain-English description of what they want to achieve. This should describe the business need, not the technical solution.

**Good:** "I want merchants to be able to add a birthday bonus multiplier to their loyalty programme without editing code."

**Bad:** "Add a `birthday_multiplier` column to the `loyalty_programs` table and a form field in the campaign settings page."

### Step 2 — ChatGPT CTO Writes Sprint Spec

CTO translates the objective into a full sprint spec using the template in `ai/01-CTO-Instructions.md`. The spec must be unambiguous enough that Claude can implement it without asking follow-up questions.

**Before writing the spec, CTO must:**
- Check `docs/08-Product-Decisions.md` for prior decisions
- Check `ai/07-Architecture-Rules.md` for constraints
- Confirm the objective aligns with `roadmap/`

### Step 3 — Sprint Spec Saved

The spec is saved as:
```
sprints/SPRINT-[ID]-[kebab-case-name].md
```

Example: `sprints/SPRINT-007-Birthday-Bonus-Multiplier.md`

Sprint IDs are sequential. Never reuse an ID.

### Step 4 — Claude Reads Docs

Claude reads in this order before writing a single line of code:
1. The sprint spec
2. `ai/05-Quality-Gates.md`
3. `ai/06-Branding-Rules.md`
4. `ai/07-Architecture-Rules.md`
5. Any `docs/` files referenced in the spec

If anything is unclear, Claude asks now — not halfway through implementation.

### Step 5 — Claude Implements

Claude implements strictly within scope. See `ai/02-Claude-Developer-Instructions.md` for all implementation rules.

### Step 6 — Tests Pass

```bash
php artisan test
```

All tests must pass. If any test fails, Claude diagnoses and fixes before committing. Claude never commits with failing tests.

### Step 7 — Claude Commits

```
git add [specific files only]
git commit -m "Sprint [ID] — [Name]

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

### Step 8 — Product Owner Sends Summary to CTO

The Product Owner copies Claude's end-of-sprint report and sends it to ChatGPT CTO for review.

### Step 9 — CTO Reviews

CTO uses the checklist in `ai/03-Reviewer-Instructions.md` and saves the result to `reviews/REVIEW-[ID].md`.

### Step 10 — Deploy

If the review is ✅ Approved or ⚠️ Approved with notes, the Product Owner gives the green light to deploy. Claude or the Product Owner runs deployment commands per `ai/08-Deployment-Rules.md`.

---

## Sprint Naming Conventions

| Prefix | Purpose | Example |
|---|---|---|
| `SPRINT-` | Regular feature sprint | `SPRINT-012-Member-Import` |
| `BUG-` | Bug fix sprint | `BUG-003-Dashboard-500-Error` |
| `AI-` | AI system / workflow sprint | `AI-01-Development-System` |
| `SEC-` | Security sprint | `SEC-001-Rate-Limiting` |
| `INFRA-` | Infrastructure sprint | `INFRA-002-Queue-Configuration` |
| `DEV-` | Developer tools sprint | `DEV-003-Log-Viewer` |

---

## Parallel Work Policy

Only one sprint may be in active implementation at a time. A new sprint spec may be written while a previous sprint is in review, but implementation must not start until the previous sprint is approved.

---

## Scope Change Policy

If the Product Owner wants to add something during a sprint:
1. The current sprint must be paused at a clean commit.
2. The CTO writes an updated or new spec.
3. Claude restarts from Step 4.

No informal "just also add this" requests. Every change goes through the spec.
