# 24 — Production Email Infrastructure

> **Sprint:** 6.2  
> **Last updated:** 2026-06-29  
> **Decision reference:** DECISION-056  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/23-Stripe-Billing.md](23-Stripe-Billing.md)

---

## 1. Architecture Overview

All transactional email in OneMember is **event-driven and queued**. Controllers never send email directly. The flow is:

```
Controller or Service
    │
    └─ dispatch(SomeEvent)
            │
            └─ EmailEventSubscriber::handleSomeEvent()
                    │
                    └─ Mail::to($recipient)->queue(new SomeMailable(...))
                            │
                            └─ Queue worker processes the queued job
                                    │
                                    └─ Email delivered via configured provider
```

**Key rules (DECISION-056):**
- No `Mail::send()` or `Mail::to()->send()` anywhere in the codebase
- All email dispatch happens in `EmailEventSubscriber`
- Every Mailable implements `ShouldQueue`
- All emails are localized (EN + TH)
- Notification preferences are checked before dispatch

---

## 2. Configuration

**File:** `config/email.php`

| Config Key | Env Variable | Purpose |
|---|---|---|
| `email.provider` | `MAIL_MAILER` | Mail driver (`ses`, `mailgun`, `smtp`, etc.) |
| `email.from_name` | `MAIL_FROM_NAME` | Default sender name |
| `email.from_address` | `MAIL_FROM_ADDRESS` | Default sender address |
| `email.reply_to` | `MAIL_REPLY_TO` | Reply-to address |
| `email.support_email` | `SUPPORT_EMAIL` | Support team address (feedback notifications) |
| `email.logo_url` | `EMAIL_LOGO_URL` | Brand logo in emails (optional) |
| `email.company_name` | `EMAIL_COMPANY_NAME` | Company name in email footers |
| `email.frontend_url` | `APP_URL` | Base URL for CTA buttons |
| `email.footer_text` | `EMAIL_FOOTER_TEXT` | Footer copyright line |
| `email.social.twitter` | `EMAIL_SOCIAL_TWITTER` | Social link (optional) |
| `email.social.facebook` | `EMAIL_SOCIAL_FACEBOOK` | Social link (optional) |
| `email.social.linkedin` | `EMAIL_SOCIAL_LINKEDIN` | Social link (optional) |

### Recommended Provider: Amazon SES

**Why SES:**
- Low cost at scale (< $0.10 per 1,000 emails)
- High deliverability with proper DKIM/SPF setup
- Native Laravel Mail driver support
- Same AWS account as S3 (if used)

