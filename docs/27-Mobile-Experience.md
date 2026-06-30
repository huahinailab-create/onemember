# 27 — Mobile Experience

> **Sprint:** 6.5  
> **Last updated:** 2026-06-30  
> **Decision reference:** DECISION-059  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/12-SaaS-Architecture.md](12-SaaS-Architecture.md)

---

## 1. Overview

Sprint 6.5 optimises OneMember for merchants using the platform at the point of sale on a mobile device. The primary persona is a business owner or staff member looking up a customer's loyalty balance, recording a purchase, or adding a new member — all from a phone.

Desktop functionality is unchanged. Every improvement is either CSS-only (responsive behaviour) or additive (Counter Mode bar, FAB).

---

## 2. Changes by Area

### 2.1 Sidebar — Mobile Overlay

On viewports narrower than 768 px the sidebar changes from an inline push layout to a fixed overlay:

| Property | Desktop (≥768px) | Mobile (<768px) |
|---|---|---|
| Position | `sticky` inline | `position: fixed`, off-canvas left |
| Default state | Open (`sidebarOpen: true`) | Closed (`sidebarOpen: false`) |
| Toggle | Hamburger button in topbar | Same button |
| Close gesture | Toggle button | Tap backdrop **or** toggle button |
| Z-index | — | 1055 (above all content) |

**Backdrop** (`div.sidebar-backdrop`): a semi-transparent overlay (rgba 0 0 0 / 0.5) that covers the page when the sidebar is open on mobile. Tapping it closes the sidebar via `@click="sidebarOpen = false"`.

**Alpine.js initial state:** `x-data="{ sidebarOpen: window.innerWidth >= 768 }"` — open on desktop, closed on mobile, evaluated once at mount.

### 2.2 Counter Mode

A merchant preference stored in `merchant.settings['counter_mode']` (boolean). Toggled by `PUT /settings/counter-mode` (CSRF-protected form POST with `@method('PUT')`).

**When Counter Mode is off (default):** No visible change.

**When Counter Mode is on:**
- A blue gradient bar appears immediately below the topbar on every authenticated page.
- The bar contains:
  - "Counter Mode" label (with shop icon)
  - "Find Member" link → `/members`
  - "Add Member" link → `/members/create`
- The "Counter" button in the topbar gains `.active` styling (blue tint).

Counter Mode is designed for the scenario where a merchant hands a phone to a customer or uses it to quickly look up members during service. It keeps the two most common POS actions one tap away without navigating the sidebar.

### 2.3 Floating Action Button (FAB)

A 56 × 56 px circular button fixed at `bottom: 1.5rem; right: 1.5rem`. Visible only on mobile (`.d-md-none`). Links to `/members/create`.

The FAB sits at z-index 1030, below the sidebar (1055) and backdrop (1050), so opening the sidebar covers the FAB naturally.

### 2.4 Touch Targets (44 px minimum)

The WCAG 2.5.5 "Target Size" guideline requires interactive elements to be at least 44 × 44 CSS pixels. Applied via mobile media query (`max-width: 767.98px`) to:

- `.sidebar .nav-link` — `min-height: 44px`
- `.topbar-toggle` — `min-width: 44px; min-height: 44px`
- `.btn` — `min-height: 44px`
- `.form-control`, `.form-select` — `min-height: 44px; font-size: 1rem`

`btn-sm` and `form-control-sm` use 36–38 px (secondary actions where space is tight).

### 2.5 Form Keyboard Hints

| Input | Before | After |
|---|---|---|
| Phone number (member create) | `type="text"` | `type="tel" inputmode="numeric"` |
| Email (member create) | `type="email"` | `type="email" inputmode="email" autocomplete="email"` |

`type="tel"` triggers the phone dialler keyboard on iOS/Android. `inputmode="numeric"` further restricts to the numeric keypad. `autocomplete="email"` enables browser autofill.

### 2.6 PWA Readiness (Groundwork)

The layout `<head>` now includes:

```html
<meta name="theme-color" content="#1d4ed8">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="OneMember">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
```

A `public/manifest.webmanifest` is served at `/manifest.webmanifest` with app name, icons, theme colour, and `"display": "standalone"`.

**Icon placeholders** — `/icons/icon-192.png` and `/icons/icon-512.png` must be added to `public/icons/` before submitting to any app store or for full "Add to Home Screen" support. Full service-worker / offline support is deferred to a future sprint.

### 2.7 Responsive Content Area Padding

On mobile the `.content-area` padding is reduced from `1.75rem` to `1rem` to maximise usable screen width.

---

## 3. Architecture

```
resources/
  css/app.css                      ← Mobile overlay sidebar, FAB, counter mode, touch targets
  views/
    layouts/app.blade.php          ← Backdrop, counter mode bar, FAB include, PWA meta
    components/fab.blade.php       ← FAB component (renders only for authenticated merchants)

app/Http/Controllers/
  CounterModeController.php        ← PUT /settings/counter-mode

lang/
  en/mobile.php                    ← 8 EN strings
  th/mobile.php                    ← 8 TH strings

public/
  manifest.webmanifest             ← PWA manifest

routes/web.php                     ← counter-mode.toggle route
```

---

## 4. Routes

| Method | URI | Name | Controller |
|---|---|---|---|
| `PUT` | `/settings/counter-mode` | `counter-mode.toggle` | `CounterModeController@toggle` |

---

## 5. Counter Mode — Data Flow

```
User taps "Counter" button in topbar
  → form POST PUT /settings/counter-mode
  → CounterModeController::toggle()
      → reads merchant->settings['counter_mode']
      → flips boolean
      → saves via merchant->update(['settings' => $settings])
      → tracks analytics: feature_used {feature: counter_mode, enabled: true/false}
  → redirect back()
  → layout re-renders counter-mode-bar based on new DB value
```

The preference is read from the database on every page load (in `app.blade.php` `@php` block). No session state is used.

---

## 6. Testing

**File:** `tests/Feature/MobileExperienceTest.php` (22 tests)

| Category | Count |
|---|---|
| Counter mode toggle (enable, disable, persist, no-erase-other-settings) | 5 |
| Counter mode auth (requires auth, requires merchant) | 2 |
| Counter mode analytics (tracked) | 1 |
| Counter mode bar layout (shown/hidden, links present) | 4 |
| PWA meta tags (theme-color, manifest, apple) | 3 |
| Sidebar backdrop in layout | 1 |
| FAB (shown, links to add-member) | 2 |
| Form improvements (tel type, autocomplete) | 2 |
| Topbar counter button (shown, active state) | 3 |

---

## 7. No-Change Guarantee — Desktop

None of the CSS changes affect the desktop layout:

- Mobile sidebar override is inside `@media (max-width: 767.98px)` — desktop is unaffected.
- FAB uses `.d-md-none` — hidden on desktop.
- Touch-target overrides are in the same media query.
- Counter Mode bar renders server-side based on `merchant.settings['counter_mode']` — its styling does not affect desktop layout (it's a full-width flex row with `flex-wrap: wrap`).

---

## 8. Future Work

- Service worker + offline cache (full PWA — Sprint TBD)
- App icon assets (`public/icons/icon-192.png`, `icon-512.png`)
- Counter Mode: member search auto-focus when `?search_focus=1` is present on `/members`
- Table card-stacking (`.table-responsive-stack`) can be applied to the members index table once the `data-label` attributes are added to each `<td>`

---

*Last updated: Sprint 6.5 — 2026-06-30*
