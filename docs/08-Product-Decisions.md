# 08 — Product Decisions

This file is the authoritative record of all approved architectural and business decisions for the OneMember project.

Every entry must be recorded here **before** implementation begins.

No decision may be assumed, invented, or implemented without a corresponding entry in this log.

---

## Decision Log Format

```
## [DECISION-XXX] Short Title
- **Date:** YYYY-MM-DD
- **Requested by:** Product Owner / CTO
- **Status:** Approved
- **Decision:** What was decided.
- **Reason:** Why this decision was made.
- **Impact:** Files, tables, or systems affected.
```

---

## Approved Decisions

### [DECISION-001] Laravel 13 as Application Framework
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** Use Laravel 13 with PHP 8.3+ as the backend framework.
- **Reason:** Modern LTS-track release with strong ecosystem, Eloquent ORM, and built-in queue/event/mail support appropriate for a SaaS platform.
- **Impact:** Entire application stack.

---

### [DECISION-002] Bootstrap 5 as Frontend Framework
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** Use Bootstrap 5.3 and Bootstrap Icons as the sole frontend UI framework. Tailwind CSS was removed.
- **Reason:** Team familiarity, desktop-first responsive grid, no build step required for prototyping, avoids JavaScript framework complexity at MVP stage.
- **Impact:** `resources/css/app.css`, `resources/js/app.js`, all Blade templates.

---

### [DECISION-003] SQLite for Local Development / MySQL for Production
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** SQLite is used for local development. MySQL 8+ is the target for staging and production.
- **Reason:** Zero-config local setup. MySQL provides production-grade performance and full-text indexing when needed.
- **Impact:** `.env`, `config/database.php`, migration compatibility (no MySQL-specific syntax in migrations).

---

### [DECISION-004] Core Domain: Merchants, Members, Loyalty Programs, Rewards, Transactions, Redemptions, Birthday Rewards, Audit Logs
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** The eight tables above constitute the MVP data model. No additional tables are to be added without a new decision entry.
- **Reason:** Covers the complete loyalty SaaS lifecycle: merchant onboarding → member enrolment → point earning → reward redemption → audit trail.
- **Impact:** All migrations in `database/migrations/2026_06_27_*`.

---

### [DECISION-005] Desktop-First Responsive Design
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** The admin/merchant web application is designed desktop-first. Mobile breakpoints are supported via Bootstrap's grid but are not the primary target.
- **Reason:** The merchant-facing dashboard is primarily used on desktop browsers. Mobile-first is reserved for a future member-facing app.
- **Impact:** All Blade layouts and component markup.

---

### [DECISION-006] Sidebar + Topbar Application Layout
- **Date:** 2026-06-27
- **Requested by:** CTO
- **Status:** Approved
- **Decision:** The authenticated layout uses a fixed left sidebar (260px, dark) and a sticky topbar (60px). Guest pages use a centred card layout.
- **Reason:** Standard SaaS dashboard pattern. Provides consistent navigation without page reloads.
- **Impact:** `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`.

---

### [DECISION-007] Laravel Breeze (Blade + Alpine.js) as Authentication System
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** Install `laravel/breeze` using the Blade + Alpine.js stack as the permanent authentication system. No React, Vue, Livewire, or Inertia.
- **Reason:** Standard Laravel auth scaffolding. Blade keeps the stack consistent with the rest of the application. Alpine.js provides lightweight interactivity without a full JS framework.
- **Impact:** Adds auth routes (login, register, password reset, email verification), auth views in `resources/views/auth/`, and a `dashboard` route. Publishes to `routes/auth.php`.

---

### [DECISION-008] Merchant Profile Fields (Sprint 2)
- **Date:** 2026-06-27
- **Requested by:** Product Owner
- **Status:** Approved
- **Decision:** The merchant profile page captures: Business Name, Contact Person, Business Email, Mobile Number, Business Address, Business Logo (placeholder only), Currency (default THB), Time Zone (default Asia/Bangkok).
- **Reason:** Core merchant identity data required before any loyalty program setup.
- **Impact:** New migration to add `contact_person` and `timezone` columns to `merchants` table (other fields already exist). New controller, form request, and view.

---

*New decisions must be appended above this line in the format shown.*
