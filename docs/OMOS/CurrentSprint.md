# Current Sprint

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | Live |
| **OMOS Version** | 1.1 |
| **Status** | 🔄 In Progress — MERCHANT-READY-001 / MR-001 |
| **Last Updated** | 2026-07-09 |

| **Related Documents** | [EXECUTE.md](./EXECUTE.md), [Product-State.md](./Product-State.md), [Sprints/README.md](./Sprints/README.md), [Sprint-Lifecycle.md](./Sprint-Lifecycle.md) |

---

## Current Sprint

| Field | Value |
|---|---|
| **Sprint ID** | MERCHANT-READY-001 |
| **Title** | Merchant Readiness (umbrella: Help Center & Manual ✅ · MR-001 ✅ · MR-002 ✅ CTO approved · MR-003 Onboarding Experience 🔄) |
| **Status** | 🔄 In Progress — MR-003 |
| **Sprint Type** | Merchant experience — guidance, content, dashboard (no new platform architecture, no new business modules) |
| **Classification** | Type A — dashboard surfacing on existing rails, content, docs, tests |
| **Sprint File** | [Sprints/MERCHANT-READY-001.md](./Sprints/MERCHANT-READY-001.md) |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-09 |
| **Actual Completion** | — |
| **Final Commit** | Help Center: `856a9e9`/`dd801dd` (merged `c698cea`) · MR-001: see git log |

### Business Objective

Help every new merchant understand exactly what to do next. Work item 1 (✅ shipped): 47 EN + 6 TH merchant manual articles on the PLATFORM-002 Knowledge Center rails, Help Center sidebar link, contextual ? buttons on Members, Campaigns, Products and Launch Kit. Work item 2 (✅ MR-001, CTO approved): Merchant Launch Dashboard — the LAUNCH-001 checklist evolved to the full launch path as a reusable tenant-scoped component (progress %, Launch Ready badge), deterministic Next Recommended Action, and a green/amber/red Merchant Health Card. Work item 3 (✅ MR-002, CTO approved): empty states with friendly EN/TH copy + CTA + contextual Help Center link on every list screen; ? help buttons on all 8 primary screens; /rewards dead end removed; regression test forbids dead help links. Work item 4 (🔄 MR-003 — Merchant Onboarding Experience): guided launch journey — after completing any launch step the merchant sees why it matters + the ONE deterministic next action (`x-launch.step-success`); raw-English create flashes localized (EN/TH); encouraging steps-left progress copy; a calm 🎉 Launch Ready celebration with quick actions at 100%; onboarding finish hands off to the dashboard launch plan. Wizard business rules untouched; no new architecture.

---

## Previous Sprint (OMEGA-001E)

| Field | Value |
|---|---|
| **Sprint ID** | OMEGA-001E |
| **Title** | Store Identity & Public URL Foundation |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | Architecture + Settings UI — reuses existing `slug` column, no migration |
| **Classification** | Type B — CTO Review (new editable merchant-facing field, new endpoint) |
| **Sprint File** | Spec provided directly by Product Owner (this board records scope); DECISION-098; [ADR-015](./12-ADR/ADR-015-Store-Identity-and-Public-URL.md) |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-08 |
| **Actual Completion** | 2026-07-08 |
| **Final Commit** | see git log: OMEGA-001E |

### Business Objective

Formalizes Business Name (brand, exactly as typed, never auto-changed) and Store URL (`merchants.slug`, reused — no migration) as two distinct merchant identities, per spec's "intended to be the FINAL platform architecture sprint before Merchant Readiness." New `App\Services\StoreIdentity\StoreIdentityService` centralizes generation, reserved-word/uniqueness validation, and public-URL resolution — `Merchant::booted()` now delegates to it (identical output, existing merchants' slugs untouched). Reserved words documented once in `config/store_identity.php`. Settings → Business Profile gained an editable "Store URL" field (merchant-facing UI never says "slug"): live sanitize-as-you-type, a debounced live-availability check against a new `GET /settings/store-url/availability` endpoint, a copyable public-URL preview, and a `confirm()` warning (no redirect implemented — explicitly out of scope) when the value actually changes. Backward compatible: Storefront/Join/Launch Kit/Commerce/Identity all continue reading `$merchant->slug` unchanged. Caught and fixed a real mobile/desktop layout bug (URL prefix squeezing the input to unreadable width) via in-browser verification before commit.

---

## Previous Sprint (OMEGA-001D)

| Field | Value |
|---|---|
| **Sprint ID** | OMEGA-001D |
| **Title** | Merchant Branding & Product Experience Polish |
| **Status** | ⏳ Awaiting CTO Review |
| **Final Commit** | see git log: OMEGA-001D |

