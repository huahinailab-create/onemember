# 22 — Feedback and Analytics

## Overview

Sprint 5.7 introduced a vendor-independent feedback collection system and an analytics abstraction layer. The design ensures that provider changes require edits to a single service file and that analytics failures never break the application.

---

## 1. Architecture

### The Abstraction Principle

All analytics and error-tracking calls go through `App\Services\AnalyticsService`. No controller, command, or view may call a vendor SDK directly.

```
Controller → AnalyticsService → PostHog / Sentry / null
```

This means:
- Swapping PostHog for Mixpanel requires editing only `AnalyticsService`.
- Disabling analytics entirely requires changing one `.env` flag.
- A provider outage or misconfiguration never crashes the application.

### Key Design Choices

| Choice | Reason |
|---|---|
| No-op when disabled | Analytics failures must not interrupt the user |
| Native PHP HTTP for PostHog | No SDK package dependency; stays within KISS principle |
| `class_exists()` for Sentry | Sentry SDK is optional; detected at runtime, not required |
| JSON files for feedback | No database migration; zero schema risk at V1.0 |
| Feature toggles per category | Granular control without code changes |

---

## 2. AnalyticsService

**File:** `app/Services/AnalyticsService.php`

### Public Methods

| Method | Purpose |
|---|---|
| `identifyMerchant(Merchant $merchant)` | Send merchant profile to analytics provider |
| `identifyUser(User $user, ?Merchant $merchant)` | Send user profile to analytics provider |
| `page(string $name, ?string $url, array $extra)` | Track a page view |
| `track(string $event, array $properties, ?int $userId, ?int $merchantId)` | Track a named event |
| `feature(string $feature, array $properties)` | Track a feature interaction |
| `exception(Throwable $e, array $context)` | Send an exception to error tracking |
| `activationMetrics(Merchant $merchant)` | Return activation milestone timestamps and time deltas |
| `merchantActivityScore(Merchant $merchant)` | Return a 0–100 engagement score |

### Internal Flow

```
canTrack($feature)
    ↓ false → return silently
    ↓ true
dispatch($method, $payload)
    ↓ catches all Throwable
    sendToPostHog($method, $payload)   // if provider = posthog
```

`dispatch()` wraps every outbound call in a `try/catch(Throwable)` that logs the failure at debug level and returns. This guarantees the service never throws.

---

## 3. Configuration

**File:** `config/analytics.php`

```php
return [
    'enabled'  => (bool) env('ANALYTICS_ENABLED', false),
    'provider' => env('ANALYTICS_PROVIDER', 'null'),
    'posthog'  => [
        'api_key' => env('POSTHOG_API_KEY', ''),
        'host'    => env('POSTHOG_HOST', 'https://app.posthog.com'),
        'timeout' => (int) env('POSTHOG_TIMEOUT', 2),
    ],
    'sentry'   => [
        'dsn'                 => env('SENTRY_DSN', ''),
        'environment'         => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),
        'traces_sample_rate'  => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.0),
    ],
    'features' => [
        'page_views' => (bool) env('ANALYTICS_TRACK_PAGE_VIEWS', true),
        'events'     => (bool) env('ANALYTICS_TRACK_EVENTS', true),
        'exceptions' => (bool) env('ANALYTICS_TRACK_EXCEPTIONS', true),
        'identify'   => (bool) env('ANALYTICS_IDENTIFY', true),
    ],
];
```

### Environment Variables

```
ANALYTICS_ENABLED=false          # Master switch; false = complete no-op
ANALYTICS_PROVIDER=null          # Options: null, posthog
POSTHOG_API_KEY=                 # PostHog project API key
POSTHOG_HOST=https://app.posthog.com
POSTHOG_TIMEOUT=2                # HTTP timeout in seconds
SENTRY_DSN=                      # Sentry project DSN
SENTRY_ENVIRONMENT=production
SENTRY_TRACES_SAMPLE_RATE=0.0    # 0.0 = no performance tracing
ANALYTICS_TRACK_PAGE_VIEWS=true
ANALYTICS_TRACK_EVENTS=true
ANALYTICS_TRACK_EXCEPTIONS=true
ANALYTICS_IDENTIFY=true
```

---

## 4. Events Tracked

| Event | Controller / Command | When |
|---|---|---|
| `merchant_registered` | `Auth/RegisteredUserController::store` | After new user created |
| `onboarding_completed` | `OnboardingController::storeQuickStart` | After onboarding wizard finishes |
| `dashboard_viewed` | `DashboardController::index` | On every dashboard load |
| `campaign_created` | `CampaignController::store` | After campaign saved |
| `campaign_archived` | `CampaignController::archive` | After campaign soft-deleted |
| `member_created` | `MemberController::store` | After member saved |
| `member_archived` | `MemberController::archive` | After member soft-deleted |
| `reward_created` | `RewardController::store` | After reward saved |
| `reward_archived` | `RewardController::archive` | After reward soft-deleted |
| `purchase_recorded` | `PurchaseController::store` | After transaction written |
| `reward_redeemed` | `RedemptionController::store` | After redemption written |
| `settings_updated` | `SettingsController::updateProfile/Preferences` | After settings saved |
| `subscription_viewed` | `SubscriptionController::index` | On every subscription page load |
| `trial_expired` | `ProcessExpiredTrials` command | Per merchant whose trial ends |
| `feedback_submitted` | `FeedbackController::store` | After feedback JSON written |

### Page Views

| Page Name | Controller |
|---|---|
| `Dashboard` | `DashboardController::index` |
| `Members` | `MemberController::index` |
| `Campaigns` | `CampaignController::index` |
| `Settings` | `SettingsController::index` |
| `Subscription` | `SubscriptionController::index` |

