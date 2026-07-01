# Reviews

This folder contains sprint review reports completed by the ChatGPT CTO after each sprint.

## Naming Convention

```
reviews/REVIEW-[SPRINT-ID].md
```

Examples:
- `reviews/REVIEW-SPRINT-012.md`
- `reviews/REVIEW-BUG-003.md`
- `reviews/REVIEW-AI-01.md`

## Review Process

1. Claude completes a sprint and reports: files modified, tests added, commit hash.
2. Product Owner forwards the report to ChatGPT CTO.
3. CTO runs the review checklist from `ai/03-Reviewer-Instructions.md`.
4. CTO saves the review here with rating and notes.
5. Product Owner reads the review and gives deploy approval if ✅.

## Rating Scale

| Rating | Meaning | Action |
|---|---|---|
| ✅ Approved | All gates passed | Deploy when Product Owner confirms |
| ⚠️ Approved with notes | Minor issues, no re-work needed | Note for next sprint |
| 🔄 Revision required | Specific items must be fixed | Claude fixes, re-review |
| ❌ Rejected | Fundamental problem | Stop, re-assess with CTO |

## Review Index

| Sprint | Reviewer | Date | Rating |
|---|---|---|---|
| AI-01 | ChatGPT CTO | — | Pending |

## Review Template

See `ai/03-Reviewer-Instructions.md` for the full review template and checklist.