### Business Objective

Visual polish pass across merchant branding and Commerce. Sidebar brand block redesigned with a fixed, non-cropping logo container (object-fit: contain, light backing for transparent PNGs) and a generated initials avatar (`Merchant::initials()`) when no logo exists. New `Merchant::displayName()` normalizes all-lowercase/ALL-CAPS business names to title case for display only ("mike's coffee" → "Mike's Coffee") — the stored `name` and any mixed-case name typed deliberately ("Wilkinson LLC", "Aufderhar and Sons") are left untouched, so acronyms and connector words are never mangled. Settings' Business Logo section now labels the current-logo state and shows dimension/format/aspect guidance via the existing `<x-ui.media-upload>` component. Commerce Products page gained a "View My Store" button (existing storefront route, opens in a new tab). Product-list hover polish and larger, bordered, non-stretching storefront thumbnails (object-fit: cover). Storefront empty catalogue state got an icon. Global `:focus-visible` outline added as an accessibility safety net. Verified in-browser at desktop and 375px mobile widths for both a logo-less (initials avatar) and a logo-bearing merchant.

---

## Previous Sprint (OMEGA-001A frontend)

| Field | Value |
|---|---|
| **Sprint ID** | OMEGA-001A (frontend) |
| **Title** | Reusable Premium Image Upload UI (Drag/Drop, Crop, Rotate) |
| **Status** | ⏳ Awaiting CTO Review |
| **Final Commit** | see git log: OMEGA-001A frontend |

### Business Objective

A ticket asked to "fix" a broken drag/drop + Cropper.js product-image upload UI. Before writing code, a repo search confirmed no such UI, JS file, or Cropper.js dependency existed — the form had a plain file input. Raised to the Product Owner/CTO, who approved building it as new work (DECISION-097), with the explicit requirement that it be a reusable component, not Product-specific. Delivered: `<x-ui.media-upload>` (generic Blade component) + `resources/js/product-image.js` (enhances every `[data-media-upload]` root), Cropper.js `^1.6.2` added as a dependency, drag/drop, live preview with filename/dimensions/file-size, crop with 1:1/4:5/16:9 presets, rotate left/right, replace/remove, and a structural progressive-enhancement fallback (plain file input works with zero JS). Cropping is client-side only — the server (`MediaService`/`ProductController`, unchanged since ADR-013) receives a normal multipart upload of the cropped bytes. `ProductImageTest` passes unmodified; 728 tests green; build clean.

---

## Previous Sprint (OMEGA-001C)

| Field | Value |
|---|---|
| **Sprint ID** | OMEGA-001C |
| **Title** | Unified Media Foundation |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | Architecture only — no merchant-facing behaviour change |
| **Classification** | Type B — CTO Review (new service layer + config, changes how an existing controller stores files) |
| **Final Commit** | see git log: OMEGA-001C |

### Business Objective

Extract Commerce's product-image upload/validate/store/delete logic (built in OMEGA-001A, i.e. BETA-008A) into a reusable `MediaService` + `config/media.php`, so every future media-bearing module (merchant logos, staff photos, customer avatars, booking images, knowledge-center images, marketplace screenshots, marketing assets, documents, galleries) has one system to build on instead of re-inventing upload/validation/storage-path logic per module. Storage-provider abstraction (public disk today, S3/R2/Spaces/Azure/Backblaze later), a declared (not yet generated) image-variant pipeline, and DTO-only gallery scaffolding (`MediaItem`/`MediaCollection`, no migration) are prepared per ADR-013. `ProductController` now calls `MediaService`; merchant-facing behaviour, routes, and schema are unchanged — `ProductImageTest` passes unmodified.

---

## Previous Sprint (PLATFORM-002)

| Field | Value |
|---|---|
| **Sprint ID** | PLATFORM-002 |
| **Title** | Platform Foundation Sprint (Marketplace, SDK, Events, Webhooks, API, Automation, Knowledge, Queue, Procurement, i18n, Help, Docs) |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | Code — 12-part architectural foundation (Laravel monolith preserved; everything backward compatible) |
| **Classification** | Declared Type A by Product Owner (note: OMOS rules would classify new schema/modules as Type B/C — recorded for the review) |
| **Sprint File** | Spec provided directly by Product Owner (this board records scope); docs/dev/ is the technical record |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-07 |
| **Actual Completion** | 2026-07-07 |
| **Final Commit** | see git log: PLATFORM-002 P1–P12 (12 commits on `fable-dev`) |

### Business Objective

