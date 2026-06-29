# 23 — Stripe Billing

> **Sprint:** 6.1  
> **Last updated:** 2026-06-29  
> **Decision reference:** DECISION-055  
> **Cross-reference:** [docs/08-Product-Decisions.md](08-Product-Decisions.md), [docs/12-SaaS-Architecture.md](12-SaaS-Architecture.md)

---

## 1. Architecture Overview

OneMember uses Stripe as the **sole billing source of truth**. The application does not calculate, store, or infer billing state independently of Stripe.

```
Merchant browser
    │
    ├─ POST /subscription/checkout  ──► BillingService::createCheckoutSession()
    │                                        │
    │                                   Stripe Checkout Session URL
    │                                        │
    │◄────────────────────────────── redirect to checkout.stripe.com
    │
    └─ POST /subscription/portal    ──► BillingService::createPortalSession()
                                             │
                                        Stripe Billing Portal URL
                                             │
                                        redirect to billing.stripe.com

Stripe (asynchronous)
    │
    └─ POST /stripe/webhook  ──► SubscriptionController::webhook()
                                      │
                              BillingService::handleWebhookEvent()
                                      │
                              syncFromStripeSubscription()
                                      │
                              merchants table updated (ONLY HERE)
                                      │
                              Laravel Event dispatched
```

**Key rule (DECISION-055):** `merchants.subscription_status`, `merchants.subscription_plan`, `merchants.subscription_renews_at` are only written by `BillingService::syncFromStripeSubscription()`, which is called exclusively from webhook handlers.

---

## 2. Stripe SDK Integration

**Package:** `stripe/stripe-php ^20` (installed via Composer)

`BillingService` is the only file that imports Stripe SDK classes. Controllers never reference `\Stripe\*` directly.

```php
// ✅ Correct — controller calls BillingService
$url = $billing->createCheckoutSession($merchant, $priceId);

// ❌ Wrong — controller touches Stripe SDK directly
$session = \Stripe\Checkout\Session::create([...]);
```

---

## 3. Configuration

**File:** `config/stripe.php`

| Config Key | Env Variable | Purpose |
|---|---|---|
| `stripe.publishable_key` | `STRIPE_PUBLISHABLE_KEY` | Frontend JS (Stripe.js) |
| `stripe.secret_key` | `STRIPE_SECRET_KEY` | API calls in BillingService |
| `stripe.webhook_secret` | `STRIPE_WEBHOOK_SECRET` | Webhook signature verification |
| `stripe.currency` | `STRIPE_CURRENCY` | ISO 4217 code, default `thb` |
| `stripe.prices.starter.monthly` | `STRIPE_PRICE_STARTER_MONTHLY` | Stripe Price ID |
| `stripe.prices.professional.monthly` | `STRIPE_PRICE_PROFESSIONAL_MONTHLY` | Stripe Price ID |
| `stripe.prices.enterprise.monthly` | `STRIPE_PRICE_ENTERPRISE_MONTHLY` | Stripe Price ID |

### Setting Up Price IDs