---

## 5. Feedback Collection

### Flow

1. User clicks "Send Feedback" in sidebar or floating button → Bootstrap modal opens
2. User selects category, fills subject and message → submits to `POST /feedback`
3. `FeedbackController::store` validates via `FeedbackRequest`
4. Payload is written to `storage/app/feedback/YYYY-MM-DD_HHiiss_<uuid>.json`
5. `feedback_submitted` event is tracked via `AnalyticsService`
6. User sees flash success message

### Feedback Payload Schema

```json
{
  "id": "uuid-v4",
  "user_id": 1,
  "merchant_id": 1,
  "user_name": "Jane Smith",
  "user_email": "jane@example.com",
  "merchant_name": "Jane's Coffee",
  "category": "bug | feature | question | general",
  "subject": "Short description",
  "message": "Full message text",
  "current_url": "https://app.onemember.com/members",
  "browser": "Mozilla/5.0 ...",
  "app_version": "1.0",
  "app_env": "production",
  "submitted_at": "2026-06-29T10:00:00+00:00"
}
```

### Categories

| Value | Label |
|---|---|
| `bug` | Report a Bug |
| `feature` | Request a Feature |
| `question` | Question |
| `general` | General Feedback |

### Storage

Files are written to `storage/app/feedback/` using `Storage::disk('local')`. The directory is created on first write if it does not exist. A `.gitkeep` placeholder ensures the directory is committed.

To read feedback: `ls storage/app/feedback/` or open individual JSON files. No admin UI is provided in V1.0.

---

## 6. UI Components

### Sidebar Button

Located at the bottom of the sidebar navigation, above the user/logout section. Uses Bootstrap outline button style. Triggers the feedback modal via `data-bs-toggle="modal"`.

### Floating Button

A small circular button fixed to the bottom-right of the viewport on desktop only (`d-none d-md-flex`). Uses `position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 1040`. Hidden on mobile to avoid overlap with content.

### Feedback Modal

**File:** `resources/views/feedback/modal.blade.php`

Bootstrap modal with:
- Category `<select>` (4 options)
- Subject `<input>` (max 200 chars)
- Message `<textarea>` (10–5000 chars)
- Hidden fields for `current_url` and `browser` (auto-filled via JavaScript on modal open)
- Reset on `hidden.bs.modal`

---

## 7. PostHog Integration

When `ANALYTICS_PROVIDER=posthog` and `ANALYTICS_ENABLED=true`:

- Events are sent to `{POSTHOG_HOST}/capture/` via `file_get_contents()` with stream context
- HTTP method: POST
- Content-Type: `application/json`
- Timeout: `POSTHOG_TIMEOUT` seconds (default 2)
- No SDK package; no Composer dependency added

The PostHog `distinct_id` is set to `user_{userId}` or `merchant_{merchantId}` for server-side events.

---

## 8. Sentry Integration

When `SENTRY_DSN` is set and the Sentry SDK is installed:

- `AnalyticsService::exception()` checks `class_exists(\Sentry\SentrySdk::class)` before any SDK call
- If the SDK is absent, exceptions are logged locally only (no crash)
- The Sentry SDK is never listed in `composer.json` as a hard dependency for V1.0

To enable full Sentry support in production:
```bash
composer require sentry/sentry-laravel
```
Then set `SENTRY_DSN` and `SENTRY_ENVIRONMENT` in `.env`.

---

## 9. Helper Methods

### `activationMetrics(Merchant $merchant): array`

Returns:
```php
[
    'registered_at'          => Carbon|null,
    'onboarding_completed_at'=> Carbon|null,
    'first_campaign_at'      => Carbon|null,
    'first_member_at'        => Carbon|null,
    'first_purchase_at'      => Carbon|null,
    'first_redemption_at'    => Carbon|null,
    'minutes_to_onboard'     => int|null,
    'minutes_to_first_campaign' => int|null,
    'minutes_to_first_member'   => int|null,
    'is_fully_activated'     => bool,
]
```

Used to measure time-to-value and identify activation bottlenecks.

### `merchantActivityScore(Merchant $merchant): int`

Returns a 0–100 score based on:
- Members count (max 25 pts)
- Active campaigns (max 20 pts)
- Purchases in last 30 days (max 30 pts)
- Redemptions in last 30 days (max 25 pts)

Useful for identifying at-risk merchants during customer success reviews.

---

## 10. Privacy Considerations

- User email and name are included in feedback payloads stored on the local filesystem. Ensure `storage/app/feedback/` is excluded from public web access (Laravel's default storage layout handles this).
- Feedback files should be purged on a retention schedule (e.g., 90 days). No automated purge is implemented in V1.0.
- Analytics events include `user_id` and `merchant_id` but not PII such as email or phone. PostHog `identify` calls include name and email — ensure PostHog's data residency settings comply with applicable law (PDPA in Thailand).
- Sentry error payloads may include request context. Review Sentry's `before_send` hook for PII scrubbing if required by compliance.

---

## 11. Future Integrations

The `ANALYTICS_PROVIDER` config key is designed for extension. To add a new provider:

1. Add its config keys to `config/analytics.php`
2. Add a `sendToProvider()` private method in `AnalyticsService`
3. Add a branch in `dispatch()` for the new provider name
4. Update `.env.example` with the new keys
5. No other files need changing

Candidate future providers: Mixpanel, Amplitude, Plausible (self-hosted).

---

*Last updated: Sprint 5.7 — 2026-06-29*
