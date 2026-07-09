# OneMember Design System

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Last Updated** | 2026-07-06 |
| **Author** | Claude Fable 5 (PLATFORM-001) |
| **Related Documents** | [10-Brand-Guidelines](../../10-Brand-Guidelines.md), [ADR-005 Bootstrap 5](../12-ADR/ADR-005-Bootstrap-5-Standard.md), [Product-Bible v2.0.0](../02-Product/Product-Bible.md), [04-UI-UX](../../04-UI-UX.md) |

**Rule: every future screen reuses this system.** This document standardizes what already exists — it does not redesign the product. When a screen needs a pattern, use the canonical component/class below; only invent new UI when no pattern fits, and then add it here first.

---

## 1. Foundations

### 1.1 Color palette (CSS custom properties in `resources/css/app.css` `:root`)

| Token | Value | Use |
|---|---|---|
| `--om-navy` / `--bs-primary` | `#1A2E5A` | Primary actions, headings, sidebar, brand |
| `--om-navy-d` | `#0F1C3A` | Hover/darkened navy, gradients (identity card, storefront header) |
| `--om-pink` / `--om-accent` | `#FF1585` | Accent, highlights, chart fills, brand "one" |
| `--om-cloud` | `#F0F0F4` | Page background, muted panels |
| `--om-ink` | `#1A1A2E` | Body text |
| Bootstrap semantic | success/warning/danger defaults | Status only — never decorative |

Rules: navy = action, pink = emphasis (never both competing on one element); status colours come only from `<x-ui.status-badge>` (one mapping, §2.5); never hardcode new hex values — extend `:root` tokens.

### 1.2 Typography

Merchant app: **Figtree** (400/500/600/700, bunny.net; self-hosting queued INFRA-001). Corporate/storefront/print: system stack. Logo: Arial bold, `one` pink + `member` navy/white. Scale: page `h1` via `.page-header h1`; card titles `fw-semibold` 0.875rem headers; body 1rem; metadata 0.75–0.8rem `text-muted`. Thai first: never rely on capitalisation for emphasis (Thai has none) — use weight and colour.

### 1.3 Icons

**Bootstrap Icons only** (`bi-*`, bundled). Standard pairings: members `bi-people`, campaigns `bi-star`, rewards `bi-gift`, transactions `bi-arrow-left-right`, reports `bi-bar-chart-line`, launch kit `bi-rocket-takeoff`, apps `bi-grid-3x3-gap`, commerce `bi-shop`, orders `bi-receipt`, identity/QR `bi-qr-code(-scan)`, counter `bi-upc-scan`, settings `bi-gear`. New features pick one icon and keep it everywhere (nav, headers, empty states).

### 1.4 Responsive breakpoints (Bootstrap defaults; our layout switches)

| Breakpoint | Behaviour |
|---|---|
| `< 576px` | Language switcher icon-only; stacked tables (`.table-responsive-stack`) |
| `< 768px` | Merchant sidebar becomes overlay (z-index 1055 + backdrop 1050); mobile FAB visible; language dropdown static positioning |
| `≥ 768px` | Persistent sidebar (260px), desktop tables |
| Admin | Desktop-first (240px fixed sidebar); mobile not optimised by design (DECISION-068) |
| Storefront/wallet/portal/cards | Single-column, `max-width` 380–560px, mobile-first always |

Z-index ladder: topbar 100 · FAB 1030 · sidebar backdrop 1050 · sidebar 1055 · sidebar close 1060 · language menu (mobile) 1065.

---

## 2. Components (canonical, implementation-ready)

New reusable components live in `resources/views/components/ui/` (anonymous — use as `<x-ui.*>`). Pre-existing components (`x-app-layout`, `x-modal`(auth), `x-language-switcher`, `x-trial-banner`, `x-subscription-limit-warning`, `x-fab`) remain valid; `ui.*` is the namespace for everything new.

