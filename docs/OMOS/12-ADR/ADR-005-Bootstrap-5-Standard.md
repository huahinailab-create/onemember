# ADR-005 — Bootstrap 5 as the Sole Frontend Framework

| Field | Value |
|---|---|
| **Status** | Approved |
| **Date** | 2026-07-02 |
| **Author** | ChatGPT CTO |
| **Supersedes** | None |
| **Superseded by** | None |
| **Related Documents** | [CTO-Decisions.md](../CTO-Decisions.md#cto-002), [11-Standards/Bootstrap-Standards.md](../11-Standards/Bootstrap-Standards.md), [11-Standards/Brand-Standards.md](../11-Standards/Brand-Standards.md) |

---

## Context

The merchant portal and customer-facing join flows require a CSS framework for layout, components, and responsive design. The choice of framework affects developer velocity, design consistency, build complexity, and long-term maintainability.

## Decision

**Bootstrap 5 is the sole CSS framework for OneMember.** Tailwind CSS is explicitly not permitted. Alpine.js is permitted for lightweight interactivity. No other CSS frameworks may be introduced without a new ADR.

## Options Considered

### Option A — Bootstrap 5 (chosen)
Stable, well-documented component library. No build step required for basic usage. Responsive grid. Complete component set (modals, dropdowns, tabs, forms, tables). Large ecosystem.

**Pros:** Zero build configuration required. Works with Blade natively. Consistent components. Developer-familiar.  
**Cons:** Opinionated styles require overriding for branding. Larger CSS bundle than Tailwind (when Tailwind is tree-shaken).

### Option B — Tailwind CSS
Utility-first CSS framework. Highly customisable. Requires PostCSS build pipeline. Produces optimised CSS via tree-shaking.

**Pros:** Maximum design control. Optimised bundle size in production. Popular in modern stacks.  
**Cons:** Requires build pipeline (PostCSS, purging). Class-heavy HTML is harder to read. Different mental model from component-based design. Would require redesigning all existing UI.

### Option C — No Framework (custom CSS)
Write all CSS from scratch.

**Pros:** Complete control.  
**Cons:** Requires a designer or significant CSS engineering time. Responsive design is expensive to build and maintain. Reinvents solved problems.

## Rationale

Bootstrap 5 is the correct choice for a single-developer Laravel+Blade application at this stage:

1. No build step: Blade templates render Bootstrap classes without Webpack or Vite configuration
2. Component alignment: Bootstrap modals, dropdowns, and tabs align with the merchant portal's UX patterns
3. Existing codebase: All existing UI is Bootstrap 5. Introducing Tailwind would create a two-framework codebase with inconsistent styling
4. Brand integration: Bootstrap 5 CSS variables allow OneMember's brand palette to override Bootstrap defaults cleanly

## Consequences

### Positive
- Consistent UI across all pages without a designer
- No build pipeline complexity for CSS
- Any developer familiar with Bootstrap 5 can contribute immediately

### Negative
- Bootstrap's default component styles require CSS variable overrides to match OneMember branding
- Bootstrap's bundle size is larger than a tree-shaken Tailwind build (acceptable at current scale)
- Some modern design patterns (e.g., complex dark mode, extensive animations) are harder with Bootstrap

### Risks
- Bootstrap 5 may become less maintained in the future. Mitigated by: Bootstrap 5 is in active maintenance; a decision to upgrade will be captured in a new ADR when relevant.

## Validation

This decision is working when: all new pages are visually consistent with existing pages, no designer is required for merchant portal work, and mobile responsive behaviour is correct without custom CSS.
