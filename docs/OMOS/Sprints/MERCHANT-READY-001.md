# MERCHANT-READY-001 — Merchant Readiness

| Field | Value |
|---|---|
| **Sprint ID** | MERCHANT-READY-001 |
| **Status** | 🔄 In Progress |
| **Type** | Merchant experience — guidance, content, dashboard (no new platform architecture, no new business modules) |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-09 |

---

## Mission

Every new merchant should understand **exactly what to do next** — from first login to a fully launched, member-earning store — without needing a human to walk them through it.

MERCHANT-READY-001 is the umbrella sprint that turns the platform built through PLATFORM-002 and OMEGA-001A–E into something a real Thai SME can pick up and launch alone. It ships in work items:

| Work item | Deliverable | Status |
|---|---|---|
| Help Center & User Manual | 47 EN + 6 TH articles on the Knowledge Center rails; sidebar link; contextual `?` buttons | ✅ Shipped (`856a9e9`, `dd801dd`) |
| **MR-001 — Merchant Launch Dashboard** | Launch Checklist component, Next Recommended Action, Merchant Health Card | ✅ Shipped (`954135e`, `25b8d97`) — CTO approved |
| **MR-002 — Empty States & Contextual Help** | Every empty state answers "what next?"; help buttons on all 8 primary screens; no dead help links | ✅ Shipped (`57bc844`) — CTO approved |
| **MR-003 — Merchant Onboarding Experience** | Guided launch journey: step-success guidance, encouraging progress, Launch Ready celebration | ✅ Shipped (`4a33f3c`) — CTO approved |
| **MR-004 — Merchant Readiness Audit** | UX/QA/a11y/i18n audit; small safe fixes only; TH/MM readiness review | This branch |

---

## MR-001 — Merchant Launch Dashboard

### Scope

1. **Launch Checklist** — a reusable, tenant-scoped checklist component tracking:
   - Business Profile completed (name + business type)
   - Logo uploaded
   - Store URL configured
   - First Product *(shown when the Commerce App is installed)*
   - First Campaign
   - First Reward
   - First Member
   - QR Poster viewed/printed
   - Storefront visited *(shown when the Commerce App is installed)*

   With: completed count, progress %, visual progress bar, and a **Launch Ready** badge at 100%.

   Implementation: evolves the existing `LaunchChecklistService` (LAUNCH-001) — same service, same `launch_flags` mechanism in merchant settings — rather than introducing a parallel system. Data-derived items are computed from real records; "did you do it" items (QR poster, storefront visit) are flags set when the merchant actually opens those pages.

2. **Next Recommended Action** — the dashboard recommends exactly ONE next action: the first incomplete checklist item in a fixed priority order (profile → logo → store URL → product → campaign → reward → member → QR poster → storefront). Deterministic — no AI, no randomness; the same merchant state always yields the same recommendation.

3. **Merchant Health Card** — a dashboard card with green / amber / red status per dimension: Business Profile, Logo, Store URL, Products, Campaigns, Members, Storefront, and overall Launch %. Status rules are fixed and data-derived (see `LaunchChecklistService::health()`).

4. **Quality** — mobile responsive, accessible (ARIA on progress and status indicators), existing design system only (Bootstrap card patterns, `x-ui.progress-bar`, OneMember palette).

### Out of Scope

- New platform architecture (no new services beyond evolving `LaunchChecklistService`, no schema changes, no migrations)
- New business modules or Apps
- AI-driven or personalized recommendations
- Email/notification nudges based on checklist state
- Admin-side merchant health (exists separately as `MerchantHealthService`, ADMIN-001 — untouched)
- Store URL redirect handling (OMEGA-001E out-of-scope note stands)
- Deployment (explicitly: do not push, do not deploy)

### Success Criteria

- A brand-new merchant landing on the dashboard sees their launch progress, what's done, and one clear next step.
- Checklist progress %, completed count, and next action are computed from real tenant data and are correct for any state.
- All checklist state is tenant-scoped: one merchant's data or flags never affect another's checklist.
- 100% completion shows a Launch Ready badge.
- Health card statuses follow the documented green/amber/red rules.
- Works at 375px mobile width; progress bar and statuses carry ARIA attributes.
- Full test suite green; production build clean.

### Exit Criteria

- [ ] Sprint doc, CurrentSprint.md, Product-State.md, CHANGELOG.md updated
- [ ] Launch Checklist component live on the dashboard (reusable Blade component)
- [ ] Next Recommended Action live on the dashboard (deterministic)
- [ ] Merchant Health Card live on the dashboard (green/amber/red)
- [ ] Tests: checklist progress, launch completion, next recommended action, tenant isolation
- [ ] `composer dump-autoload` + `php artisan test` + `npm run build` all clean
- [ ] Committed (not pushed); awaiting CTO review

### Pilot Success Metrics

To be measured across the first pilot merchant cohort:

| Metric | Target |
|---|---|
| Merchants reaching 100% launch checklist within 7 days of signup | ≥ 60% |
| Merchants completing first campaign + first reward within 48 hours | ≥ 70% |
| Merchants adding their first member within 7 days | ≥ 60% |
| Merchants who print/view the QR poster | ≥ 50% |
| Support requests asking "what do I do next?" | ~0 (manual + dashboard answer it) |

