# Brand Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [](./), [Bootstrap-Standards.md](./Bootstrap-Standards.md), [Accessibility-Standards.md](./Accessibility-Standards.md) |

---

## Purpose

The visual identity standards for OneMember — colors, logo, typography, and component patterns.

---

## Standards

> See also: `ai/06-Branding-Rules.md` for the full branding ruleset.
> This document focuses on the technical application of the brand in code.

### Color Palette in Code

```css
/* CSS variables (set in app.css or layout) */
--onemember-navy:    #1A2E5A;
--onemember-pink:    #FF1585;
--onemember-cloud:   #F0F0F4;
--onemember-ink:     #1A1A2E;
--onemember-white:   #FFFFFF;
```

### Bootstrap Override
Bootstrap's `--bs-primary` must be mapped to `#1A2E5A` (Deep Navy).
This ensures `.btn-primary`, `.badge.bg-primary`, `.text-primary` all render in the correct brand color.

### Logo Usage in Blade
```blade
<img src="{{ asset('images/logo.svg') }}" alt="OneMember" height="32">
```
Never hotlink logos from external URLs. Always serve from the app's asset pipeline.

### Page Checklist (before submitting for review)
- [ ] `x-app-layout` or `x-guest-layout` used
- [ ] Page title is `<h4 class="fw-bold mb-0">...</h4>` or `<h5 class="fw-bold mb-2">...</h5>`
- [ ] Primary action button: `btn btn-primary`
- [ ] All cards: `.card` with `.card-header` and `.card-body`
- [ ] No unbranded colors in browser
- [ ] Mobile view (375px) renders without horizontal scroll
- [ ] All text strings use `__()`