| # | Pattern | Canonical usage |
|---|---|---|
| 2.1 | **Navigation (topbar)** | Provided by `x-app-layout` (`.topbar`: toggle, page title, counter button, language switcher, user). Never build a second topbar. |
| 2.2 | **Sidebars** | Merchant: `x-app-layout` sidebar (`.sidebar`, section labels, `.nav-link` rows, `routeIs()` active states, App links conditional on `hasApp()`). Admin: `x-admin-layout`. New sections copy the existing `<li class="nav-item">` block exactly. |
| 2.3 | **Cards** | `.card` (+`.card-header fw-semibold` with leading icon). KPI: **`<x-ui.stat-card icon label value variant hint>`**. |
| 2.4 | **Tables** | `.table.table-hover mb-0` inside `.card-body.p-0`; header cells auto-styled; mobile: `.table-responsive-stack` with `data-label`. Pagination in `.card-footer`. |
| 2.5 | **Status badges** | **`<x-ui.status-badge :status :label>`** — the ONLY status→colour mapping. |
| 2.6 | **Forms / Inputs** | **`<x-ui.input>` / `<x-ui.select>` / `<x-ui.textarea>`** — label, required-star, `is-invalid` + `@error`, hint. FormRequests always; `novalidate` on forms that rely on server validation display. |
| 2.7 | **Buttons** | `.btn-primary` (navy) main action — one per view; `.btn-accent` (pink) marketing CTAs; `.btn-outline-primary/secondary` secondary; `.btn-outline-danger` destructive (+ confirm). Leading icon `me-1`. |
| 2.8 | **Modals** | **`<x-ui.modal id title>` + footer slot**, triggered via `data-bs-toggle`. |
| 2.9 | **Empty states** | **`<x-ui.empty-state icon title body>` + action slot** — every list must have one. |
| 2.10 | **Charts** | Pure CSS only (ADR-005 minimalism): bars via **`<x-ui.progress-bar :percent color>`**; 30-day columns per the campaign-analytics pattern. No JS chart libraries. |
| 2.11 | **Dashboard widgets** | Card + header icon + list-group/table rows; counts via stat-cards; alerts (win-back pattern): `.alert` + icon + CTA button. |
| 2.12 | **Merchant storefront** | `.storefront-*` family (header gradient, sections, product rows, qty inputs, footer). Public pages: standalone Blade + `@vite` css, `max-width` column, merchant locale. |
| 2.13 | **Customer wallet / cards** | `.identity-card-*` family — pass-shaped (Apple/Google-ready): header logo+label, avatar, name, monospace pink ID, white QR panel, privacy footer. Wallet membership cards (PH2-001D) extend this family. |
| 2.14 | **Commerce (merchant)** | Products table per 2.4; fulfillment settings form per 2.6 with `form-switch` toggles. |
| 2.15 | **Orders** | Order card blocks (merchant) with items list, `<x-ui.status-badge>` for status + payment, action buttons from `Order::TRANSITIONS`. Customer confirmation: storefront family + payment QR (`.commerce-payment-qr-preview`). |
| 2.16 | **Apps marketplace** | App cards: `.stat-icon` + name + status badge + description + install/uninstall buttons (apps/index is the reference implementation). |
| 2.17 | **Mobile layouts** | Overlay sidebar, counter-mode bar, FAB, 44px touch targets (`.btn` min-heights in the mobile block), print pages excluded. |
| 2.18 | **Loading states** | **`<x-ui.spinner :label size>`**. Full-page loads rely on server render — no skeletons. |
| 2.19 | **Toast notifications** | **`<x-ui.flash :with-errors>`** — session `success`/`error` + optional first validation error, dismissible. Set flashes in controllers; never inline ad-hoc alert markup in new views. Session flashes render **once, globally, in the layouts** (`x-app-layout`, `x-admin-layout`); a view that only needs the validation alert passes `:session="false"` (BETA-007A). |
| 2.20 | **Page headers** | **`<x-ui.page-header :title :subtitle|:back-url>` + actions slot**. |
| 2.21 | **Help buttons** | **`<x-ui.help-button topic="screen.key" />`** — round "?" linking to the Knowledge Center article whose `context_key` matches the topic (falls back to Help Center). Global entry lives in the topbar; `label` doubles as tooltip/aria-label (PLATFORM-002 P11). |
| 2.22 | **Image upload card** | `.omega-dropzone` + `resources/js/product-image.js` (OMEGA-001A): drag & drop / click / keyboard browse, guidance text before upload, live preview with metadata, Cropper.js v2 crop (1:1/4:5/16:9) + rotate, client WebP export; server re-optimizes via `ProductImageService` (≤1200px longest edge → WebP). Progressive enhancement — the plain file input works without JS. Reference: commerce product form. |

## 3. Print & Email

Print (launch kit): `.launch-sheet-a4/-a6` mm-sized sheets, `.launch-print-toolbar d-print-none`, one sheet per page. Email: markdown mailables (`mail::message/panel/button/table`), merchant logo header where branded, `__('email.*')` keys, queued always.

## 4. Adoption Policy

1. **New screens:** `ui.*` components are mandatory where a pattern exists.
2. **Existing screens:** adopt on touch — when a sprint edits a view, migrate the edited section (no big-bang rewrite; visual output is identical by construction).
3. **New patterns:** add the component + a row in §2 in the same PR.
4. SEC-002 direction: new components avoid inline `style=""` where a utility/class exists; remaining inline sizes migrate with the CSP sprint.
5. Reference implementation of the full set: `resources/views/apps/index.blade.php` (uses page-header, flash, empty-state, status-badge via the marketplace cards).

## 5. Accessibility Baseline

Labels tied to inputs (ui.* enforce), `aria-label` on icon-only buttons, `role="progressbar"` + values on bars, `aria-expanded` on toggles, visually-hidden text in spinners, colour never the sole signal (badges carry text), Thai/English copy always via `__()` (completeness test enforced).
