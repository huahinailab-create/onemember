# PH2-001D — Wallet Dashboard Polish & Notification Unification

| Field | Value |
|---|---|
| **Status** | 🔲 Planning — blocked by PH2-001C; BD-08 (channel priority) |
| **Classification** | Type A/B (mostly UX + one event refactor) |
| **Complexity** | Medium |
| **Dependencies** | PH2-001B/C live |

## Objective
Production-quality wallet home (W4/W5): branding, reward progress, history pagination, PWA install; unify notifications so a wallet-linked member gets exactly one email per event (W-40).

## Files Expected to Change
- Views W4/W5 polish; PWA manifest scope for wallet domain; empty/offline states (Doc 10 notes)
- `app/Events/WalletBalanceChanged.php` + dispatch from existing member-event listeners
- `app/Listeners/MemberEmailSubscriber.php` — dedup rule (suppress member email when link + consent route the wallet email)
- Wallet notification mailables (points, reward available, birthday) — reuse MVP-006 templates with wallet CTA
- Cache: card-list `Cache::remember` + invalidation on `WalletBalanceChanged` (Scalability §1.5)

## Database Impact
None (reads only).

## Test Plan
- One-email-per-event matrix: {linked, unlinked} × {consented, not} × {points, reward, birthday}
- Cache invalidation test (balance change refreshes card list)
- Lighthouse mobile pass ≥ 90 performance/accessibility on W4 (staging gate)

## Acceptance Criteria
1. No customer ever receives duplicate event emails (test-proven matrix).
2. Card list p95 < 200 ms with warm cache at 5 linked memberships.
3. Wallet installable as PWA on Android/iOS Safari; offline shows last-known balances with timestamp.
