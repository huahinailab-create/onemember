# RELEASE-4A — Campaign Analytics Dashboard

| Field | Value |
|---|---|
| **Sprint ID** | RELEASE-4A |
| **Status** | ⏳ Awaiting CTO Review |
| **Sprint Type** | Feature |
| **Classification** | Type B — new capability (read-only analytics page) |
| **Owner** | Product Owner |
| **Developer** | Claude Fable 5 |
| **Started / Completed** | 2026-07-05 |
| **Related Documents** | [02-Product/Analytics.md](../02-Product/Analytics.md), [09-Roadmap/Roadmap.md](../09-Roadmap/Roadmap.md), [08-Product-Decisions DECISION-073](../../08-Product-Decisions.md) |

---

## Business Objective

Roadmap Phase 1 High item: "Campaign analytics dashboard — per-campaign breakdown." Merchants need to see how each campaign performs (points economy, reward uptake, member engagement) to make merchandising decisions.

## Scope Delivered

1. **Route** `GET /campaigns/{campaign}/analytics` (`campaigns.analytics`), merchant-scoped, linked from the campaign detail page header.
2. **Campaign breakdown cards:** points issued (earn + birthday), points redeemed, points expired, purchase count, purchase total, participating members.
3. **30-day activity trend:** pure-CSS daily bar chart of transaction counts (no JS chart library — consistent with the admin funnel widget pattern).
4. **Member engagement:** top 5 members by points earned with visit counts; 30-day active member count.
5. **Reward performance:** all campaign rewards (including archived) ranked by redemption count with share bars.
6. Full Thai + English localization (25 new keys per language).

## Out of Scope

- Analytics CSV export (Analytics.md lists it; separate sprint)
- Cross-campaign comparison view
- Anonymised consumer analytics (Phase 2, consent-gated)

## Tests

`tests/Feature/CampaignAnalyticsTest.php` — 6 tests: auth required, owner access, tenant isolation (403), real earn/redeem data displayed, empty states, show-page link. Full suite: 515 passed / 1,046 assertions.

## Commit

`feat(campaigns): add per-campaign analytics dashboard (RELEASE-4A)`
