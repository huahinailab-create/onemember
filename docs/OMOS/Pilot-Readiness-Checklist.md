# Pilot Readiness Checklist — First 10 Merchants

| Field | Value |
|---|---|
| **Document Owner** | Product Owner |
| **Version** | 1.0.0 |
| **Status** | Active |
| **Created** | 2026-07-03 |
| **Sprint** | MVP-005 |

---

## Purpose

This checklist must be completed before onboarding any pilot merchant. It covers infrastructure, environment, email, billing, and smoke testing. Every item must pass before a merchant is invited.

---

## 1 — Infrastructure

| # | Check | Command / Location | Status |
|---|---|---|---|
| 1.1 | Server is running PHP 8.3+ | `php -v` | ☐ |
| 1.2 | Laravel queue worker is running | `php artisan queue:work --daemon` (or Supervisor) | ☐ |
| 1.3 | Cron is installed for `schedule:run` | `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1` | ☐ |
| 1.4 | `storage/` and `bootstrap/cache/` are writable | `php artisan storage:link` | ☐ |
| 1.5 | SQLite / MySQL database is accessible | `php artisan migrate:status` | ☐ |
| 1.6 | All migrations are up to date | `php artisan migrate:status` — all `Ran` | ☐ |

---

## 2 — Environment Variables

All variables below must be set in the server's `.env` or Forge environment panel.

| Variable | Required | Notes |
|---|---|---|
| `APP_NAME` | ✅ | `OneMember` |
| `APP_ENV` | ✅ | `production` |
| `APP_KEY` | ✅ | Generate: `php artisan key:generate` |
| `APP_DEBUG` | ✅ | `false` in production |
| `APP_URL` | ✅ | Full URL including https |
| `APP_LOCALE` | ✅ | `th` |
| `DB_CONNECTION` | ✅ | `sqlite` or `mysql` |
| `SESSION_DRIVER` | ✅ | `database` |
| `SESSION_SECURE_COOKIE` | ✅ | `true` (HTTPS required) |
| `QUEUE_CONNECTION` | ✅ | `database` |
| `CACHE_STORE` | ✅ | `database` |
| `MAIL_MAILER` | ✅ | `resend` |
| `RESEND_API_KEY` | ✅ | From resend.com/api-keys |
| `MAIL_FROM_ADDRESS` | ✅ | `noreply@onemember.app` |
| `SUPPORT_EMAIL` | ✅ | `support@onemember.app` |
| `STRIPE_PUBLISHABLE_KEY` | ✅ | From Stripe dashboard |
| `STRIPE_SECRET_KEY` | ✅ | From Stripe dashboard |
| `STRIPE_WEBHOOK_SECRET` | ✅ | From Stripe webhook endpoint |
| `STRIPE_CURRENCY` | ✅ | `thb` |
| `STRIPE_PRICE_PROFESSIONAL_MONTHLY` | ✅ | Must match live Stripe price ID |
| `DEV_TOOLS_ENABLED` | ✅ | `false` — never true in production |
| `BACKUP_PATH` | Recommended | Directory for DB backup verification |

---

## 3 — Email (Resend)

| # | Check | How to Verify |
|---|---|---|
| 3.1 | `noreply@onemember.app` domain is verified in Resend | Resend dashboard → Domains |
| 3.2 | SPF and DKIM records are published in DNS | Resend dashboard → Domains → DNS status |
| 3.3 | Test email sends successfully | `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('your@email.com')->subject('Test'))` |
| 3.4 | Queue worker processes the test email | Check `php artisan queue:monitor` — job completed |

---

## 4 — Stripe

| # | Check | How to Verify |
|---|---|---|
| 4.1 | Stripe webhook endpoint registered | Stripe Dashboard → Developers → Webhooks → `https://yourdomain.com/stripe/webhook` |
| 4.2 | Webhook events selected | `customer.subscription.*`, `invoice.*`, `payment_intent.*` |
| 4.3 | `STRIPE_WEBHOOK_SECRET` matches the signing secret shown in Stripe dashboard | Must match `whsec_...` value |
| 4.4 | Test webhook delivery | Stripe CLI: `stripe trigger customer.subscription.created` |

---

## 5 — Scheduled Commands

Verify all 5 commands run without error and are scheduled correctly.

| Command | Schedule | Test |
|---|---|---|
| `subscriptions:process-expired-trials` | 01:00 daily | `php artisan subscriptions:process-expired-trials` |
| `loyalty:process-point-expiry` | 02:00 daily | `php artisan loyalty:process-point-expiry` |
| `backup:verify` | 03:00 daily | `php artisan backup:verify` |
| `loyalty:process-birthday-rewards` | 08:00 daily | `php artisan loyalty:process-birthday-rewards` |
| `subscriptions:send-trial-ending-reminders` | 09:00 daily | `php artisan subscriptions:send-trial-ending-reminders` |

Run `php artisan schedule:list` to confirm all are registered.

---

## 6 — Smoke Test — Full Merchant Journey

Complete this walkthrough in production (or staging) before inviting any pilot merchant.

| Step | Action | Expected Result |
|---|---|---|
| 6.1 | Visit `https://yourdomain.com` | Landing page loads with navy hero, OneMember brand logo |
| 6.2 | Click "Start Free Trial" | Register page loads with 30-day trial strip |
| 6.3 | Register a new account | Welcome email received; redirected to email verification page |
| 6.4 | Verify email | Email verified confirmation email received; redirected to onboarding |
| 6.5 | Complete onboarding — business info | Step 2 of 6 loads |
| 6.6 | Complete business settings — select Thai locale | Step 3 of 6 loads |
| 6.7 | Choose loyalty type — Points | Step 4 of 6 loads |
| 6.8 | Accept starter campaign creation | Campaign created in Thai; step 5 loads |
| 6.9 | Finish onboarding | Trial-started confirmation panel shows with end date; TrialStarted email queued |
| 6.10 | Dashboard loads | No errors; campaign visible; member count 0 |
| 6.11 | Create a member | Member created; portal link available |
| 6.12 | Visit member portal URL | Portal loads in Thai; member sees loyalty card |
| 6.13 | Record a purchase for the member | Points awarded; transaction appears in history |
| 6.14 | Check queue | `php artisan queue:monitor` — no failed jobs |

---

## 7 — Known Limitations for Pilot Merchants

Brief pilot merchants on these limitations before they go live.

| Limitation | Impact | Planned Sprint |
|---|---|---|
| Members receive no email notifications (points earned, rewards available, birthday greetings) | Members must visit the portal to see their balance | TBD |
| No Counter Mode UI (simplified staff sale-recording view) | Staff must use the full merchant dashboard to record purchases | TBD |
| No win-back campaign alerts | Merchants cannot see at-risk inactive members via automated alerts | TBD |
| Trial-ending reminder sends once at ≤ 7 days (not at 7 and 3 days) | One reminder only | MVP-006 or future |

---

## 8 — Pre-Onboarding Sign-Off

| Reviewer | Role | Date | Sign-off |
|---|---|---|---|
| | Product Owner | | ☐ |
| | AI CTO | | ☐ |

All items in sections 1–6 must be checked before sign-off. Known limitations in section 7 must be communicated to pilot merchants.
