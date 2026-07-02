# Release Philosophy

| Field | Value |
|---|---|
| **Document Owner** | ChatGPT CTO |
| **Version** | 1.0.0 |
| **Status** | Approved |
| **Last Updated** | 2026-07-02 |
| **Related Documents** | [MVP-Strategy.md](./MVP-Strategy.md), [11-Standards/Deployment-Standards.md](../11-Standards/Deployment-Standards.md), [00-Executive/Company-Principles.md](../00-Executive/Company-Principles.md) |

---

## Purpose

This document defines how OneMember thinks about releasing software. It answers: when is something ready to ship, how do we communicate releases to merchants, and how do we roll back if something goes wrong?

---

## Release Readiness

A feature is ready to release when:

1. All sprint tasks are complete
2. `php artisan test` passes with zero failures
3. Regression tests exist for the feature
4. The sprint has been reviewed and approved by ChatGPT CTO
5. Product Owner has given explicit deployment approval
6. All new `.env` variables are documented and provisioned in production
7. A rollback plan exists (migration `down()` tested, or feature is purely additive)

If any of these is not true, the feature is not ready to release. Partial releases are not acceptable.

---

## Release Cadence

OneMember does not have a fixed release cadence. Releases happen when:
- A sprint is complete and approved
- The Product Owner determines the timing is appropriate

There is no "release every Tuesday" — the timing is driven by product readiness and business context, not a calendar.

**Exceptions:**
- Security patches: release as soon as ready, subject only to passing tests and PO approval
- Critical bug fixes: same as security patches

---

## How Releases Are Communicated

### To Merchants
Major feature releases (new module, significant UX change) are communicated via:
- In-app notification in the dashboard
- Email to all active merchants (future: segmented by plan)
- Update to `CHANGELOG.md` (future)

Minor releases (bug fixes, small improvements) may be released without announcement.

### To the Team
Every release is documented in the sprint review and linked from `docs/OMOS/SprintReview.md`. The commit hash is recorded in `docs/OMOS/CurrentSprint.md`.

---

## Backward Compatibility

OneMember is not a public API service (Phase 1). Backward compatibility for the web application is not required — we can change UI flows, rename routes, and restructure forms without versioning concerns.

When the Enterprise Bridge API launches (Phase 2), backward compatibility for API endpoints becomes a requirement. API versioning standards will be defined at that time.

---

## Feature Flags

Feature flags are not currently used in OneMember (Phase 1). The `DEV_TOOLS_ENABLED` environment variable is the only flag-like mechanism, and it is for developer tools only.

If a future feature requires gradual rollout (e.g., testing the Customer Wallet with a subset of merchants before general availability), a feature flag system will be specified in an RFC before implementation.

---

## Rollback

Every release with a database migration must have a tested `down()` migration. The rollback procedure is defined in `docs/OMOS/11-Standards/Deployment-Standards.md`.

Rollback is authorised by the Product Owner. Claude Developer does not initiate a rollback without explicit instruction.

---

## Post-Release Monitoring

After every release:
1. Check Forge error logs for 15 minutes post-deploy
2. Verify `https://app.onemember.app/up` returns healthy
3. Run a manual smoke test of the affected feature
4. Monitor for support queries from merchants in the first 24 hours

If an error rate increases post-deploy, the Product Owner is notified immediately. Rollback is considered if critical merchant flows are affected.
