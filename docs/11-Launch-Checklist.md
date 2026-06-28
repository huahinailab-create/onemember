# 11 — Launch Checklist

> **Last updated:** 2026-06-28  
> **Purpose:** Pre-launch verification checklist for OneMember V1.0  
> **Owner:** Product Owner (Huahin) + CTO (Solution Architect)

---

## How to Use This Checklist

Work through each section in order before declaring the product launch-ready. Each item must be checked off by a named person. Items marked **BLOCKING** must be completed before launch. Items marked **NICE** are desirable but not launch-blocking.

---

## 1. Feature Completeness

### 1.1 Core Loyalty Engine
- [ ] Member CRUD (list, add, view, edit, archive) — **DONE in Sprint 3**
- [ ] Record Purchase → points/stamps issued correctly
- [ ] Reward Redemption → immediate, immutable transaction recorded
- [ ] Points expiry enforced (if configured)
- [ ] Birthday reward delivery triggered on member birthday date
- [ ] Manual point adjustment (merchant override)
- [ ] Member search and filter on list page

### 1.2 Campaign Management
- [ ] Points campaign create/configure/pause/archive
- [ ] Stamps campaign create/configure/pause/archive
- [ ] Campaign status enforced on earn (paused campaigns do not issue points)
- [ ] Business type gate — campaign type available based on merchant's business_type (DECISION-025)

### 1.3 Reward Management
- [ ] Reward create/edit/archive per campaign
- [ ] Reward quantity limit enforced at redemption
- [ ] Global Rewards view (sidebar "Rewards" — currently "coming soon")
- [ ] Birthday reward configuration and delivery

### 1.4 Merchant Settings
- [ ] Business Profile — all 12 fields save correctly
- [ ] Business Preferences — currency, timezone, date format, expiry, birthday defaults
- [ ] Password change with audit timestamp
- [ ] Logo/branding upload (logo_path column exists — **upload UI not yet built**)
- [ ] Email change flow (with re-verification)

### 1.5 Dashboard & Reporting
- [ ] Dashboard KPIs accurate (spot-check against database)
- [ ] Reports module — at minimum: member count over time, revenue attributed, redemption rate

### 1.6 Billing & Subscriptions — **BLOCKING**
- [ ] Stripe integration complete
- [ ] Trial-to-paid conversion flow
- [ ] Plan limits enforced (member cap, campaign cap)
- [ ] Billing portal accessible from Account tab
- [ ] Cancellation flow

### 1.7 Notifications
- [ ] Welcome email on merchant registration
- [ ] Password reset email (Laravel Breeze — verify Mailer config)
- [ ] Reward redemption confirmation email to merchant
- [ ] Birthday reward trigger email (requires scheduler)

### 1.8 Staff Accounts
- [ ] Staff can be invited to a merchant account (NICE for V1.0)
- [ ] Staff permissions limited (cannot access billing/settings)

---

## 2. Security

- [ ] **BLOCKING** — OWASP Top 10 review completed
- [ ] All routes behind `auth` middleware (no unauthenticated data access)
- [ ] All controller actions check merchant ownership (`abort_unless`)
- [ ] CSRF tokens on all POST/PUT/DELETE forms
- [ ] SQL injection: Eloquent ORM used throughout (no raw queries with user input)
- [ ] XSS: Blade `{{ }}` escaping used everywhere (no `{!! !!}` without sanitisation)
- [ ] File upload validation (MIME type, size limit) for logo upload
- [ ] Password requirements enforced (`Password::defaults()` — Sprint 5.1)
- [ ] Rate limiting on login and password reset routes
- [ ] Sensitive data not logged (passwords, payment data)
- [ ] `.env` not committed to version control
- [ ] `APP_DEBUG=false` in production

---

## 3. Performance

- [ ] Dashboard queries optimised (eager loading in place — Sprint 4.1)
- [ ] Indexes on FK columns and common query columns (`merchant_id`, `member_id`, `status`, `created_at`)
- [ ] N+1 query check — run Laravel Debugbar on all list pages
- [ ] Page load < 2s on production hardware (verify with Lighthouse or similar)
- [ ] Vite production build runs without errors (`npm run build`)
- [ ] Asset compression enabled (Vite handles JS/CSS — verify gzip on server)
- [ ] Image optimisation for logo uploads (resize/compress before storage)

