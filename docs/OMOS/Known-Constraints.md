# Known Constraints

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Assumptions.md](./Assumptions.md), [CTO-Decisions.md](./CTO-Decisions.md), [CEO-Decisions.md](./CEO-Decisions.md), [12-ADR/README.md](./12-ADR/README.md) |

---

## Purpose

Known Constraints documents the fixed boundaries within which OneMember is built. These are not assumptions (things that might change) — they are hard constraints that define the current operating environment. Changes to constraints require explicit CEO or CTO decisions and updates to this document.

---

## Technical Constraints

### Laravel 13 / PHP 8.3+

**Constraint:** The application runs on Laravel 13.x with PHP 8.3 minimum (production: PHP 8.5).

**What this means:**
- Laravel 13 conventions are followed for all new code
- PHP 8.3+ features (readonly properties, enums, match expressions, named arguments, first-class callables) are available and encouraged
- No PHP 7.x or 8.0/8.1 syntax patterns
- Composer packages must support PHP 8.3+

**Change condition:** Framework version changes require a dedicated upgrade sprint and CTO decision.

---

### Bootstrap 5 Only

**Constraint:** Bootstrap 5 is the only permitted CSS framework. Tailwind CSS is explicitly banned.

**What this means:**
- All UI uses Bootstrap 5 classes, components, and utilities
- No Tailwind classes in any Blade file
- Alpine.js is permitted for lightweight interactivity; it does not replace server-side routing
- No mixing of Bootstrap 4 patterns

**Change condition:** This can only change with a new CTO-Decisions.md entry and an ADR. No individual sprint may introduce Tailwind or another framework.

---

### Single-Database Multi-Tenancy

**Constraint:** All tenants (merchants) share one database. Data isolation is enforced by `merchant_id` scoping in application code.

**What this means:**
- Every merchant-scoped table has `merchant_id`
- Every query on merchant data is scoped to the authenticated merchant's ID
- No per-tenant database or schema

**Change condition:** Moving to per-tenant databases would require a significant architectural migration sprint. Not planned before Phase 3.

---

### Web-First (No Native App)

**Constraint:** OneMember is a web application. No native iOS or Android app exists or is planned before Phase 2/4.

**What this means:**
- All UI must be responsive and work well on mobile browsers
- QR join flow is browser-based — no app download required
- Progressive Web App (PWA) features may be considered but are not required
- The Customer Wallet (Phase 2) may launch as a PWA before a native app

**Change condition:** Native app development requires a separate project decision. Not before Phase 4 unless the Customer Wallet requires it in Phase 2.

---

### Forge + DigitalOcean Deployment

**Constraint:** Production runs on Laravel Forge + DigitalOcean. HTTPS is managed by Forge via Let's Encrypt.

**What this means:**
- Deployment commands are run via Forge deploy scripts
- Infrastructure scaling is vertical (larger Droplet) before horizontal
- No Kubernetes, no Docker in production currently
- Environment variables are managed in Forge, not in code

**Change condition:** Infrastructure migration requires a dedicated DevOps decision. Not planned.

---

### GitHub Workflow

**Constraint:** Source code is managed in GitHub. The main branch is the production branch. There are no separate `develop` or `staging` branches currently.

**What this means:**
- All commits go to main
- Pull requests are not required (single-developer team)
- CI/CD is managed via Forge's GitHub integration
- Git history is the audit trail for all code changes

**Change condition:** Branch strategy may need to evolve when the team grows beyond one developer.

---

### Claude Pro / Claude Sonnet 4.6 Optimisation

**Constraint:** Claude Developer is Claude Sonnet 4.6 running in a Claude Pro session with context limitations. Complex sprints may hit context limits and require session continuity via summarisation.

**What this means:**
- Sprint specifications must be explicit and complete — Claude cannot rely on conversational context from previous sessions
- EXECUTE.md, CurrentSprint.md, and SprintSpecification.md are the session initialisation documents
- Every session should begin with reading these three files before doing anything
- Documentation written in OMOS must be self-contained — assume no prior context

**Change condition:** This constraint is inherent to the AI development model. OMOS is designed specifically to work around it.

---

## Business Constraints

### Thailand Primary Market

**Constraint:** Phase 1 is Thailand only. All features, pricing, and localisations are designed for Thailand first.

**What this means:**
- Currency: Thai Baht (THB) as default
- Language: Thai (th) with English (en) as fallback
- Payment: PromptPay as primary local payment (Phase 3)
- Accounting: Thai VAT and fiscal year conventions (Phase 4)
- Timezone: Asia/Bangkok as default merchant timezone

**Change condition:** Regional expansion is Phase 4. No regional features are implemented before the regional expansion sprint is approved.

---

### English-First Documentation, Thai Localisation Second

**Constraint:** OMOS documentation and code comments are in English. UI copy is in English first, with Thai translation required but allowed to lag by one sprint.

**What this means:**
- All Blade views use `__()` helpers so translation is possible
- English keys must exist before a sprint is marked complete
- Thai translations may be completed in the same sprint or the next sprint
- OMOS documentation is English only (the development team communicates in English)

**Change condition:** If a Thai-speaking product manager joins the team, OMOS documentation may be bilingual. Not required until then.

---

### Subscription-Gated Features

**Constraint:** OneMember's revenue depends on merchant subscription payments via Stripe. All merchant-facing features must be behind a valid subscription check.

**What this means:**
- Merchants in trial or with expired subscriptions have limited or no access
- Subscription status is checked at the middleware level, not individually in each controller
- No feature bypasses the subscription gate without explicit Product Owner approval

**Change condition:** The subscription model itself is a CEO-002 decision and cannot change without a new CEO decision.

---

## Security Constraints

These constraints are absolute. They cannot be changed by any sprint. Only a CEO decision can modify them.

| Constraint | Rule |
|---|---|
| Email verification | Always enabled. Never disabled or weakened. |
| Developer tools in production | Never. `APP_ENV !== 'production'` is a hard gate. |
| Hardcoded secrets | Never. All secrets in `.env` / Forge only. |
| `APP_DEBUG` in production | Always `false`. |
| `--no-verify` on commits | Never used. If a hook fails, fix the issue. |
