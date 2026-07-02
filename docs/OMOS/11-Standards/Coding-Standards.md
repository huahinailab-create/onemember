# Coding Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [07-Architecture-Rules.md](./07-Architecture-Rules.md), [02-Claude-Developer-Instructions.md](./02-Claude-Developer-Instructions.md), [Testing-Standards.md](./Testing-Standards.md) |

---

## Purpose

The authoritative PHP and Laravel coding standards for the OneMember codebase.

---

## Standards

### PHP Style
- PSR-12 code style enforced
- PHP 8.3+ features used where they improve clarity (readonly properties, enums, match expressions, named arguments)
- No mixed types without justification
- Return types declared on all methods

### Laravel Conventions
- Controllers: thin, delegate to Services
- Services: stateless, injected via constructor, no HTTP layer interaction
- Form Requests: all input validation lives here, never inline in controllers
- Events: used for cross-cutting concerns (email, audit, analytics)
- Jobs: all queue work — never synchronous heavy processing in controllers

### Naming
- Classes: PascalCase
- Methods and variables: camelCase
- Database columns and tables: snake_case
- Route names: dot.notation (e.g. `campaigns.rewards.create`)
- Blade view files: kebab-case.blade.php

### Forbidden Patterns
- `dd()`, `dump()`, `var_dump()` — no debug output in committed code
- Raw SQL without binding — use Eloquent or Query Builder with parameter binding
- `DB::statement()` with user input — always bind parameters
- `$guarded = []` — use explicit `$fillable`
- Logic in Blade views — keep views as dumb as possible
- Direct email sending in controllers — use event + listener

### Comments
- No comments explaining what the code does (clear naming does that)
- Comments only for non-obvious WHY: hidden constraints, workarounds, subtle invariants
- No multi-paragraph docblocks — one short line maximum
