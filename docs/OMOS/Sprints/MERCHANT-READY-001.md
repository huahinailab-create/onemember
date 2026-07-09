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
| **MR-002 — Empty States & Contextual Help** | Every empty state answers "what next?"; help buttons on all 8 primary screens; no dead help links | This branch |

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

### Decisions & Notes

- **Store URL "configured"** = `merchants.slug` is set. Since OMEGA-001E auto-generates a slug at registration, this item is normally complete from day one — it stays on the checklist so merchants discover the Store URL setting exists (the item links to Settings → Business Profile). Recorded deliberately; not a bug.
- **Commerce-gated items** (First Product, Storefront visited) follow the LAUNCH-001 precedent: they appear only when the merchant has the Commerce App, keeping the checklist honest for loyalty-only merchants (7 items instead of 9).
- The previous LAUNCH-001 items "Try Counter Mode" and "Open your Launch Kit" are superseded by the spec's item list; their flags (`counter_tried`, `launch_kit_opened`) continue to be recorded for future use but no longer surface as checklist items.
