# 04 — UI / UX

> **Status:** Draft  
> **Last updated:** 2026-06-27

## 1. Design System

- **Framework:** Bootstrap 5.3
- **Icons:** Bootstrap Icons 1.x
- **Font:** Inter (Google Fonts via Bunny CDN)

## 2. Layout Patterns

| Layout | File | Used For |
|--------|------|----------|
| `app` | `layouts/app.blade.php` | Authenticated pages with sidebar |
| `guest` | `layouts/guest.blade.php` | Login, register, password reset |

## 3. Colour Palette

| Role    | Bootstrap Token | Hex (approx) |
|---------|----------------|---------------|
| Primary | `--bs-primary` | `#0d6efd`     |
| Success | `--bs-success` | `#198754`     |
| Danger  | `--bs-danger`  | `#dc3545`     |
| Warning | `--bs-warning` | `#ffc107`     |
| Sidebar bg | custom     | `#1e293b`     |
| Body bg    | custom     | `#f8f9fa`     |

## 4. Component Conventions

- Use Bootstrap utility classes first; write custom CSS only when utilities are insufficient.
- Blade components live in `resources/views/components/`.
- Flash messages (`session('success')`, `session('error')`) are handled in `layouts/app.blade.php`.

## 5. Responsive Breakpoints

Bootstrap defaults are used as-is:

| Name | Min width |
|------|-----------|
| sm   | 576px     |
| md   | 768px     |
| lg   | 992px     |
| xl   | 1200px    |
| xxl  | 1400px    |

## 6. Accessibility

- All interactive elements must have visible focus states.
- Icon-only buttons must include `aria-label`.
- Colour is never the sole indicator of state.
- Forms use `<label>` elements linked to inputs via `for`/`id`.
