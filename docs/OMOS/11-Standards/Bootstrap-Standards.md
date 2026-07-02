# Bootstrap 5 Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Brand-Standards.md](./Brand-Standards.md), [Coding-Standards.md](./Coding-Standards.md), [Accessibility-Standards.md](./Accessibility-Standards.md) |

---

## Purpose

How Bootstrap 5 is used in OneMember — approved components, patterns, and restrictions.

---

## Standards

### Framework Version
Bootstrap 5.x only. No Bootstrap 4 patterns. No mixing versions.

### What Is Approved
- All Bootstrap 5 utility classes (spacing, flex, grid, display, text)
- Bootstrap 5 components: card, modal, dropdown, nav, tab, alert, badge, button, table, form
- Bootstrap Icons (`bi bi-*`) — only icon library permitted

### What Is NOT Approved
- Tailwind CSS — not permitted under any circumstances
- Custom CSS frameworks or utility libraries (no UnoCSS, no Windi)
- Font Awesome, Material Icons, Heroicons — Bootstrap Icons only
- jQuery — Bootstrap 5 works without it; use vanilla JS or Alpine.js for interactivity

### Layout Patterns
- Page wrapper: `x-app-layout` (authenticated) or `x-guest-layout` (guest)
- Content: `container-fluid` > `row` > `col-*`
- Cards: `.card` > `.card-header` + `.card-body`
- Tables: `.table.table-hover.align-middle` wrapped in `.table-responsive`
- Forms: each field in `.mb-3`, inputs use `.form-control`, labels use `.form-label.fw-medium`

### Color Mapping
Override Bootstrap's default `primary` to OneMember Deep Navy (`#1A2E5A`).
Accent (Hot Pink `#FF1585`) is used for CTAs not covered by Bootstrap utility classes.
Do not use Bootstrap's default blue for any merchant-visible element.

### Alpine.js
Alpine.js is permitted for lightweight interactivity (show/hide, form state, small reactive data).
It must not be used to build SPAs or replace server-side routing.
Keep `x-data` objects small and focused. Do not build business logic in Alpine.
