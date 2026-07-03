# Sprint Spec — RELEASE-1A: OneMember Product Identity

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-1A |
| **Title** | OneMember Product Identity |
| **Sprint Type** | Design + UX Transformation |
| **Classification** | Type B — CTO Review Required (major UX redesign) |
| **Status** | ✅ CTO Activated — 2026-07-03 |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Created** | 2026-07-03 |
| **Related Documents** | [Product-State.md](../Product-State.md), [CTO-Decisions.md](../CTO-Decisions.md) |

---

## Business Outcome

Transform the OneMember application into a premium SaaS product that fully reflects the official OneMember brand. A merchant who logs in for the first time should immediately feel they are using a polished, professional loyalty platform — not a generic Bootstrap starter.

No new business features. This is a design and UX transformation.

---

## Codebase Audit Findings (pre-sprint)

| Finding | Severity | Action |
|---|---|---|
| Sidebar fallback shows generic hexagon icon when no merchant logo | High | Replace with OneMember text logo |
| `application-logo.blade.php` is generic Laravel Breeze SVG | High | Replace with OneMember brand mark |
| `navigation.blade.php` uses Tailwind CSS classes — not referenced anywhere | Medium | Convert to Bootstrap / clean up |
| Dashboard stat cards use `bg-success`, `bg-warning`, `bg-info` generic Bootstrap | High | Replace with brand-consistent palette |
| Dashboard quick actions use `btn-outline-success/warning` | Medium | Replace with brand-consistent buttons |
| Onboarding welcome uses hexagon icon + `bg-success`/`bg-warning` containers | High | Replace with brand logo + navy/pink |
| Form inputs have Bootstrap blue focus ring | Medium | Override with pink brand focus |
| Cards use default Bootstrap border — low elevation feel | Medium | Add premium shadow system |
| `onboarding/welcome.blade.php` uses generic hexagon — first impression | High | Replace with OneMember logo mark |
| No icon/logo image files in `public/` — text logo is the official brand | Info | Text logo pattern is authoritative |

---

## Design System

### Brand Palette (authoritative)

| Token | Value | Use |
|---|---|---|
| `--om-navy` | `#1A2E5A` | Primary brand, headers, sidebar |
| `--om-navy-d` | `#0F1C3A` | Hover/active dark navy |
| `--om-pink` | `#FF1585` | Accent, CTAs, highlights |
| `--om-cloud` | `#F0F0F4` | App body background |
| `--om-ink` | `#1A1A2E` | Body text |
| `--om-white` | `#FFFFFF` | Cards, containers |

### Official Logo Pattern

**On light backgrounds:**
```html
<span style="color:#FF1585;font-weight:700;">one</span><span style="color:#1A2E5A;font-weight:700;">member</span>
```

**On dark/navy backgrounds:**
```html
<span style="color:#FF1585;font-weight:700;">one</span><span style="color:#FFFFFF;font-weight:700;">member</span>
```

Font: `Arial, sans-serif` (system font — no web font dependency for the logo).

---

## Tasks

### Task 1 — CSS Design System (`resources/css/app.css`)

Enhance the existing design tokens with a complete premium component system:

- **Elevation system**: Three shadow levels (`--om-shadow-sm`, `--om-shadow-md`, `--om-shadow-lg`)
- **Card redesign**: Remove generic Bootstrap border, apply `--om-shadow-sm`, round corners to 10px
- **Form focus states**: Override Bootstrap blue with pink (`--om-pink`) focus ring on all inputs, selects, textareas
- **Stat card component** (`.stat-card`): Premium metric display — no colored icon backgrounds; use a left-border accent in brand color instead
- **Badge refinements**: Override Bootstrap badge padding/radius for cleaner look
- **Button refinements**: `btn-primary` uses navy; add `.btn-accent` for pink CTA
- **Table refinements**: Tighter header, cleaner hover state using `--om-cloud`
- **Brand nav-tabs**: Active tab uses navy underline, not Bootstrap blue
- **Responsive sidebar backdrop**: Ensure mobile overlay is correct
- **Utility additions**: `.text-pink`, `.bg-pink`, `.border-pink`

### Task 2 — `application-logo.blade.php`

Replace the generic Laravel Breeze geometric SVG with the OneMember text logo HTML component. Must render inline with correct colors in both light and dark contexts. Accept an optional `$dark` attribute for dark-background variant.

### Task 3 — `navigation.blade.php`

Convert the legacy file from Tailwind CSS classes to Bootstrap 5 classes. This file is currently unreferenced by the app (the actual navigation lives in `app.blade.php`) but it should not contain Tailwind if discovered.

### Task 4 — `layouts/app.blade.php` (Sidebar)

