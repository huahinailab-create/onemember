# 02 — Site Map & Navigation

> Principle: **shallow, fast, phone-first.** A shop owner on a phone reaches Start Free from anywhere in one tap; nothing important lives more than two taps deep.

## Primary navigation (header, sticky)

```
[onemember]   Features   Industries   Pricing   Resources ▾   |   Login   [ Start Free ]
```

- **Start Free** — pink button, always visible, always rightmost. On mobile it stays visible in the collapsed header.
- **Login** — quiet text link → app.onemember.co/login (merchants, not prospects).
- **Resources ▾** dropdown: Knowledge Center · FAQ · Blog (future) · About · Contact.
- Language switcher: TH ⇄ EN, top-right, remembered. Thai is default for Thai visitors (geo/browser), never forced.
- Mobile: hamburger → full-screen sheet; Start Free and LINE chat pinned at bottom of the sheet.

## Full site tree

```
onemember.co
├── / ............................ Home (03)
├── /features .................... Features overview (04)
│   ├── /features/members
│   ├── /features/campaigns
│   ├── /features/rewards
│   ├── /features/commerce
│   ├── /features/storefront
│   ├── /features/launch-kit
│   ├── /features/analytics
│   └── /features/knowledge-center
├── /industries .................. Industry hub (05)
│   ├── /industries/coffee-shops
│   ├── /industries/restaurants
│   ├── /industries/hair-salons
│   ├── /industries/nail-salons
│   ├── /industries/massage-spa
│   ├── /industries/hotels
│   ├── /industries/retail
│   ├── /industries/fashion
│   ├── /industries/pet-shops
│   └── /industries/beauty-clinics
├── /pricing ..................... Pricing (06)
├── /about ....................... About / story (07)
├── /faq ......................... FAQ, 100 questions (08)
├── /contact ..................... Contact router (09)
├── /resources
│   ├── /help → Knowledge Center (existing, reuse in-product articles publicly where sensible)
│   └── /blog (future — SEO strategy 10)
├── /legal (11)
│   ├── /legal/privacy · /legal/terms · /legal/cookies · /legal/pdpa
│   ├── /legal/acceptable-use · /legal/refunds · /legal/security
├── /partners (Phase 2 — POS vendors, agencies; teaser section on /about until then)
├── /enterprise (lands from Pricing "Talk to us"; can start as a /contact?type=enterprise view)
└── Start Free → app.onemember.co/register (external, tracked)
```

## Footer (site-wide)

Four columns + bar:
1. **Product** — Features, Industries, Pricing, Start Free, Login
2. **Resources** — Knowledge Center, FAQ, Blog, Demo video
3. **Company** — About, Contact, Partners, Careers (future)
4. **Legal** — Privacy, Terms, PDPA, Cookies, Security
Bar: wordmark ("one" pink / "member" navy) · language switcher · LINE OA + Facebook icons · © year OneMember.

## Navigation rules

- Breadcrumbs on industries/features subpages (SEO + orientation).
- Every industries page cross-links its sibling feature pages and vice versa (internal-linking engine — see [10](./10-SEO-Strategy.md)).
- 404 page follows the in-product empty-state pattern: friendly line, Start Free, popular links. Even the 404 sells.
- No mega-menus, no carousels in nav, nothing hover-dependent (phone-first).

## URL conventions

Lowercase, hyphenated, English slugs even for Thai pages (`/industries/coffee-shops?lang=th` or `th/`-prefixed once localization routing is chosen — decision deferred to implementation; blueprint assumes `/th/...` prefix as the SEO-preferred option, see [10](./10-SEO-Strategy.md)).
