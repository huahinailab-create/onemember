# INTEGRATION-001 — Unified Beta Release Candidate

| Field | Value |
|---|---|
| **Sprint ID** | INTEGRATION-001 |
| **Type** | Integration + regression validation (no features, no redesign) |
| **Branch** | `integration-001-beta-rc` (from main `20084eb`) |
| **Status** | ⏳ Awaiting CTO Review — NOT merged to main, NOT pushed, NOT deployed |
| **Developer** | Claude Fable 5 |
| **Date** | 2026-07-16 |

---

## Sources merged (exact tips)

| Source | Tip | Contents |
|---|---|---|
| `main` | `20084eb` | Base (Merchant Readiness + launch-planning cycle) |
| `website-002a-public-site` | `fc5b4b9` | WEBSITE-002A MVP + polish pass ("WEBSITE-002B"), DECISION-099 |
| `release-001-beta-readiness` | `ca2313d` | CUSTOMER-001A (`652a08c`) → 001B (`2eedc45`) → 001C (`bf8ce25`) → RELEASE-001 fixes; DECISION-100/101/102, ADR-016/017/018 |

Merge commits: `3db897a` (website), `dfcd90d` (customer + release). All source branches preserved; no rebase, no squash. Note: the remote is unreachable from this machine (SSH denied); local `main` verified identical to the `origin/main` ref.

## Conflict resolutions (union — nothing discarded)

- **resources/css/app.css** — both branches appended blocks at EOF; website-polish block and customer identity/address/wallet blocks both kept. The overlap swallowed one closing brace of `.corp-section.pb-0 + .corp-section` (found by PostCSS "Unclosed block" + brace-balance check); restored to the website branch's exact rule. Verified: all selectors from both parents present, build clean.
- **docs/08-Product-Decisions.md** — DECISION-099 (website side) + 100–102 (customer side) kept in numeric order; single append-anchor retained. The 099/100 numbering had been deliberately coordinated across branches, so no renumbering was needed.
- **docs/CHANGELOG.md** — entries from both sides interleaved newest-first.
- **docs/OMOS/CurrentSprint.md** — rebuilt from both parents: WEBSITE-002A(+polish) inserted as a Previous Sprint in the customer-side board.
- **docs/OMOS/Product-State.md** — both sides' unmerged-branch rows replaced by one integration-branch row.
- **public/build/** — regenerated via `vite build`, never hand-edited.

No source-code conflicts occurred — the two branch lines touched disjoint application code (corporate site vs. customer platform); RELEASE-001's corporate-layout og/favicon edits auto-merged with the website branch's equivalent (both use the PNG og-image).

## Verification

- **Tests:** 991 passed, 2,762 assertions, 0 failures (886 website-side + 949 customer-side suites unioned + 10 new integration tests; shared base counted once).
- **Build:** clean; all four Vite entries (`app.css`, `app.js`, `product-image.js`, `corporate.js`).
- **Integrity:** conflict-marker sweep clean; 297 routes, no duplicate names/URIs; `view:cache` compiles every Blade; EN/TH lang files fully paired; `config:show auth` shows both guards/providers; `migrate:status` all ran, timestamps ordered, no duplicate migration names or indexes.
- **New integration tests** (`tests/Feature/IntegrationBetaTest.php`, 10): website renders beside the customer platform; RELEASE-001 assets survive the merge; slim corporate bundle vs. app bundle per surface; branded localized 404; guard isolation both directions (real-HTTP customer login never touches the `web` guard); login pages coexist; storefront → saved-address checkout → wallet order history; guest checkout unchanged; customer account + merchant membership links coexist; Thai on website and wallet.
- **Browser smoke** (dual-domain dev server, process-scoped env): guest website→storefront, customer register/login→wallet→addresses→checkout surfaces, merchant login→dashboard→members/campaigns/rewards/help — EN + TH at 375/768/1440, zero console errors, zero failed requests, no horizontal scrolling (Bootstrap negative-margin measurement at 375 is clipped by the polish pass's `overflow-x:hidden`, not user-visible).

## Remaining release blockers (unchanged from RELEASE-001 — documented, not fixed)

1. **Email-first OTP decision required** — `LogSmsProvider` is the only SMS implementation; phone OTP has no production delivery path. Beta must launch email-OTP/password-first or wait for an SMS gateway sprint.
2. **Production duplicate-email pre-check** — required before running the CUSTOMER-001A migration (unique index on `customers.email`).
3. **Customer self-join gap** — storefront visitors cannot self-join a merchant programme; linking is merchant-side. Documented, not fabricated.
4. **Order-completion → points gap** — orders do not award loyalty points automatically. Documented, not fabricated.
5. **Infrastructure configuration** — SMTP/Resend, queue worker, scheduler cron, `storage:link`, DNS/TLS/session domain, Stripe keys (test placeholders), support mailboxes, `LINE_OA_URL`, backups. Full checklist in the RELEASE-001 report.
6. **DECISION-014 (pricing) and DR-33 (legal)** — still open; site correctly shows ฿0/TBA and gates legal pages.

No deployment was performed.