Fix the merchant-logo fallback branch:

**Before:** hexagon Bootstrap icon + merchant display name  
**After:** OneMember text logo (dark variant: pink + white) — establishes brand identity in every app session

Also refine the "Powered by OneMember" treatment when a merchant HAS a custom logo.

### Task 5 — `dashboard.blade.php`

Brand consistency pass:

- **Stat cards**: Replace `bg-success/warning/info bg-opacity-10` icon containers with consistent navy icon containers (`var(--om-icon-bg)`) across all 4 cards; use brand-appropriate icon colors
- **Quick actions**: Replace `btn-outline-success`, `btn-outline-warning`, `btn-outline-secondary` with `btn-outline-primary` for visual consistency
- **Subscription panel**: Replace inline hardcoded strings with cleaner brand-consistent typography
- **Insights/opportunities**: Icon colors → consistent brand navy

### Task 6 — `onboarding/welcome.blade.php`

- Replace the large hexagon icon with the OneMember text logo mark
- Replace `bg-success bg-opacity-10`/`bg-warning bg-opacity-10` step containers with brand-consistent navy/pink palette
- Ensure "Get Started" CTA button is brand navy

### Task 7 — Onboarding Steps (`business-info`, `business-settings`, `loyalty-preference`, `quick-start`, `finish`)

- Review each step for generic Bootstrap colors
- Replace any `bg-success`/`bg-warning`/`bi-hexagon` with brand equivalents
- Ensure step indicators and progress UI is brand-consistent

### Task 8 — `auth/login.blade.php` + `auth/register.blade.php`

- Login: add subtle brand polish — pink underline on "Sign in" heading, refine CTA
- Register: review trial strip for brand consistency (from MVP-003)
- `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`, `auth/verify-email.blade.php`: brand consistency check

### Task 9 — Members Pages (`members/index`, `members/show`, `members/create`)

- Replace any generic Bootstrap color references with brand palette
- Member status badges: refine styling
- Action buttons: consistent with brand

### Task 10 — Campaigns Pages (`campaigns/index`, `campaigns/show`, `campaigns/create`)

- Replace generic Bootstrap colors
- Campaign type and status badges: brand-consistent
- Form sections in `campaigns/create` — check for generic styling

### Task 11 — Rewards Pages (`rewards/show`, `rewards/create`)

- Replace generic Bootstrap colors with brand palette

### Task 12 — Settings Page (`settings/index`)

- Nav tabs: apply brand styling (navy active underline)
- Section cards: apply premium card styling
- Review for generic Bootstrap colors

### Task 13 — Subscription Page + Trial Banner

- `subscription/index.blade.php`: brand-consistent plan cards
- `components/trial-banner.blade.php`: brand-consistent with navy/pink palette; remove generic Bootstrap alert colors

### Task 14 — `coming-soon.blade.php`

Replace generic `bg-primary bg-opacity-10` + hexagon icon with brand-consistent treatment.

### Task 15 — `layouts/guest.blade.php`

Enhance the auth card container:
- Navy top accent bar on the card
- Subtle brand shadow
- "Powered by OneMember" subtle footer refinement

---

## Acceptance Criteria

| # | Criterion |
|---|---|
| AC-1 | OneMember text logo appears in: sidebar (no-merchant-logo), wizard header, guest layout, application-logo component |
| AC-2 | No generic hexagon Bootstrap icon (`bi-hexagon-fill`) appears on any merchant-facing page |
| AC-3 | Dashboard stat card icon containers use consistent brand colors (no `bg-success`, `bg-warning`, `bg-info`) |
| AC-4 | All form inputs show pink focus ring (`#FF1585`) instead of Bootstrap blue |
| AC-5 | Cards use elevation shadow system instead of flat Bootstrap border |
| AC-6 | Quick action buttons use brand-consistent colors |
| AC-7 | Onboarding welcome uses brand logo + brand colors for step indicators |
| AC-8 | Auth pages are brand-polished (navy accent, consistent CTA) |
| AC-9 | Trial banner uses brand palette (not generic Bootstrap alert) |
| AC-10 | `navigation.blade.php` contains no Tailwind CSS classes |
| AC-11 | All 380+ tests pass |

---

## Commit Message

```
RELEASE-1A — OneMember Product Identity

Complete design system and UX transformation sprint.
Official OneMember text logo applied across all surfaces.
Premium CSS design system with brand-consistent components.
All generic Bootstrap colors replaced with OneMember palette.
```

---

## Classification Justification

**Type B** — Major UX redesign across the entire application. Touches every user-facing view. Requires CTO review before deployment to confirm visual quality and brand alignment.
