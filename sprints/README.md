# Sprints

This folder contains all sprint specifications for the OneMember platform.

## Naming Convention

```
sprints/SPRINT-[ID]-[kebab-case-name].md
```

| Prefix | Purpose | Example |
|---|---|---|
| `SPRINT-` | Regular feature sprint | `SPRINT-012-Birthday-Bonus.md` |
| `BUG-` | Bug fix sprint | `BUG-003-Dashboard-Links.md` |
| `AI-` | AI system / workflow sprint | `AI-01-Development-System.md` |
| `SEC-` | Security sprint | `SEC-001-Rate-Limiting.md` |
| `INFRA-` | Infrastructure sprint | `INFRA-002-Queue-Config.md` |
| `DEV-` | Developer tools sprint | `DEV-003-Log-Viewer.md` |

## Sprint IDs

IDs are sequential within each prefix type. Never reuse or skip an ID.

Keep a running index here:

## Sprint Index

| Sprint | Name | Status | Commit |
|---|---|---|---|
| DEV-01 | Developer Tools Module | ✅ Complete | `d30e09f` |
| DEV-02 | Developer Productivity Suite | ✅ Complete | `962a82f` |
| BUG-001 | Email Verification Flow Fix | ✅ Complete | `a26e761` |
| BUG-002 | Dashboard Broken Links | ✅ Complete | `056495f` |
| AI-01 | OneMember AI Development System | ✅ Complete | — |

## How to Use This Folder

1. ChatGPT CTO writes the spec using the template in `ai/01-CTO-Instructions.md`.
2. Save the spec as a new file in this folder.
3. Claude reads the spec and implements.
4. After completion, update the Sprint Index above with the commit hash.
5. Move completed specs are kept here permanently for reference.

## Sprint Template

See `ai/01-CTO-Instructions.md` for the full sprint spec template. Every sprint must use that exact structure.