---

## MR-002 — Empty States & Contextual Help

### Scope

1. **Empty states** — Members, Campaigns, Rewards (campaign tab + `/rewards` page), Products, Orders, and the Reports/Transactions placeholders all use the design-system `<x-ui.empty-state>` with: friendly, encouraging merchant copy (EN + TH, no technical wording), a primary CTA button, and a contextual Help Center link (new `help-topic` prop).
2. **Contextual help expansion** — `?` buttons now on Dashboard, Members, Campaigns, Rewards (campaign Rewards tab + `/rewards`), Products, Orders, Settings, and Launch Kit. New `rewards` context registered on the `create-rewards` article. A regression test scans every literal `topic=`/`help-topic=` in Blade views and fails if any doesn't resolve to a published article — dead help links cannot ship.
3. **Dead ends removed** — `/rewards` no longer says "coming soon" in hardcoded English; it explains that rewards live inside campaigns and routes the merchant there. The generic coming-soon view (Reports, Transactions) is localized and friendly with a way back to the dashboard.
4. **Visual polish** — rewards-tab empty state converted from hand-rolled markup to the design-system component; consistent page-header + help-button pattern (Launch Kit's) on Dashboard/Settings/Orders; icons `aria-hidden`; empty-state body width/typography unified in the component instead of per-view inline styles.

### Out of Scope (MR-002)

- No business-logic changes — controllers and services untouched; the only `routes/web.php` edit passes different *view data* to the same placeholder routes
- No new platform architecture; no schema changes
- Reports/Transactions remain placeholders (now friendly + localized) — building them is future work

---

## MR-003 — Merchant Onboarding Experience

### Scope

1. **Guided launch journey** — completing any launch step (business profile / logo / store URL via Settings, first product, first campaign, first reward, first member) flashes `launch_step`; a new `<x-launch.step-success>` (rendered once, globally, under the flash) then shows *why the step matters* + the ONE deterministic next action from `LaunchChecklistService`. When the step completes the whole checklist, it hands off to the dashboard celebration instead.
2. **Success experience** — ✓ localized success message (the raw-English create flashes for member/campaign/reward/profile now use the existing `messages.*` EN/TH keys — friction found in the Part 1 review) + why-it-matters + recommended next action.
3. **Progress experience** — the launch checklist shows encouraging steps-left copy ("Just 1 step to go — you're almost there!"), completed items get a screen-reader "completed" suffix; the X/Y · % badge stays.
4. **First-launch celebration** — at 100% the checklist card becomes a calm 🎉 "Congratulations! Your business is now ready to welcome customers." with quick actions: View storefront (Commerce merchants), Print QR poster, Add a customer, Read the merchant guide. No animation.
5. **Onboarding wizard handoff** — the finish page's primary CTA is now "See your launch plan" (dashboard), where the journey continues; Launch Kit and Add member remain as secondary actions. The wizard's own 6 steps, business rules and starter-campaign logic are untouched.

### Out of Scope (MR-003)

- No changes to onboarding business rules, steps, validation, trial start, or starter-campaign creation
- No new platform architecture, schema, or modules
- QR-poster and storefront-visit steps don't flash step-success (they're visit-flags, not form submits) — the dashboard next-action covers them

---

## MR-004 — Merchant Readiness Audit (summary)

Senior-QA/UX audit of the whole merchant experience. **No features, no business logic, no architecture, no schema.** Small safe fixes only; everything else documented here.

### Part 1+4 — Journey & consistency findings

| # | Finding | Action |
|---|---|---|
| 1 | 8 success flashes (member/campaign/reward update-archive-pause-configure) were hardcoded English while EN+TH keys already existed in `messages.php` — Thai merchants saw English after these actions | ✅ Fixed — now use `__('messages.*')` |
| 2 | 3 plan-limit validation errors (member/campaign/reward store) hardcoded English; `messages.*_limit_reached` keys existed | ✅ Fixed |
| 3 | `alt="QR Code"` hardcoded in members/show | ✅ Fixed — `__('members.qr_code')` |
| 4 | Duplicate `qr_coming_soon` key in `lang/en/members.php` + `lang/th/members.php` (second silently won) | ✅ Fixed — deduped |
| 5 | Page titles: all 14 primary merchant pages use localized `pageTitle` slots consistently | ✅ Clean — no action |
| 6 | EN↔TH lang-file parity: **zero missing keys across all files** (scripted check) | ✅ Clean |
| 7 | No `href="#"` dead links, no TODO/FIXME in merchant views; feedback modal fully localized | ✅ Clean |
| 8 | Rewards nav → campaigns landing (MR-002); Transactions/Reports friendly placeholders | ✅ Already handled |

### Part 2 — Mobile audit (375 / 768 / desktop)

Checked with real browser rendering, logged-in merchant with data (dashboard, members list/create/detail, campaigns list/create/detail/analytics, products list/create, orders, commerce settings, launch kit, settings, help, rewards, reports, subscription, apps, public storefront):

