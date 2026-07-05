# RELEASE-3A — OneMember Platform Admin Foundation

**Sprint:** RELEASE-3A  
**Status:** Complete  
**Date:** 2026-07-05  
**Decision:** DECISION-068

---

## Objective

Build an internal Platform Admin area for OneMember management to monitor platform performance: merchant registrations, member growth, transaction volume, and merchants that need attention.

---

## Requirements Delivered

| # | Requirement | Status |
|---|---|---|
| 1 | Admin role/flag on users table | ✅ |
| 2 | Merchants blocked from /admin (403) | ✅ |
| 3 | Guests redirected to login | ✅ |
| 4 | Admin middleware protecting all /admin routes | ✅ |
| 5 | /admin/dashboard with full metrics | ✅ |
| 6 | /admin/merchants with search + filters | ✅ |
| 7 | /admin/merchants/{merchant} detail page | ✅ |
| 8 | Platform analytics (new merchants/members by day/week/month) | ✅ |
| 9 | Top merchants by members and transactions | ✅ |
| 10 | Attention metrics (zero members, not onboarded, trial ending soon) | ✅ |
| 11 | OneMember design system applied | ✅ |
| 12 | 21 tests — all passing | ✅ |
| 13 | DECISION-068 documented | ✅ |
| 14 | `npm run build` succeeds | ✅ |

---

## Access Model

**Who can access admin:**
- Users with `is_admin = true` on the `users` table

**Who is blocked:**
- Merchants (`is_admin = false`, even with a merchant account) → 403
- Unauthenticated guests → redirect to `/login`

**How to create the first admin:**
```bash
php artisan tinker
User::where('email', 'your@email.com')->update(['is_admin' => true]);
```

**Middleware stack:** `auth → verified → admin (EnsureUserIsAdmin)`

---

## Dashboard Metrics

| Section | Metrics |
|---|---|
| Merchant counts | Total, Active, On Trial, Paid, Free, Inactive |
| Member counts | Total, Added today |
| Transactions | Total, Today |
| Redemptions | Total, Today |
| New Merchants trend | Today / This week / This month |
| New Members trend | Today / This week / This month |
| Top performers | Top 5 by members, Top 5 by transactions |
| Attention needed | Zero members, Not onboarded, Trial ending <7 days |
| System health | Database status, Queue status |
| Recent registrations | Last 10 merchants with plan + status |

---

## Merchant List (`/admin/merchants`)

**Columns:** Business name, Owner name, Owner email, Plan, Subscription status, Trial end date, Members count, Transactions count, Registered date

**Filters:**
- Search (name, contact person, email)
- Plan (free / starter / professional / enterprise)
- Status (active / inactive / suspended)
- Subscription status (trial / active / expired / cancelled)
- Date range (registered from / to)

---

## Merchant Detail (`/admin/merchants/{merchant}`)

- Merchant profile (name, contact, email, phone, city, country, website, onboarding status)
- Owner (name, email, email verification)
- Plan / Subscription (plan, status, trial end, Stripe customer ID)
- Counts (members, active members, transactions, redemptions, campaigns, rewards)
- Recent 10 members (name, phone, joined, points)
- Recent 10 transactions (member, type, points, date)

---

## Files Changed

| File | Change |
|---|---|
| `database/migrations/2026_07_05_000001_add_is_admin_to_users_table.php` | New — adds `is_admin` boolean |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | New — 403 if not admin |
| `app/View/Components/AdminLayout.php` | New — Blade layout component |
| `app/Http/Controllers/Admin/DashboardController.php` | New |
| `app/Http/Controllers/Admin/MerchantController.php` | New |
| `resources/views/layouts/admin.blade.php` | New — desktop sidebar layout |
| `resources/views/admin/dashboard.blade.php` | New |
| `resources/views/admin/merchants/index.blade.php` | New |
| `resources/views/admin/merchants/show.blade.php` | New |
| `tests/Feature/AdminTest.php` | New — 21 tests |
| `app/Models/User.php` | Added `is_admin` fillable + cast |
| `database/factories/UserFactory.php` | Added `is_admin: false` default |
| `bootstrap/app.php` | Added `admin` middleware alias |
| `routes/web.php` | Added `/admin` route group |
| `docs/08-Product-Decisions.md` | DECISION-068 appended |

---

## Test Results

```
457 tests, 457 passed, 915 assertions
21 new tests in AdminTest
```

### Test Coverage
- Guest redirected from `/admin/dashboard` and `/admin/merchants`
- Merchant gets 403 on dashboard, merchant list, merchant detail
- Admin can access all three pages
- Dashboard shows correct merchant count
- Dashboard shows correct member count
- Dashboard shows recent merchant names
- Dashboard contains ADMIN layout markers
- Merchant list shows merchant names
- Merchant list search filter works
- Merchant list shows member counts
- Merchant detail shows correct merchant data
- Merchant detail shows owner name
- Merchant detail shows member count
- `is_admin` defaults to false for new users
- `is_admin` can be set to true
- Regular user without merchant account gets 403

---

## Risks / Next Recommendations

1. **Domain migration:** Move to `admin.onemember.co` in RELEASE-3B by adding a domain group in `routes/web.php` and updating `config/domains.php`. No controller or view changes needed.
2. **Admin user management UI:** Currently admins must be created via `artisan tinker`. A future sprint could add `/admin/users` with a promote/demote UI — only accessible to super-admins.
3. **Audit logging:** Admin actions (viewing merchant data) are not currently logged to `audit_logs`. Consider adding in a follow-up sprint for GDPR/compliance.
4. **Pagination on detail pages:** Recent members and transactions are capped at 10. Add pagination if merchants have large datasets.
5. **Real-time metrics:** Dashboard metrics are computed on page load. For a platform with many merchants, consider caching these counts (5-minute cache is sufficient).
