# 48-Hour Core Completion Sprint (CORE-001/002, APP-001/002/003)

| Field | Value |
|---|---|
| **Status** | ✅ Complete — 2026-07-06 |
| **Directive** | Product Owner "48-Hour Core Completion Sprint" |
| **Developer** | Claude Fable 5 |

## Delivered (one commit each)

| Sprint | Commit | Scope |
|---|---|---|
| CORE-001 | `bc53edb` | Global onboarding: country (22 countries), currency/timezone/language, versioned terms acceptance (draft pending legal review), country in settings, free plan = 100 members (DECISION-081) |
| CORE-002 | `0e44384` | Apps framework: config registry, installed_apps, hasApp()/app.installed middleware, marketplace placeholder page, audit-logged install/uninstall, no SDK (DECISION-082) |
| APP-001 | `fd2ab30` | Commerce App MVP: products, categories, inventory, fulfillment settings incl. merchant-defined delivery radius (DECISION-083) |
| APP-002 | `cae9be0` | Public Merchant Storefront: /store/{slug} — profile, catalogue, loyalty + rewards, seller-of-record note (DECISION-084) |
| APP-003 | (this commit) | Basic Orders: guest ordering, server-side totals, status lifecycle + audit, merchant payment-QR display, manual payment confirmation (DECISION-085) |

## Rules honoured
Core stayed lightweight (commerce fully behind the App gate; uninstall removes the public storefront). No payment gateway, no country-specific payment integration, no commission logic, no marketplace revenue. OneMember is never merchant of record and never touches customer funds. TH/EN localization throughout (guarded by TranslationCompletenessTest). Suite grew 564 → 608 tests, green after every commit.

## Deliberately deferred
Product images; order notifications (email to merchant on new order — CTO-003 rails ready); loyalty-on-orders (Commerce.md open item); App uninstall data policy detail (DR-34); pricing tiers beyond free-100 (DR-32); legal copy sign-off (DR-33).
