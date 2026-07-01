# OneMember Branding Rules

These rules are mandatory. Every UI component, page, email, and export must comply. No exceptions without explicit Product Owner approval.

---

## Color Palette

| Role | Name | Hex | Usage |
|---|---|---|---|
| Primary | Deep Navy | `#1A2E5A` | Navigation, headings, primary buttons, sidebar |
| Accent | Hot Pink | `#FF1585` | CTAs, badges, highlights, active states, links on dark bg |
| Background | Cloud | `#F0F0F4` | Page background, secondary cards, table rows |
| Body Text | Ink | `#1A1A2E` | All body text, labels, table content |
| White | White | `#FFFFFF` | Card backgrounds, modal backgrounds, form fields |

### Do Not Use
- Do not introduce new primary colors.
- Do not use Bootstrap's default `primary` blue (`#0d6efd`) for anything visible to the merchant.
- Do not use random greys — use `#F0F0F4` (Cloud) or Bootstrap's `text-muted`.
- Do not use red (`#dc3545`) for anything except error states.

### Bootstrap Theme Mapping
When using Bootstrap 5 utility classes that reference `primary`, ensure the Bootstrap theme has been configured to map `primary` to `#1A2E5A`. Use inline style or custom CSS variable only if Bootstrap's utility class produces the wrong color.

---

## Logo

- Use the official OneMember logo only.
- Never alter, stretch, rotate, recolor, or crop the logo.
- Minimum clear space around the logo: equal to the height of the "O" in the wordmark.
- On dark backgrounds: use the light/white version of the logo.
- On light backgrounds: use the full-color or dark version of the logo.
- Do not place the logo on a patterned, gradient, or photographic background.

---

## Typography

- Body text: Bootstrap 5 default (`system-ui`, `-apple-system`, `sans-serif` stack).
- Headings: `fw-bold` or `fw-semibold` Bootstrap utility classes.
- Never import external fonts (no Google Fonts, no Adobe Fonts) without explicit approval.
- Font sizes: use Bootstrap's responsive type scale (`fs-1` through `fs-6`, `small`, `.text-sm`).

---

## Layout Patterns

### Application Pages (authenticated)
- Use `x-app-layout` as the outer wrapper.
- Sidebar navigation is always present on desktop.
- Content area uses Bootstrap grid (`container-fluid`, `row`, `col-*`).
- Page header: `<h4 class="fw-bold mb-0">Page Title</h4>` inside a `.d-flex` bar with action buttons right-aligned.

### Guest Pages (unauthenticated)
- Use `x-guest-layout` as the outer wrapper.
- Centered card, max-width ~450px, on the Cloud background.

### Cards
```html
<div class="card mb-4">
    <div class="card-header fw-semibold">Section Title</div>
    <div class="card-body">...</div>
</div>
```

### Buttons
| Purpose | Class |
|---|---|
| Primary action | `btn btn-primary` (Deep Navy) |
| Destructive action | `btn btn-danger` |
| Secondary / cancel | `btn btn-outline-secondary` |
| Accent highlight | `btn btn-accent` or custom style with `#FF1585` |
| Small buttons | Add `btn-sm` |

### Badges
| Purpose | Class |
|---|---|
| Active / success | `badge bg-success` |
| Warning / trial | `badge bg-warning text-dark` |
| Inactive / archived | `badge bg-secondary` |
| Hot Pink accent | `badge` with custom background `#FF1585` |

### Tables
- Use `.table.table-hover.align-middle` on all data tables.
- Wrap in `.table-responsive` for mobile.
- Header row: `.table-light`.
- Action column: right-aligned, `text-end`.

### Forms
- All inputs: `form-control` or `form-select`.
- All labels: `form-label fw-medium`.
- Error state: `is-invalid` class + `<div class="invalid-feedback">`.
- Success state: `is-valid` (use sparingly).
- Group related fields in a `<div class="mb-3">`.

### Icons
- Use Bootstrap Icons (`bi bi-*`) only.
- Do not use Font Awesome, Heroicons, or Material Icons.
- Icon + label: `<i class="bi bi-plus-lg me-1"></i>Label`

---

## Email Templates

- Background: `#F0F0F4` (Cloud)
- Card background: `#FFFFFF` (White)
- Header/logo area: `#1A2E5A` (Deep Navy)
- CTA button: `#FF1585` (Hot Pink) or `#1A2E5A` (Deep Navy)
- Body text: `#1A1A2E` (Ink)
- Table-based layout for email client compatibility.

---

## What a New Page Must Look Like

Before submitting a page for review, confirm:

- [ ] Uses `x-app-layout` or `x-guest-layout`
- [ ] Page title uses `fw-bold` heading
- [ ] Primary action button uses `btn-primary` (Deep Navy)
- [ ] Cards use `.card` with `.card-header` and `.card-body`
- [ ] No unbranded colors appear in browser
- [ ] Mobile view renders without horizontal scroll
- [ ] Bootstrap Icons used for all iconography
- [ ] All text strings use `__()` for localization
