# 29 — Merchant Intelligence

> **Sprint:** 6.7  
> **Last updated:** 2026-06-30  
> **Decision reference:** DECISION-061  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/25-Merchant-Branding.md](25-Merchant-Branding.md)

---

## 1. Overview

Sprint 6.7 adds actionable business intelligence to the merchant dashboard. Merchants see a health score, up to five prioritised insights, and a set of opportunity cards — all derived from their existing loyalty data using deterministic rules.

**This is not AI.** All insights are computed by `RuleBasedInsightProvider` using simple data queries. The architecture uses `InsightProviderInterface` so a future `AiInsightProvider` can replace or augment the rule-based logic without changing the UI.

---

## 2. What's NOT included (by design)

- No external AI or ML API calls in V1
- No insight emails or push notifications (stub only)
- No historical trend charts (deferred)
- No per-segment analytics (deferred)

---

## 3. Architecture

```
DashboardController
    │  injects MerchantIntelligenceService
    │
    └─ MerchantIntelligenceService      ← Single source of truth; handles caching
          │  injects InsightProviderInterface
          │
          └─ RuleBasedInsightProvider   ← V1 implementation; all rule logic here
```

### Interface contract

`App\Contracts\InsightProviderInterface` defines one method:

```php
public function analyze(Merchant $merchant): array;
// Returns: ['insights' => [...], 'health_score' => [...], 'opportunities' => [...]]
```

A future AI provider must implement the same method signature and return the same shape — the UI will not need to change.

### Service methods

| Method | Returns | Cached |
|---|---|---|
| `getInsights(Merchant)` | Max 5 insights, high→low priority | Yes (15 min) |
| `getHealthScore(Merchant)` | score, label, explanation, badge_class | Yes (15 min) |
| `getOpportunities(Merchant)` | Up to 4 opportunity cards | Yes (15 min) |
| `getWeeklySummary(Merchant)` | Structured weekly stats | No |
| `clearCache(Merchant)` | void | — |

All three cached outputs share a single cache key (`merchant_intelligence_{id}`) — the provider's `analyze()` is called once and all outputs are stored together.

### Binding

`InsightProviderInterface` is bound to `RuleBasedInsightProvider` in `AppServiceProvider::register()`. Swapping to a future AI provider requires changing only this binding.

---

## 4. Health Score (0–100)

| Factor | Max points |
|---|---|
| At least 1 active campaign | +15 |
| At least 1 active reward | +10 |
| Member count: 1/10/50/100+ | +5/+10/+15/+20 |
| Transactions (30 days): 1/10/30/100+ | +5/+15/+20/+25 |
| Any redemptions ever | +15 |
| Paid subscription plan | +15 |
| **Total** | **100** |

**Labels:**

| Score | Label | Badge |
|---|---|---|
| 80–100 | Excellent | `bg-success` |
| 60–79 | Good | `bg-primary` |
| 40–59 | Needs Attention | `bg-warning text-dark` |
| 20–39 | Getting Started | `bg-secondary` |
| 0–19 | New Business | `bg-secondary` |

---

## 5. Insights

Up to 5 insights are shown on the dashboard, sorted by priority (high → medium → low). If more than 5 rules fire, the lowest-priority ones are dropped.

| Insight | Icon | Priority | Condition |
|---|---|---|---|
| No active campaign | `bi-star-fill` | High | Members exist but 0 active campaigns |
| No rewards on campaign | `bi-gift` | High | Active campaign exists, 0 active rewards |
| Inactive customers | `bi-person-x` | High | Active members with `joined_at < 45d` and `last_activity_at < 45d` |
| Near reward | `bi-trophy` | High | Active members with points ≥ 80% of lowest reward threshold |
| Birthday this week | `bi-cake2` | Medium | Active members with birthday in next 7 days |
| New members this month | `bi-people-fill` | Low | Count of members joined since start of month > 0 |

Each insight includes:
- `icon` — Bootstrap Icon class
- `priority` — `high`, `medium`, or `low`
- `text` — Pre-translated insight text (via `trans_choice()`)
- `action_label` / `action_url` — Optional CTA link

---

## 6. Opportunities

Opportunity cards appear in the intelligence card footer. Up to 4 cards are shown.

| Opportunity | Condition |
|---|---|
| Create your first campaign | No campaigns at all |
| Add rewards to campaign | Active campaign exists but has 0 rewards |
| Set up a birthday reward | No birthday rewards, but members exist |
| Grow your member base | Fewer than 10 active members |

---

## 7. Caching

Results are cached for **15 minutes** per merchant under the key `merchant_intelligence_{merchant_id}`.

The cache is **not** proactively cleared when a merchant adds a member, records a purchase, or redeems a reward. Stale results expire naturally. This is acceptable because insights are guidance, not real-time data.

`MerchantIntelligenceService::clearCache(Merchant)` is available for future use (e.g., if a future sprint wants to clear on specific events).

---

## 8. Dashboard Integration

The **Business Insights** card is inserted between the Subscription Usage card and the Recent Activity section (Section 2c). It shows:

1. Card header with title + health score badge (`{label} · {score}/100`)
2. List of up to 5 insights with icon, text, and optional action link
3. "Action needed" badge on high-priority insights
4. Opportunity cards in the card footer (if any exist)

No inline CSS is used. No inline JS is used. All strings come from `lang/en/intelligence.php` and `lang/th/intelligence.php`.

---

## 9. Weekly Summary (stub)

`getWeeklySummary(Merchant)` returns a structured array for future email use. It does NOT send any emails in V1.

```php
[
    'period_start'     => Carbon,
    'period_end'       => Carbon,
    'new_members'      => int,
    'purchases'        => int,
    'rewards_redeemed' => int,
    'health_score'     => int,
    'health_label'     => string,
]
```

---

## 10. Localization

| File | Keys |
|---|---|
| `lang/en/intelligence.php` | 37 keys |
| `lang/th/intelligence.php` | 37 keys |

Covers: card title, health score labels and explanations (5 × 2), insight texts (6), action link labels (3), priority badge, empty state, opportunity titles/descriptions/actions (4 × 4).

---

## 11. Testing

**File:** `tests/Feature/MerchantIntelligenceTest.php` (25 tests)

| Category | Count |
|---|---|
| Health score (zero, increases with campaign/members/purchases, labels) | 6 |
| Inactive customer insight | 2 |
| Near-reward insight | 2 |
| Birthday insight | 1 |
| No-campaign insight | 1 |
| Priority ordering | 1 |
| Cap at 5 insights | 1 |
| Opportunities (create campaign, add rewards, grow members) | 3 |
| Caching (15-min TTL, separate per merchant) | 2 |
| Tenant isolation | 1 |
| Dashboard integration (card visible, score shown) | 2 |
| Weekly summary structure | 1 |
| Localization | 1 |

---

## 12. Future AI Enhancement Path

Per DECISION-061, the interface-based architecture means upgrading to AI recommendations requires:

1. Implement `App\Contracts\InsightProviderInterface` in a new `AiInsightProvider` class
2. Change the binding in `AppServiceProvider::register()` from `RuleBasedInsightProvider` to `AiInsightProvider`
3. No changes to `MerchantIntelligenceService`, `DashboardController`, or any Blade view

The rule-based provider can remain as a fallback when the AI provider is unavailable.

---

*Last updated: Sprint 6.7 — 2026-06-30*
