# ADR-014 — Reusable Media Upload UI (Drag/Drop, Crop, Rotate)

| Field | Value |
|---|---|
| **Status** | **Approved** (Product Owner, 2026-07-08) |
| **Date** | 2026-07-08 |
| **Author** | Claude Fable 5 (sprint: OMEGA-001A frontend) |
| **Related Documents** | [DECISION-097](../../08-Product-Decisions.md), [ADR-013](./ADR-013-Unified-Media-Foundation.md) (Unified Media Foundation — backend), [DECISION-094](../../08-Product-Decisions.md) (BETA-008A / OMEGA-001A — Product Images backend), [DECISION-029](../../08-Product-Decisions.md) (UI Consistency Standard) |

---

## Context

A ticket asked to "fix" the Product Image upload UI — described as having drag/drop, a Cropper.js crop stage, rotate controls, and size guidance that had supposedly broken. Before touching code, a repo-wide search (`resources/`, `package.json`, `docs/`) confirmed none of that ever existed: the form had a plain `<input type="file">` with a small inline script. This was surfaced to the Product Owner/CTO, who approved it as new work (DECISION-097) rather than a fix, with an explicit architecture requirement: build it once, generically, so other modules needing image upload (merchant logo, staff avatar, supplier logo, gallery items, documents) don't each reinvent it.

This sits directly on top of ADR-013's backend Media Foundation: that ADR abstracted *storage*; this one covers the *upload experience* in the browser. The two are independent layers — the UI component here works by producing a normal multipart file upload that `MediaService` receives exactly as before.

## Decision (approved)

**`<x-ui.media-upload>`** (`resources/views/components/ui/media-upload.blade.php`) is a generic Blade component: `name`, `remove-name`, `current-url`, `current-label`, `recommended`/`minimum` size hints, `formats`, `max-mb`, `aspect`, and `presets` are all props. Nothing in the component or its JS references "product."

**`resources/js/product-image.js`** (registered from the existing single Vite entry, `resources/js/app.js` — no new entry point) finds every `[data-media-upload]` root in the page and enhances it: drag/drop, a live preview, filename/dimensions/file-size, Cropper.js crop stage with aspect-ratio presets, rotate left/right, and replace/remove.

**Cropper.js `^1.6.2`** (the classic stable API — `new Cropper(image, options)`, `.rotate()`, `.setAspectRatio()`, `.getCroppedCanvas()`) was added as a project dependency. Cropper.js v2 (a web-component rewrite) was deliberately not used — it's a materially different, newer, less-proven API for a UI element with no functional need for v2's capabilities.

### Progressive Enhancement (Part 6/7 of the ticket)

This is structural, not a CSS class flip on `<body>`. Each component root renders two sibling blocks:
- `.media-upload-native-fallback` — the real `<input type="file">` and, in edit mode, the real "remove" checkbox. Visible by default. Fully sufficient to select and submit a file with zero JavaScript.
- `.media-upload-enhanced` — the dropzone/crop/rotate/meta UI, rendered with the `hidden` attribute.

`product-image.js` wraps each root's setup in `try/catch`. Only on successful setup does it hide the fallback and reveal the enhanced block. If setup throws (missing element, Cropper failing to load, anything), the fallback is simply never hidden — the native input keeps working exactly as it did before this sprint.

### Crop Is Client-Side Only

`MediaService` and `ProductController` were **not modified for this ADR** (they were already routed through `MediaService` per ADR-013, and stay that way). Cropping happens entirely in the browser: on form submit, if a crop session is active, `Cropper.getCroppedCanvas().toBlob()` produces a new `File`, which is swapped into the real `<input type="file">` via the `DataTransfer` API before the form actually posts. The server receives a normal multipart upload — it has no idea a crop happened. This keeps the ADR-013 backend contract (`MediaService::store()`/`validationRules()`) completely unchanged.

### Removal Uses the Existing Checkbox, Not a New Field

The "remove image" affordance is still the same `remove_image` checkbox `ProductController` already reads via `$request->boolean('remove_image')`. It lives inside the (visually hidden, but still form-participating) native fallback block; the enhanced UI's "Remove" button toggles that same checkbox's `.checked` property rather than introducing a second field. No backend validation or controller change was needed.

## Rationale

Scoping this to "one component, many future callers" now — while there is exactly one real caller (Product images) — costs one config-driven Blade component and one JS file. The alternative (building it Product-specific, generically "later") is exactly the mistake ADR-013 was written to avoid on the backend side; doing it here for the frontend keeps both halves of the Media Foundation consistent.

## Consequences

- **Positive:** any future image-upload need can add `<x-ui.media-upload name="..." aspect="..." presets="[...]" />` and get drag/drop + crop + rotate for free, with zero JS changes. Cropper.js v1's stable API means low upgrade risk. Progressive enhancement means a JS bundling failure degrades to a fully functional plain file input, not a broken form.
- **Negative / accepted:** cropping only applies to a newly selected file, not to an already-stored image reopened for edit (re-processing a stored image client-side would need a same-origin `fetch()` + canvas round-trip; deferred as it wasn't requested and isn't needed for the common case). Cropper.js (~30 KB gzipped incl. CSS) is now a global asset via the single `app.js` entry rather than lazy-loaded per-page — acceptable at current bundle size (`app.js` 169 KB / 53 KB gzipped total) but worth splitting into its own chunk if more heavy per-page dependencies get added later.
- **Reversibility:** high. Reverting `ProductController`'s view to the plain `<input>` markup and removing the two new files/dependency has no data or schema impact — the underlying `image`/`remove_image` form fields never changed shape.