Five-year platform foundation, Shopify/Odoo-style, inside the monolith: Marketplace (manifests, registry, lifecycle, health), Plugin SDK (AppProvider + contracts; third-party-ready without Core changes), domain event bus (10 stable events), signed webhook framework, /api/v1 foundation (keys, rate limits, OpenAPI), WHEN/IF/THEN automation engine, Knowledge Center + screen help framework, Queue and Procurement apps as SDK references, config-driven localization expansion (7 placeholder locales), and full developer documentation.
---

## Previous Sprint (BETA-008)

| Field | Value |
|---|---|
| **Sprint ID** | BETA-008 |
| **Title** | Global Merchant Settings + Product Images |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | Code — two private-beta gaps (no architecture change; Commerce stays an App) |
| **Classification** | Type B — CTO Review (schema addition + localization model); DECISION-094/095 |
| **Final Commit** | see git log: BETA-008A (product images), BETA-008B (localization) |

### Business Objective

Part A — Commerce products get one main image (upload/preview/replace/remove, merchant-scoped storage, validation, list + storefront + order display, placeholder). Part B — Global Settings/Localization tab: country, primary + additional accepted currencies (display only, conversion = future), timezone, internal language separate from ordered customer-facing languages; customer surfaces resolve `?lang=` against the merchant's offered list (never browser). Cambodia (internal EN, customers KM+EN, KHR+USD) and Thailand (TH+EN, THB) covered by tests.

---

## Previous Sprint (BETA-007)

| Field | Value |
|---|---|
| **Sprint ID** | BETA-007 |
| **Title** | Premium Experience Sprint (polish, friction, consistency) |
| **Status** | ⏳ Awaiting CTO Review |
| **Classification** | Type B — CTO Review (cross-cutting UX/i18n changes) |
| **Final Commit** | see git log: BETA-007A…E (5 commits, merged to `main` in `f0e1b36`) |

### Business Objective

Private-beta experience polish across onboarding, dashboard, members, campaigns, rewards, counter mode, commerce, storefront, orders, admin, mobile, empty states, flash messages, TH/EN copy, and accessibility. Sub-sprints: A flash-message single-render, B canonical empty states + dead-UI removal, C enum-label localization (Thai badges), D accessibility/ARIA + onboarding visual bug, E responsive tables. 669 tests green; production build clean.

---

## Previous Sprint (MORNING-001)

| Field | Value |
|---|---|
| **Sprint ID** | MORNING-001 |
| **Title** | Fable Maximum Sprint — Private Beta Readiness |
| **Status** | ✅ Complete — 701 tests green, 0 bugs found in journey/mobile/first-use audits |
| **Sprint Type** | QA / Polish / Demo tooling (no new architecture) |
| **Classification** | Type A — tests, polish, tooling, docs |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-07 |
| **Actual Completion** | 2026-07-07 |
| **Commits** | cd0145e (DEPLOY-001), b9674c1 (BETA-001), 1b003c7 (BETA-003), d6801ed (BETA-005) + docs |

### Business Objective

Make OneMember private-beta ready: verified deploy path (Forge script with post-deploy route checks), the full merchant journey pinned as one sequential test (no bugs found), mobile audit clean at all four widths, customer-facing polish (join landing contact, order receipt hint), first-use guidance confirmed complete, and `onemember:demo-seed` for founder demos (production-gated). Deployment itself awaits founder action on the server (paste deploy script, verify .env, deploy).

---

## Previous Sprint (OVERNIGHT-001)

| Field | Value |
|---|---|
| **Sprint ID** | OVERNIGHT-001 |
| **Title** | Private Beta Stabilization & Bug Hunt |
| **Status** | ✅ Complete — 697 tests green, 1 bug fixed |
| **Sprint Type** | Stabilization / QA (no new features) |
| **Classification** | Type A — bug fixes, tests, docs |
| **Sprint File** | [Sprints/OVERNIGHT-001-Private-Beta-Stabilization.md](./Sprints/OVERNIGHT-001-Private-Beta-Stabilization.md) |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-07 |
| **Target Completion** | 2026-07-07 |
| **Actual Completion** | 2026-07-07 |
| **Final Commit** | see git log: OVERNIGHT-001 P6 |

### Business Objective

Private beta stabilization & bug hunt: deployment verification (route-integrity guard + deploy-troubleshooting docs), end-to-end smoke suite, broken-link audit (none found), mobile table-overflow fix, and safe error-state regression tests. One bug fixed (products table overflow at 375px); no other defects found. 697 tests green.

---

## Previous Sprint (PHASE-A)