**Setup:**
1. Configure SES in AWS Console
2. Verify your sender domain
3. Set `MAIL_MAILER=ses` in `.env`
4. Configure AWS credentials (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`)

**Other supported providers:** Mailgun, Postmark, Resend, SMTP — all work via Laravel Mail without code changes.

---

## 3. Mailables

**Directory:** `app/Mail/`

| Mailable | Trigger Event | Recipient | Can be disabled? |
|---|---|---|---|
| `WelcomeEmail` | `Registered` | New user | No (security alert) |
| `EmailVerifiedEmail` | `Verified` | Verified user | No (security alert) |
| `TrialStartedEmail` | `TrialStarted` | Merchant owner | Yes (product_updates) |
| `TrialEndingReminderEmail` | `TrialEnding` | Merchant owner | No (billing) |
| `SubscriptionPurchasedEmail` | `SubscriptionPurchased` | Merchant owner | No (billing) |
| `SubscriptionRenewedEmail` | `SubscriptionRenewed` | Merchant owner | No (billing) |
| `SubscriptionCancelledEmail` | `SubscriptionCancelled` | Merchant owner | No (billing) |
| `PaymentFailedEmail` | `PaymentFailed` | Merchant owner | No (billing) |
| `PasswordChangedEmail` | `PasswordChanged` | User | No (security alert) |
| `FeedbackReceivedEmail` | `FeedbackSubmitted` | User + Support | No (direct response) |

All mailables use Laravel Markdown mail templates in `resources/views/emails/`.

---

## 4. Events

### New Events (Sprint 6.2)

| Event | Dispatched From | Carries |
|---|---|---|
| `App\Events\TrialStarted` | `OnboardingController::storeQuickStart()` | `Merchant` |
| `App\Events\PasswordChanged` | `Auth\PasswordController::update()` | `User` |
| `App\Events\FeedbackSubmitted` | `FeedbackController::store()` | `User`, `?Merchant`, `array $feedback` |
| `App\Events\EmailSending` | `EmailEventSubscriber::send()` | template, recipient, merchantId |
| `App\Events\EmailSent` | `EmailEventSubscriber::send()` | template, recipient, merchantId |
| `App\Events\EmailFailed` | `EmailEventSubscriber::send()` | template, recipient, reason, merchantId |

### Existing Billing Events (Sprint 6.1)

`SubscriptionPurchased`, `SubscriptionRenewed`, `SubscriptionCancelled`, `PaymentFailed`, `TrialEnding` — dispatched from `BillingService`.

### Auth Events (Laravel)

`Registered`, `Verified` — dispatched by Laravel's built-in auth system.

---

## 5. EmailEventSubscriber

**File:** `app/Listeners/EmailEventSubscriber.php`

Auto-discovered by Laravel 13. Subscribes to all 10 event types. No manual registration needed.

**Key behaviour:**
1. Checks notification preferences via `$merchant->wantsEmail($category)` before sending optional emails
2. Loads merchant's `owner` relation to get the email address
3. Calls `EmailLogger::sending()` before dispatch, `EmailLogger::sent()` on success, `EmailLogger::failed()` on exception
4. Dispatches `EmailSent` or `EmailFailed` delivery status events
5. FeedbackSubmitted sends TWO emails: thank-you to user + support notification to `config('email.support_email')`

---

## 6. Email Logging

**File:** `app/Services/EmailLogger.php`  
**Log file:** `storage/logs/email.log` (daily rotation, 90 days)

Email addresses are masked in logs: `j***n@example.com`.

**Log entries:**

| Method | Level | When |
|---|---|---|
| `sending()` | info | Before `Mail::to()->queue()` is called |
| `sent()` | info | After successful queue submission |
| `failed()` | error | When an exception is caught |

**Note:** `EmailLogger` does NOT duplicate `SecurityLogger`. Password changes and registrations are logged by both — security-logger for the security audit trail, email-logger for email delivery tracking.

---

## 7. Notification Preferences

**Storage:** `merchants.settings['email_notifications']` (JSON column, no migration needed)

| Preference Key | Can be disabled | Default | Controls |
|---|---|---|---|
| `product_updates` | ✅ | true | TrialStartedEmail |
| `tips` | ✅ | true | (future tips emails) |
| `feature_announcements` | ✅ | true | (future announcement emails) |
| `billing` | ❌ | always on | All billing receipts |
| `security_alerts` | ❌ | always on | Password changed, Welcome, Email verified |

**Model helper:** `Merchant::wantsEmail(string $category): bool`

Preferences are managed in **Settings → Preferences tab** under "Email Notifications".

---

## 8. Queue Configuration

All emails are queued via `Mail::to($email)->queue($mailable)`. This requires an active queue worker.

```bash
# Local development
php artisan queue:work

# Production (with supervisor)
php artisan queue:work --tries=3 --timeout=60
```

**Queue connection:** `QUEUE_CONNECTION=database` (set in `.env`).

Failed jobs can be retried via:
```bash
php artisan queue:retry all
```

---

## 9. Localization

Two translation files provide EN + TH support:
- `lang/en/email.php` — 46 keys
- `lang/th/email.php` — 46 keys (identical key set)

The test `test_email_translations_have_identical_keys_in_en_and_th` enforces key parity.

Email subjects and body text use the Laravel translation system (`__('email.key', [...])`). The locale is determined by the app locale at the time of rendering (set per-merchant via `SetLocale` middleware).

---

## 10. Testing

**File:** `tests/Feature/EmailInfrastructureTest.php` (24 tests)

Uses `Mail::fake()` for all tests — no live emails are sent. Key test groups:

| Test | What it verifies |
|---|---|
| `test_all_mailables_implement_should_queue` | ShouldQueue interface |
| `test_registered_event_queues_welcome_email` | WelcomeEmail via event |
| `test_trial_started_skipped_when_product_updates_disabled` | Preference respecting |
| `test_feedback_submitted_queues_thankyou_and_support_emails` | Two-email feedback flow |
| `test_email_translations_have_identical_keys_in_en_and_th` | EN/TH parity |
| `test_feedback_controller_dispatches_feedback_submitted_event` | Controller dispatches event |
| `test_password_controller_dispatches_password_changed_event` | Controller dispatches event |
| `test_preferences_update_stores_email_notifications` | Settings form saves prefs |

---

## 11. Production Checklist

- [ ] **Email provider** configured (`MAIL_MAILER`, credentials in `.env`)
- [ ] **Sender domain** verified and DKIM/SPF configured
- [ ] **From address** set (`MAIL_FROM_ADDRESS`)
- [ ] **Support email** set (`SUPPORT_EMAIL`)
- [ ] **Queue worker** running in production (supervisor recommended)
- [ ] **Failed job monitoring** configured (queue:retry or Horizon)
- [ ] `storage/logs/email.log` accessible and writable
- [ ] Test email flow end-to-end in staging before production deploy

---

*Last updated: Sprint 6.2 — 2026-06-29*
