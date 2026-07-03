# RELEASE-1B — OneMember Corporate Website & Corporate Identity

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-1B |
| **Title** | Corporate Website & Corporate Identity |
| **Type** | Type B — CTO Review Required |
| **Status** | 🔄 In Progress |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Started** | 2026-07-03 |
| **Target Completion** | 2026-07-03 |

---

## Business Objective

Establish OneMember's corporate presence on `www.onemember.co` and align all application identity to the `.co` domain. This sprint delivers the public-facing corporate website, standardises corporate email addresses, and updates all application references from `onemember.app` to `onemember.co`.

---

## Corporate Domain Policy

| Property | Value |
|---|---|
| Corporate website | `www.onemember.co` |
| Application | `app.onemember.co` |
| Old domain | `onemember.app` (deprecated) |

---

## Corporate Email Identity

| Address | Purpose |
|---|---|
| `no-reply@onemember.co` | Transactional email (default FROM) |
| `support@onemember.co` | Customer support |
| `sales@onemember.co` | Sales enquiries |
| `hello@onemember.co` | General contact |
| `success@onemember.co` | Customer success |
| `billing@onemember.co` | Billing and payments |
| `privacy@onemember.co` | Privacy & PDPA requests |
| `security@onemember.co` | Security disclosures |
| `partners@onemember.co` | Partnership enquiries |
| `careers@onemember.co` | Job applications |
| `media@onemember.co` | Press & media |

---

## Website Pages (15+)

| Route | Page | Priority |
|---|---|---|
| `/` | Home | P0 |
| `/solutions` | Solutions | P0 |
| `/industries` | Industries | P0 |
| `/features` | Features | P0 |
| `/pricing` | Pricing | P0 |
| `/about` | About | P0 |
| `/security` | Security & PDPA | P0 |
| `/contact` | Contact | P0 |
| `/faq` | FAQ | P0 |
| `/resources` | Resources | P1 |
| `/blog` | Blog | P1 |
| `/careers` | Careers | P1 |
| `/partners` | Partners | P1 |
| `/demo` | Book a Demo | P0 |
| `/privacy` | Privacy Policy | P0 |
| `/terms` | Terms of Service | P0 |

---

## Acceptance Criteria

- [ ] All 15+ corporate pages live under `/` corporate routes
- [ ] Corporate layout with nav, footer, SEO meta
- [ ] All `onemember.app` references replaced with `onemember.co` in config, lang, views
- [ ] Bootstrap 5, brand colours (#1A2E5A, #FF1585), no Tailwind
- [ ] SEO-friendly: `<title>`, `<meta description>`, Open Graph tags on every page
- [ ] Responsive: mobile-first, passes preview on all viewports
- [ ] 380 existing tests still pass; no new regressions
- [ ] Committed with message `RELEASE-1B — Corporate Website & Corporate Identity`

---

## Tasks

1. Write sprint spec (this file) ✅
2. Update CurrentSprint.md to activate RELEASE-1B ✅
3. Email identity: update `config/email.php` defaults to `onemember.co`
4. Email identity: update `.env.example`
5. Email identity: update `lang/en/subscription.php` support email
6. Email identity: update `lang/th/subscription.php` support email
7. Email identity: update `resources/views/subscription/index.blade.php` sales email
8. Create `resources/views/layouts/corporate.blade.php`
9. Create `app/Http/Controllers/CorporateController.php`
10. Add corporate routes to `routes/web.php`
11. Add marketing CSS to `resources/css/app.css`
12. Create all 15+ page views in `resources/views/corporate/`
13. `npm run build`
14. Run full test suite
15. Commit
16. Update governance (CurrentSprint.md, Product-State.md)
17. Return completion report. STOP.
