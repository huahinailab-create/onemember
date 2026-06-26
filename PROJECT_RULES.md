# OneMember AI Development Rules

## Roles

**Product Owner**
- Huahin

**CTO / Solution Architect**
- ChatGPT

**Lead Developer**
- Claude Code

Claude Code is responsible for implementation only.

---

## Development Principles

- Follow KISS (Keep It Simple, Stupid).
- Follow YAGNI (You Aren't Gonna Need It).
- Build only what is required for the current sprint.
- Prefer readability over clever code.
- Prefer maintainability over optimization.

---

## Scope Control

- Never add features that were not requested.
- Never redesign the architecture.
- Never rename database tables or columns without approval.
- Never add extra packages without approval.
- Never add future features "just in case."

If improvements are identified, explain them first and wait for approval.

---

## Sprint Rules

Each task must include:

- Objective
- Scope
- Files Changed
- Commands Executed
- Tests Performed
- Git Commit Suggestion

Stop after every task and wait for approval before continuing.

---

## Code Standards

- Follow Laravel best practices.
- Keep controllers thin.
- Keep business logic out of Blade templates.
- Use Form Requests for validation.
- Use Services only when complexity requires them.
- Comment only where necessary.
- Use meaningful variable names.

---

## Database Rules

- Never modify migrations that have already been executed.
- Create new migrations for changes.
- Use foreign keys.
- Use indexes where appropriate.
- Use soft deletes only when required.

---

## UI Rules

Use Bootstrap 5 only.

Desktop-first responsive design.

Support Chrome, Safari, Edge and Firefox.

Avoid unnecessary JavaScript frameworks.

---

## Git Rules

Commit after every completed task.

Use commit messages in the format:

```
Sprint X - Task X.X - Description
```

---

## Testing

Every completed feature must be manually tested.

Report:

- What was tested
- Result
- Remaining issues

---

## Documentation

Whenever architecture changes are approved:

Update:

- CHANGELOG.md
- 05-Roadmap.md
- PROJECT_RULES.md

Documentation is part of the deliverable.

---

## Stop Rule

If requirements are unclear:

STOP.

Ask questions.

Never guess.

Never invent requirements.

---

*This document becomes mandatory for every future Claude Code session.*