| Field | Value |
|---|---|
| **Sprint ID** | PHASE-A (TRIAL/BILLING/LAUNCH/ADMIN/UX/OPS-001) |
| **Title** | Launch Readiness Sprint |
| **Status** | ⏳ Awaiting CTO Review — private-beta readiness |
| **Classification** | Type C — CEO Approval Required (phase change, pricing, legal, vendor) |
| **Sprint File** | [Sprints/CORE-SPRINT-48H.md](./Sprints/CORE-SPRINT-48H.md) |
| **Final Commit** | docs-only, see git log: GLOBAL-001 |

---

## Previous Sprint (RELEASE-2A)

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-2A |
| **Title** | Corporate Website |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint File** | [Sprints/RELEASE-2A-Corporate-Website.md](./Sprints/RELEASE-2A-Corporate-Website.md) |
| **Final Commit** | `fa69508` |

---

## Previous Sprint (RELEASE-1C)

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-1C |
| **Title** | Production Multilingual Architecture (Thai First) |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint File** | [Sprints/RELEASE-1C-Multilingual.md](./Sprints/RELEASE-1C-Multilingual.md) |
| **Final Commit** | `87f2a33` |

---

## Previous Sprint (RELEASE-1B)

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-1B |
| **Title** | Corporate Website & Corporate Identity |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint File** | [Sprints/RELEASE-1B-Corporate-Website.md](./Sprints/RELEASE-1B-Corporate-Website.md) |
| **Final Commit** | `568ff7a` |

---

## Previous Sprint (RELEASE-1A)

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-1A |
| **Title** | OneMember Product Identity |
| **Status** | ✅ Deployed — CTO Approved + PO Approved |
| **Sprint File** | [Sprints/RELEASE-1A-Product-Identity.md](./Sprints/RELEASE-1A-Product-Identity.md) |
| **Final Commit** | `364b29e` |

---

## Next Sprint

| Field | Value |
|---|---|
| **Sprint ID** | TBD |
| **Title** | Awaiting CTO sprint selection |
| **Status** | ⬜ No approved sprint queued |
| **Sprint File** | [Sprints/Backlog.md](./Sprints/Backlog.md) |

---

## Sprint History