1. Log in to [Stripe Dashboard](https://dashboard.stripe.com) (test mode first)
2. Go to **Products** → **Add product**
3. Create a product for each plan (Starter, Professional, Enterprise)
4. Add a monthly recurring price for each
5. Copy the `price_XXXX` IDs into `.env`

Price IDs differ between test and live mode. Use test mode IDs in development.

---

## 4. Database Fields

Migration: `2026_06_29_100001_add_stripe_fields_to_merchants_table.php`

New columns on `merchants` table:

| Column | Type | Nullable | Purpose |
|---|---|---|---|
| `stripe_customer_id` | varchar | ✅ | Stripe `cus_XXXX` identifier |
| `stripe_subscription_id` | varchar | ✅ | Stripe `sub_XXXX` identifier |
| `stripe_price_id` | varchar | ✅ | Current Stripe Price ID |
| `billing_email` | varchar | ✅ | Email on the Stripe customer object |
| `subscription_renews_at` | timestamp | ✅ | Next billing date (from Stripe) |
| `cancel_at_period_end` | boolean | ❌ | Cancellation scheduled (default false) |

Existing columns retained (not replaced):

| Column | Purpose |
|---|---|
| `subscription_plan` | Plan enum (free/starter/professional/enterprise) |
| `subscription_status` | Status enum (trial/active/expired/cancelled) |
| `trial_ends_at` | Trial expiry date |

---

## 5. Checkout Flow

### New Subscription (Trial → Paid)

```
1. Merchant clicks "Subscribe to Starter" on /subscription
2. POST /subscription/checkout { price_id: "price_starter_monthly" }
3. BillingService::createOrGetCustomer() — creates Stripe Customer if none exists
4. BillingService::createCheckoutSession() — creates Stripe Checkout Session
5. Redirect to checkout.stripe.com
6. Merchant enters card details
7. Stripe processes payment
8. Stripe sends checkout.session.completed webhook
9. BillingService::handleCheckoutSessionCompleted() syncs merchant
10. Stripe sends customer.subscription.created webhook (also synced)
11. Merchant redirected to /subscription/success
```

### Upgrade (e.g. Starter → Professional)

```
1. Merchant clicks "Upgrade to Professional" on /subscription
2. POST /subscription/upgrade { price_id: "price_professional_monthly" }
3. If stripe_subscription_id exists: BillingService::swapPlan()
   - Updates Stripe subscription items with proration
   - Stripe sends customer.subscription.updated webhook
   - Webhook syncs merchant
4. If no stripe_subscription_id: same as new subscription (checkout)
```

### Downgrade (e.g. Professional → Starter)

Same as upgrade but uses `POST /subscription/downgrade`. Stripe handles prorations.

---

## 6. Billing Portal Flow

```
1. Merchant clicks "Manage Billing" on /subscription
2. POST /subscription/portal
3. BillingService::createPortalSession()
4. Redirect to billing.stripe.com/session/XXX
5. Merchant can:
   - Update payment method
   - Download invoices
   - Cancel subscription
   - View billing history
6. Return URL: /subscription
```

The portal is a Stripe-hosted page. No custom UI is required for card management.

---

## 7. Cancellation Flow

### Via App UI

```
1. Merchant clicks "Cancel Subscription"
2. POST /subscription/cancel
3. BillingService::cancelSubscription()
   - Sets cancel_at_period_end = true on Stripe subscription
   - Sets merchants.cancel_at_period_end = true locally (immediate UI feedback)
4. Stripe sends customer.subscription.updated webhook
5. syncFromStripeSubscription() confirms the cancel_at_period_end state
6. Merchant keeps access until subscription_renews_at
```

### Via Billing Portal

The merchant can also cancel through the Stripe Billing Portal. The `customer.subscription.updated` webhook fires and syncs the database identically.

### Resumption

```
1. Merchant clicks "Resume Subscription"
2. POST /subscription/resume (only allowed when cancel_at_period_end = true)
3. BillingService::resumeSubscription()
   - Sets cancel_at_period_end = false on Stripe subscription
   - Sets merchants.cancel_at_period_end = false locally
4. customer.subscription.updated webhook confirms
```

---

## 8. Webhook Handling

### Route

```
POST /stripe/webhook
```

- **No auth middleware** — Stripe sends unauthenticated requests
- **CSRF-exempt** — registered in `bootstrap/app.php` via `validateCsrfTokens(except: ['stripe/webhook'])`
- **Signature-verified** — every request verified with `Stripe\Webhook::constructEvent()`

### Event Processing

| Stripe Event | Handler | Action |
|---|---|---|
| `checkout.session.completed` | `handleCheckoutSessionCompleted()` | Retrieve subscription, sync, dispatch `SubscriptionPurchased` |
| `customer.subscription.created` | `handleSubscriptionCreated()` | Retrieve subscription, sync |
| `customer.subscription.updated` | `handleSubscriptionUpdated()` | Retrieve subscription, sync |
| `customer.subscription.deleted` | `handleSubscriptionDeleted()` | Set status=Cancelled, plan=Free, dispatch `SubscriptionCancelled` |
| `invoice.paid` | `handleInvoicePaid()` | Sync subscription, dispatch `SubscriptionRenewed` |
| `invoice.payment_failed` | `handleInvoicePaymentFailed()` | Dispatch `PaymentFailed` |
| `customer.subscription.trial_will_end` | `handleTrialWillEnd()` | Dispatch `TrialEnding` |

### Idempotency

Processed event IDs are stored in Laravel's cache for 24 hours:

```php
Cache::put('stripe_event_' . $event->id, true, now()->addHours(24));
```

If Stripe retries delivery of an event that has already been processed, the handler returns immediately without re-processing.

### Signature Verification

```php
$event = Webhook::constructEvent($payload, $signatureHeader, config('stripe.webhook_secret'));
```

If the signature is invalid, `SignatureVerificationException` is thrown. The controller catches this and returns HTTP 400. All other exceptions return HTTP 200 (to prevent Stripe from retrying server errors unrelated to the event content).

### Merchant Lookup Strategy

When a webhook arrives, the merchant is resolved in this order:

1. `metadata.merchant_id` on the Stripe object (most reliable)
2. `merchants.stripe_subscription_id` match
3. `merchants.stripe_customer_id` match

Metadata is populated at checkout session creation time.

---

## 9. Billing Events (Laravel)

These events are dispatched from `BillingService` for downstream consumption. No listeners are registered in Sprint 6.1 — a future email sprint will add listener classes.

| Event | When | Key Properties |
|---|---|---|
| `App\Events\SubscriptionPurchased` | After checkout completes | `merchant`, `planKey`, `stripePriceId`, `stripeSubscriptionId` |
| `App\Events\SubscriptionCancelled` | After subscription deleted | `merchant`, `cancelAtPeriodEnd` |
| `App\Events\SubscriptionRenewed` | After invoice paid | `merchant`, `planKey`, `renewsAt` |
| `App\Events\PaymentFailed` | After invoice payment failed | `merchant`, `invoiceId`, `amountDue` |
| `App\Events\TrialEnding` | 3 days before trial end (Stripe-triggered) | `merchant`, `daysRemaining` |

---

## 10. Analytics Events

Tracked via `AnalyticsService` in `SubscriptionController`:

| Event Name | When |
|---|---|
| `checkout_started` | Merchant initiates Stripe Checkout |
| `checkout_completed` | Merchant reaches `/subscription/success` |
| `portal_opened` | Merchant opens Billing Portal |
| `subscription_upgraded` | `swapPlan()` called for a higher plan |
| `subscription_downgraded` | `swapPlan()` called for a lower plan |
| `subscription_cancelled` | `cancelSubscription()` called |
| `subscription_resumed` | `resumeSubscription()` called |

---

## 11. Security

- **No card data** — Stripe Checkout and Portal handle all payment collection. OneMember never receives or stores card numbers. PCI scope is Stripe's responsibility.
- **Webhook signatures** — all inbound webhooks verified with HMAC-SHA256 before any processing
- **Secret keys** — never exposed to browser; `STRIPE_SECRET_KEY` is server-side only; `STRIPE_PUBLISHABLE_KEY` is safe to expose
- **Merchant isolation** — all billing routes check `$merchant = $request->user()->merchant` before calling BillingService; `abort_unless($merchant, 403)` prevents cross-merchant access
- **Price ID validation** — `checkout()` and `upgrade()` run the submitted `price_id` through `priceIdToPlanKey()` and reject any that map to `free` (unrecognised IDs)

---

## 12. Failure Recovery

### Webhook Delivery Failures

Stripe retries failed webhooks up to 72 hours with exponential backoff. Because webhook handlers are idempotent (cache guard + Eloquent `update()`), retries are safe.

### Subscription Sync Drift

If the application database gets out of sync with Stripe:

1. Go to Stripe Dashboard → Developer → Webhooks
2. Find the relevant event and click **Resend**
3. The handler will re-sync the merchant

Or call `BillingService::syncFromStripeSubscription($merchant, $subscription)` from a Tinker session:

```php
$merchant = Merchant::find(1);
$sub = \Stripe\Subscription::retrieve($merchant->stripe_subscription_id);
app(\App\Services\BillingService::class)->syncFromStripeSubscription($merchant, $sub);
```

### Trial-to-Paid Conversion Gap

If a merchant's trial expires before they subscribe, `ProcessExpiredTrials` command downgrades them to Free. When they later subscribe via Stripe Checkout, the webhook sets `subscription_status = Active` and `subscription_plan` to the purchased plan, overriding the Free state correctly.

---

## 13. Production Setup Checklist

- [ ] **Stripe account** created at [dashboard.stripe.com](https://dashboard.stripe.com)
- [ ] **Test mode** verified — complete a checkout flow with test card `4242 4242 4242 4242`
- [ ] **Products created** — Starter, Professional, Enterprise (monthly recurring prices)
- [ ] **Price IDs** copied to `.env` for production environment
- [ ] **Webhook endpoint** registered in Stripe Dashboard:
  - URL: `https://yourdomain.com/stripe/webhook`
  - Events to listen for (all 7 listed in section 8)
  - Signing secret copied to `STRIPE_WEBHOOK_SECRET`
- [ ] **Billing Portal** configured in Stripe Dashboard → Settings → Billing → Customer portal
  - Enable: Update payment methods, View invoices, Cancel subscriptions
  - Return URL: `https://yourdomain.com/subscription`
- [ ] **Live mode** keys and price IDs set in production `.env` (separate from test keys)
- [ ] **Webhook signature** tested in production with Stripe CLI or a real checkout

### Test Cards (Stripe Test Mode)

| Card Number | Behaviour |
|---|---|
| `4242 4242 4242 4242` | Successful payment |
| `4000 0000 0000 9995` | Card declined |
| `4000 0025 0000 3155` | 3D Secure required |

Use any future expiry date, any 3-digit CVC, any postal code.

---

## 14. Local Development

Install the Stripe CLI to test webhooks locally:

```bash
# Install Stripe CLI (macOS)
brew install stripe/stripe-cli/stripe

# Authenticate
stripe login

# Forward webhooks to local server
stripe listen --forward-to localhost:8000/stripe/webhook

# The CLI will output a signing secret — use it as STRIPE_WEBHOOK_SECRET in .env
```

---

*Last updated: Sprint 6.1 — 2026-06-29*