- **375px: zero horizontal overflow on every page checked.** 768px (table-heavy pages re-checked): zero overflow. Desktop verified during MR-001–003.
- Empty states, button hierarchy and spacing follow the design system (upgraded in MR-002/003); `.page-header` wraps actions below the title at <576px by design — consistent app-wide.

### Part 3 — Accessibility findings

| # | Finding | Action |
|---|---|---|
| 1 | Reward-search submit button (campaign page) was icon-only with no accessible name | ✅ Fixed — aria-label + title |
| 2 | Reward-search text input had placeholder but no label | ✅ Fixed — aria-label |
| 3 | "Earn method" read-only select not associated with its visible label | ✅ Fixed — for/id |
| 4 | `x-ui.media-upload` native fallback file input had no accessible name (visible when JS fails) | ✅ Fixed — aria-label in the shared component |
| 5 | `html lang` follows the active locale; ARIA progressbars complete; icon-only topbar/help buttons all carry aria-labels; scripted scan found no other unlabelled controls on audited pages | ✅ Clean |
| 6 | No skip-to-content link in the app layout | 📋 Recommendation (small, but styling/testing beyond audit scope) |
| 7 | Contrast: navy `#1A2E5A` on white 12.9:1 ✓; brand pink `#FF1585` on white ≈3.7:1 — fine for non-text (progress fills, accents) per WCAG 1.4.11, **should not be used for body-size text on white** | 📋 Guideline documented |
| 8 | `d-none` remove-checkbox inside media-upload is JS-state only, never user-visible | ✅ No action needed |

### Part 5 — International readiness review (Thailand · Myanmar) — DOCUMENT ONLY

**Assumptions the codebase currently makes**
- Merchant **UI** languages are EN + TH only (`internal_languages`); other locales are customer-facing offers that fall back to English (BETA-008B).
- One primary currency per merchant, display-only, `number_format(x, 2)` everywhere — no conversion, no per-currency decimal rules (ADR-011: money never touches OneMember).
- Dates render Gregorian (CE) via Carbon `translatedFormat`; no Buddhist-era (BE) option.
- Phone numbers: free-form string (max 30), uniqueness is exact-string per merchant — no normalization (`08x` vs `+66 8x` are different members).
- Web font (Figtree via bunny.net CDN) covers Latin only; Thai/Burmese glyphs fall back to system fonts.

**Thailand — READY (launch market)**
- TH default country, THB, Asia/Bangkok, full Thai UI with completeness tests, Thai help articles (6 + EN fallback), storefront/join/portal all locale-aware. Known cosmetic gap: CE-only dates (most Thai SaaS accepts this; BE display would be a welcome polish).

**Myanmar — PARTIALLY READY (customer-side yes, merchant-side no)**
- ✅ Already in place: `MM` country, `MMK` currency, `Asia/Yangon` (+06:30 handled by PHP tz db), Burmese (`my`) offerable as a *customer* language, storefront QR-payment-image flow is provider-agnostic (KBZPay/WavePay QR images work exactly like PromptPay).
- ⚠️ Limitations:
  1. No Burmese merchant UI — `lang/my/` is a 3-key placeholder; merchants would run the app in English.
  2. **Zawgyi vs Unicode**: a large share of Myanmar devices still type/render the legacy Zawgyi encoding; free-text member names/products entered in Zawgyi will look garbled on Unicode devices and vice versa. No detection/conversion exists (and none should be built until the market is real).
  3. MMK amounts show 2 decimals (`1,500.00 MMK`) — kyat is used without decimals; needs per-currency decimal config before launch.
  4. Latin-only webfont → Burmese renders in system fonts of very mixed quality on older Android.
  5. Terms/legal bundle is EN/TH draft only (DR-33); no Myanmar review.
  6. CDN-hosted fonts + ~350KB CSS matter more on Myanmar bandwidth; consider self-hosted fonts and a lighter first paint.
- **Recommendations (future, in order)**: per-currency decimal rules in `config/localization.php`; translate `lang/my/` + extend `TranslationCompletenessTest`; self-host fonts incl. a Myanmar Unicode face (e.g. Noto Sans Myanmar); phone normalization strategy before any cross-border identity work; legal review per market. No code changed for Part 5 (per spec).

### Decisions & Notes

- **Store URL "configured"** = `merchants.slug` is set. Since OMEGA-001E auto-generates a slug at registration, this item is normally complete from day one — it stays on the checklist so merchants discover the Store URL setting exists (the item links to Settings → Business Profile). Recorded deliberately; not a bug.
- **Commerce-gated items** (First Product, Storefront visited) follow the LAUNCH-001 precedent: they appear only when the merchant has the Commerce App, keeping the checklist honest for loyalty-only merchants (7 items instead of 9).
- The previous LAUNCH-001 items "Try Counter Mode" and "Open your Launch Kit" are superseded by the spec's item list; their flags (`counter_tried`, `launch_kit_opened`) continue to be recorded for future use but no longer surface as checklist items.
