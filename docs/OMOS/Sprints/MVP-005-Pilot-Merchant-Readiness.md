# Sprint Spec: MVP-005 — Pilot Merchant Readiness

| Field | Value |
|---|---|
| **Sprint ID** | MVP-005 |
| **Title** | Pilot Merchant Readiness |
| **Type** | Feature + Documentation |
| **Classification** | Type A — Autonomous |
| **Priority** | 🔴 Critical |
| **Status** | ✅ Ready |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Spec Written** | 2026-07-03 |
| **Approved** | 2026-07-03 |

---

## Business Outcome

> OneMember is ready to onboard the first 10 pilot merchants with confidence.

---

## Background

Three gaps were found during the MVP-004 codebase audit that would affect pilot merchant experience:

**Gap 1 — Trial-ending reminder never sends for trial-only merchants.**
`TrialEnding` event, `handleTrialEnding` listener, and `TrialEndingReminderEmail` all exist and are wired. However the event is only dispatched by `BillingService::handleTrialWillEnd()`, which is called from a Stripe webhook (`customer.subscription.trial_will_end`). Pilot merchants who start a 30-day trial without adding payment details never trigger a Stripe subscription, so this webhook never fires and the reminder email is never sent.

Fix: a scheduled Artisan command that dispatches `TrialEnding` for any merchant whose trial ends within 7 days and who hasn't received a reminder yet.

**Gap 2 — No production operations checklist.**
There is no document specifying what must be running in production before the first merchant is onboarded: queue workers, cron, ENV vars, Stripe configuration, mail provider. This creates risk that a configuration gap is missed at launch time.

Fix: `docs/OMOS/Pilot-Readiness-Checklist.md`

**Gap 3 — Product-State.md production readiness table is inaccurate.**
Several items marked ❌ have since been resolved (birthday automation, expiry). The table should reflect current reality for a confident pilot launch decision.

Fix: update production readiness table in `Product-State.md`.

---

## Classification Rationale — Type A

- No schema changes
- No architecture changes
- No authentication or authorisation changes
- No payment model changes
- The trial-ending reminder wires three already-existing components (`TrialEnding` event → listener → email) to a scheduler trigger — completing an incomplete flow, not building a new capability
- Documentation and ops checklist only affect docs

---

## Tasks

### Task 1 — `SendTrialEndingReminders` Artisan command

**File:** `app/Console/Commands/SendTrialEndingReminders.php`
**Signature:** `subscriptions:send-trial-ending-reminders`
**Description:** `Send trial-ending reminder emails to merchants approaching the end of their trial.`

**Logic:**

```
find all merchants where:
  subscription_status = Trial
  trial_ends_at BETWEEN now() AND now()->addDays(7)
  settings['trial_reminder_sent'] is not true

for each:
  dispatch TrialEnding::dispatch($merchant, $daysRemaining)
  update merchant settings: settings['trial_reminder_sent'] = true
  log: "Trial ending reminder sent: {merchant.name}, {daysRemaining} days remaining"
```

`daysRemaining` = `(int) ceil(now()->diffInHours($merchant->trial_ends_at) / 24)`

The `settings['trial_reminder_sent']` flag prevents re-sending if the command runs daily. It is set to `true` in the merchant's JSON settings column (no migration needed — JSON column already exists).

The command must be **idempotent**: running twice on the same day must not send two emails.

**Schedule:** `routes/console.php` — `dailyAt('09:00')`

---

### Task 2 — Pilot Readiness Checklist document

**File:** `docs/OMOS/Pilot-Readiness-Checklist.md`

A production operations checklist covering:

- **Infrastructure** — queue worker running, cron (`schedule:run`) installed, storage writable
- **Environment variables** — all required `.env` keys populated (mail, Stripe, app URL, etc.)
- **Mail** — `MAIL_MAILER=resend`, `RESEND_API_KEY` set, `noreply@onemember.app` domain verified in Resend
- **Queue** — `QUEUE_CONNECTION=database`, queue worker started (`php artisan queue:work`)
- **Stripe** — `STRIPE_PUBLISHABLE_KEY`, `STRIPE_SECRET_KEY`, `STRIPE_WEBHOOK_SECRET` set; webhook endpoint registered in Stripe dashboard pointing to `/stripe/webhook`
- **Scheduled commands** — `ProcessExpiredTrials` (01:00), `ProcessPointExpiry` (02:00), `ProcessBirthdayRewards` (08:00), `SendTrialEndingReminders` (09:00), `VerifyDatabaseBackup` (03:00)
- **Smoke test** — walkthrough of the pilot merchant journey end to end
- **Known limitations for pilot** — what isn't available yet (member notification emails, counter mode, win-back alerts)

---

### Task 3 — Update Product-State.md production readiness table

Update to reflect current reality post-MVP-004:

| Item | Before | After |
|---|---|---|
| Birthday automation live | ❌ | ✅ |
| Point expiry processing | ❌ | ✅ |
| Member notification emails | ❌ | ❌ (still not built — document as known limitation) |
| Trial-ending reminder emails | ❌ (implicit) | ✅ (after Task 1) |

---

### Task 4 — Tests

**File:** `tests/Feature/SendTrialEndingRemindersTest.php`

- `test_reminder_sent_when_trial_ends_within_7_days` — merchant trial ends in 5 days, expect `TrialEnding` dispatched
- `test_reminder_not_sent_when_trial_ends_after_7_days` — trial ends in 10 days, expect no dispatch
- `test_reminder_not_sent_twice` — reminder already sent (`trial_reminder_sent = true`), expect no second dispatch
- `test_reminder_not_sent_when_subscription_is_not_trial` — subscription_status is Active, expect no dispatch
- `test_reminder_marks_merchant_as_reminded` — after command runs, merchant settings has `trial_reminder_sent = true`
- `test_days_remaining_is_correct` — verify computed `daysRemaining` passed to event

---

## Acceptance Criteria

| # | Criterion |
|---|---|
| AC-1 | `php artisan subscriptions:send-trial-ending-reminders` runs without error |
| AC-2 | `TrialEnding` is dispatched for merchants with trial ending within 7 days |
| AC-3 | Command is idempotent — does not send twice to the same merchant |
| AC-4 | Command is scheduled at 09:00 in `routes/console.php` |
| AC-5 | `docs/OMOS/Pilot-Readiness-Checklist.md` exists and is complete |
| AC-6 | `Product-State.md` production readiness table is accurate |
| AC-7 | `php artisan test` — zero failures |

---

## Commit Message

```
Sprint MVP-005 — Pilot Merchant Readiness

- SendTrialEndingReminders: daily command, dispatches TrialEnding for non-Stripe trial merchants
- Pilot-Readiness-Checklist.md: production ops checklist for first merchant onboarding
- Product-State.md: production readiness table updated to reflect post-MVP-004 state
- SendTrialEndingRemindersTest: 6 tests

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

---

## Related Documents

- [EXECUTE.md](../EXECUTE.md)
- [Sprint-Classification.md](../Sprint-Classification.md)
- [Product-State.md](../Product-State.md)
- `app/Events/TrialEnding.php`
- `app/Listeners/EmailEventSubscriber.php@handleTrialEnding`
- `app/Mail/TrialEndingReminderEmail.php`
- `app/Console/Commands/ProcessExpiredTrials.php` — scheduling pattern