| Sprint ID | Title | Status | Commit |
|---|---|---|---|
| PLATFORM-002 | Platform Foundation (12 parts: marketplace→docs) | ⏳ Awaiting CTO Review | HEAD (fable-dev) |
| BETA-008 | Global Merchant Settings + Product Images | ⏳ Awaiting CTO Review | `c57ff9d`/`fb10f08` |
| BETA-007 | Premium Experience Sprint (polish A–E) | ⏳ Awaiting CTO Review | merged `f0e1b36` |
| MORNING-001 | Fable Maximum Sprint — Private Beta Readiness (DEPLOY-001, BETA-001…005) | ✅ Complete (Type A) | `d40dda2` |
| OVERNIGHT-001 | Private Beta Stabilization & Bug Hunt | ✅ Complete (Type A) | `4b62707` |
| DOMAIN-001 | Definitive Domain Model | ✅ Complete | docs-only |
| PLATFORM-001 | OneMember Design System | ✅ Complete (Type A) | HEAD |
| ADMIN-002 | OneMember Control Room | ⏳ Awaiting CTO Review | HEAD |
| PHASE-A (6 sub-sprints) | Launch Readiness — trial ext, terms, checklist, health, UX, go-live | ⏳ Awaiting CTO Review | HEAD |
| APP-003 | Basic Orders (direct payment) | ⏳ Awaiting CTO Review | HEAD |
| APP-002 | Public Merchant Storefront | ⏳ Awaiting CTO Review | `cae9be0` |
| APP-001 | Commerce App MVP | ⏳ Awaiting CTO Review | `fd2ab30` |
| CORE-002 | Apps Framework | ⏳ Awaiting CTO Review | `0e44384` |
| CORE-001 | Global Onboarding + Terms + Free-100 | ⏳ Awaiting CTO Review | `bc53edb` |
| GLOBAL-001 | Global Platform Repositioning | ✅ Complete | docs-only |
| PH2-001A | OneMember Identity Platform | ⏳ Awaiting CTO Review | see git log |
| GOV-001 | Foundational Principles Consolidation (Custodian Model) | ✅ Complete | HEAD (docs-only) |
| RELEASE-5A | Merchant Launch Kit & Onboarding Assets | ⏳ Awaiting CTO Review | `f6e5f55` |
| FINAL-001…006 | Final Engineering Hardening | ✅ Complete (Type A) | `82e0599` |
| VISION-001 | Version 2.0 Vision & Master Roadmap 2026–2030 | ⏳ Awaiting PO Ratification | `7749e13` |
| SCALE-000 | Scalability Review & Phase 2 Blueprint | ⏳ Awaiting Ratification | `d14bd0a` |
| PH2-000  | Customer Wallet Design Package | ⏳ Awaiting CEO Approval | `912b551` |
| ENG-001  | Engineering Backlog Clearance | ✅ Complete (Type A) | `2afb644` |
| RELEASE-4A | Campaign Analytics Dashboard | ⏳ Awaiting CTO Review | `bdb0cfb` |
| MVP-010  | ADR-007 Naming Decision + Campaign Alias | ✅ Complete (Type A) | `0c48fb3` |
| MVP-009  | CRUD Test Coverage | ✅ Complete (Type A) | `f7a49d7` |
| MVP-008  | Win-back Campaign Alerts | ⏳ Awaiting CTO Review | `91c19f2` |
| MVP-007  | Counter Mode UI | ⏳ Awaiting CTO Review | `d6b34fb` |
| MVP-006  | Member Notification Emails | ⏳ Awaiting CTO Review | `5d44d3d` |
| RELEASE-2B | Mobile Merchant Experience | ⏳ Awaiting CTO Review | `ea64eda` |
| RELEASE-2A | Corporate Website | ⏳ Awaiting CTO Review | `fa69508` |
| RELEASE-1C | Production Multilingual Architecture (Thai First) | ⏳ Awaiting CTO Review | `87f2a33` |
| RELEASE-1B | Corporate Website & Corporate Identity | ⏳ Awaiting CTO Review | `568ff7a` |
| RELEASE-1A | OneMember Product Identity | ✅ Deployed | `364b29e` |
| MVP-005  | Pilot Merchant Readiness | ✅ Complete (Type A) | `74c6c94` |
| MVP-004  | Birthday and Expiry Automation | ✅ CTO Approved | `2c35ce6` |
| MVP-003  | Merchant Acquisition Experience | ✅ Complete (Type A) | `d73045d` |
| MVP-002  | Thai Localization Foundation | ✅ CTO Approved | `7e3baf8` |
| MKTG-002 | Merchant Sales Presentation Excellence | ✅ CTO Approved | `1694b01` |
| MKTG-001 | Merchant Presentation v2.0 (EN + TH) | ✅ CTO Approved | `4d46d56` |
| MVP-001 | Merchant Experience Polish | ✅ CTO Approved | `37b7d8c` |
| OMOS-1.1 | Operational Readiness | ✅ CTO Approved | `567939a` |
| AI-OMOS-BOOTSTRAP | OMOS Operational | ✅ Complete | `17e0d40` |
| AI-03 | Application Audit | ✅ Complete | `f8d6ac8` |
| AI-02C | OMOS Self-Driving Foundation | ✅ Complete | `965075d` |
| AI-02B1+B2 | Executive and Product Foundation | ✅ Complete | `67f669f` |
| AI-02A | OneMember Operating System Foundation | ✅ Complete | `eeb9744` |
| AI-01 | AI Development System | ✅ Complete | `09948b9` |
| DEV-01 | Developer Tools | ✅ Complete | `962a82f` |
| DEV-02 | Developer Productivity Suite | ✅ Complete | — |
| BUG-002 | Dashboard Broken Links | ✅ Complete | `056495f` |
| BUG-001 | Email Verification Flow Fix | ✅ Complete | `a26e761` |
| Sprint 6.7 | Merchant Intelligence | ✅ Complete | `73e1af2` |

---

## How to Read CurrentSprint.md

This file is a **status board**, not a sprint specification.

For the full sprint specification, read the sprint file linked in the `Sprint File` field above.

For the complete sprint workflow, see [EXECUTE.md](./EXECUTE.md).

---

## Sprint Status Definitions

| Status | Meaning |
|---|---|
| 🔲 Planning | Sprint spec is being written. Not yet ready for execution. |
| ✅ Ready | Sprint meets Definition of Ready. Awaiting `Continue OMOS`. |
| 🔄 In Progress | Claude Developer is implementing. |
| ⏳ Awaiting CTO Review | Completion report returned. Waiting for AI CTO review. |
| ⏳ Awaiting PO Approval | CTO approved. Waiting for Product Owner deployment decision. |
| ✅ Complete | Sprint approved, committed, and (if applicable) deployed. |
| ❌ Blocked | Sprint cannot proceed. Dependency or decision outstanding. |
| ⛔ Cancelled | Sprint cancelled. Reason in SprintReview.md. |
