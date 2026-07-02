# Accessibility Standards

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 0.1.0 |
| **Status** | Draft |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [Bootstrap-Standards.md](./Bootstrap-Standards.md), [Brand-Standards.md](./Brand-Standards.md), [Coding-Standards.md](./Coding-Standards.md) |

---

## Purpose

Accessibility requirements for OneMember's web interfaces — WCAG 2.1 Level AA as the target.

---

## Standards

### Target Standard
WCAG 2.1 Level AA. This is not aspirational — it is the minimum for the OneMember merchant portal.

### Requirements

#### Semantic HTML
- Use appropriate heading hierarchy (`h1` through `h6`) — never use headings for styling
- Use `<button>` for actions, `<a>` for navigation — never use `<div>` for interactive elements
- Use `<label>` for every form input — never rely on placeholder text alone

#### Keyboard Navigation
- All interactive elements reachable via Tab key
- Visible focus indicator on all interactive elements (Bootstrap provides this by default — do not remove it)
- Modal dialogs trap focus while open
- Custom dropdown menus support arrow key navigation

#### Color and Contrast
- Text contrast ratio ≥ 4.5:1 against background (WCAG AA)
- Check OneMember colors:
  - Deep Navy `#1A2E5A` on White `#FFFFFF`: ✅ passes (contrast ratio ~10:1)
  - Hot Pink `#FF1585` on White `#FFFFFF`: ⚠️ check — may need text alternatives
  - Ink `#1A1A2E` on Cloud `#F0F0F4`: ✅ passes
- Never convey information through color alone — always pair with text or icon

#### Images and Icons
- All `<img>` tags have meaningful `alt` text
- Decorative images: `alt=""`
- Bootstrap Icons used for decoration: `aria-hidden="true"`
- Bootstrap Icons used for interactive actions: include visible label or `aria-label`

#### Forms
- Every input has a visible `<label>` (not just `placeholder`)
- Error messages associated with inputs via `aria-describedby`
- Required fields marked with `required` attribute and visible indicator

### What Is NOT Currently Required
- Screen reader testing on mobile (future goal)
- Full WCAG 2.1 Level AAA
- Support for Internet Explorer (officially dropped)
