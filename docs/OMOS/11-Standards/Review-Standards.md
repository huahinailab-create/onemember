# Review Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [03-Reviewer-Instructions.md](./03-Reviewer-Instructions.md), [05-Quality-Gates.md](./05-Quality-Gates.md), [Testing-Standards.md](./Testing-Standards.md) |

---

## Purpose

Standards for how sprint reviews are conducted, what is checked, and what approval means.

---

## Standards

### Who Reviews
- **Primary reviewer:** ChatGPT CTO
- **Final approver:** Product Owner

### When a Review Happens
After Claude Developer completes a sprint and submits the completion report, the Product Owner sends the report to the ChatGPT CTO for review.

### What Is Reviewed
Use the full checklist in `ai/03-Reviewer-Instructions.md`. Summary:
1. Objective alignment — did the sprint achieve what it said it would?
2. Code quality — no debug output, no hardcoded secrets, clean patterns
3. Security — correct middleware, CSRF, validated input
4. Database — reversible migration, correct fillable, no mass-assignment risk
5. UI / Branding — OneMember colors only, Bootstrap 5, responsive
6. Localization — no hardcoded strings
7. Testing — test count increased, failure paths tested
8. Documentation — decisions recorded
9. Commit hygiene — message matches sprint ID, no junk files

### Review Output
Save to `reviews/REVIEW-[SPRINT-ID].md` using the template in `ai/03-Reviewer-Instructions.md`.

### Rating Definitions
| Rating | Meaning |
|---|---|
| ✅ Approved | Deploy when Product Owner confirms |
| ⚠️ Approved with notes | Notes for next sprint, no rework |
| 🔄 Revision required | Specific fixes before approval |
| ❌ Rejected | Fundamental problem — stop and reassess |

### What Approval Means
An ✅ Approved review means the sprint is ready for production deployment, pending Product Owner confirmation. It does not mean the sprint is deployed — deployment requires a separate explicit step.