---

## 4. Infrastructure & Deployment

### 4.1 Environment
- [ ] Production `.env` created with correct values
- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] MySQL database provisioned (DECISION-003: SQLite for dev, MySQL for production)
- [ ] Database connection tested
- [ ] Redis provisioned (for queues and cache)
- [ ] Queue worker configured and running (`php artisan queue:work`)
- [ ] Scheduler configured (`* * * * * php artisan schedule:run`)

### 4.2 File Storage
- [ ] Laravel filesystem configured for production (S3 or local with symlink)
- [ ] `php artisan storage:link` run (if using local disk)
- [ ] Logo upload directory writable

### 4.3 Email
- [ ] Mail driver configured (Mailgun, SES, or SMTP)
- [ ] `MAIL_FROM_ADDRESS` set to a verified sender domain
- [ ] Send a test email from production environment
- [ ] SPF/DKIM DNS records configured for sender domain

### 4.4 SSL & Domain
- [ ] SSL certificate installed and auto-renewing
- [ ] HTTP → HTTPS redirect in place
- [ ] `APP_URL` set to production HTTPS URL
- [ ] Domain DNS propagated

### 4.5 Migrations
- [ ] `php artisan migrate --force` runs cleanly on production database
- [ ] All migrations in order (check with `php artisan migrate:status`)
- [ ] Seed data (if any) applied

### 4.6 Backups
- [ ] Automated daily database backup configured
- [ ] Backup restoration tested
- [ ] File storage backup configured

---

## 5. Testing

- [ ] Manual smoke test: register → onboarding wizard → add member → record purchase → redeem reward
- [ ] Manual test: pause campaign → record purchase → verify no points issued
- [ ] Manual test: archive member → verify member hidden from active list but accessible
- [ ] Manual test: settings profile update → preferences update → password change
- [ ] Cross-browser test: Chrome, Safari, Firefox (Bootstrap 5 handles most compatibility)
- [ ] Mobile responsive check (Bootstrap grid — verify forms and tables on mobile viewport)
- [ ] Unit tests passing (`php artisan test`)
- [ ] Feature tests passing

---

## 6. Legal & Compliance

- [ ] Privacy Policy page accessible (required before launch)
- [ ] Terms of Service page accessible
- [ ] Cookie consent (if applicable for region)
- [ ] GDPR / data deletion flow — merchant can delete account and data
- [ ] PCI DSS — no card data stored in OneMember; Stripe handles payments (verify scope)
- [ ] Data Processing Agreement (DPA) with Stripe if required

---

## 7. Documentation & Support

- [ ] In-app help text / tooltips for key actions (NICE)
- [ ] Merchant onboarding guide / FAQ (NICE)
- [ ] Support contact method published (email, chat, or help desk)
- [ ] Error pages (404, 500, 419) styled with app branding

---

## 8. Monitoring & Observability

- [ ] Error tracking configured (Sentry or Laravel Telescope in production)
- [ ] Uptime monitoring configured (e.g., UptimeRobot or Better Uptime)
- [ ] Server resource monitoring (CPU, memory, disk)
- [ ] Log aggregation configured (Laravel logs → centralised service)
- [ ] Alerts set up for downtime and error spikes

---

## 9. Billing Verification — **BLOCKING**

- [ ] Test mode: new merchant registers → trial starts → trial end date displayed
- [ ] Test mode: trial expires → subscription prompt shown
- [ ] Test mode: Stripe checkout completes → merchant upgraded to paid plan
- [ ] Test mode: payment fails → merchant notified → access restricted
- [ ] Live mode: Stripe webhook verified in production
- [ ] Revenue recognised correctly (monthly / annual plans)

---

## 10. Soft Launch Sign-off

| Checkpoint | Owner | Date | Status |
|-----------|-------|------|--------|
| Feature completeness review | Product Owner | — | ⬜ |
| Security review | CTO | — | ⬜ |
| Performance review | CTO | — | ⬜ |
| Billing end-to-end test | Product Owner + CTO | — | ⬜ |
| Legal docs published | Product Owner | — | ⬜ |
| Infrastructure ready | CTO | — | ⬜ |
| Smoke test passed | Lead Developer | — | ⬜ |
| **GO / NO-GO** | Product Owner | — | ⬜ |
