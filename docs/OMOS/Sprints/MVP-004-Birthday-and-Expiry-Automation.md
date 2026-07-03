# Sprint Spec: MVP-004 — Birthday and Expiry Automation

| Field | Value |
|---|---|
| **Sprint ID** | MVP-004 |
| **Title** | Birthday and Expiry Automation |
| **Type** | Feature |
| **Classification** | Type B — CTO Decision Required |
| **Priority** | 🟠 High |
| **Status** | ✅ Ready |
| **Owner** | Product Owner |
| **Developer** | Claude Sonnet 4.6 |
| **Reviewer** | ChatGPT CTO |
| **Spec Written** | 2026-07-03 |
| **Approved** | 2026-07-03 |

---

## Business Outcome

> A merchant who has configured birthday bonuses or point expiry in their campaign settings can trust that these rules are being enforced automatically — without manual intervention.

---

## Background

Both birthday rewards and point expiry are fully configurable in the campaign UI but neither is enforced. The settings are stored; the automation does not run.

**Birthday rewards** — stored in `loyalty_programs.settings`:
- `birthday_enabled` (bool)
- `birthday_points` (int — bonus points to award)
- `birthday_valid_days_before` (int — days before birthday to open the window)
- `birthday_valid_days_after` (int — days after birthday to close the window)

**Point expiry** — stored in `loyalty_programs.settings`:
- `expiration_type` (`never` | `months` | `years`)
- `expiration_duration` (int — number of months or years)

Both `TransactionType::Birthday` and `TransactionType::Expire` are already defined and used by the UI (badge display, history). The infrastructure is ready; only the scheduled commands are missing.

**No schema changes are required.** The `transactions` table already supports these transaction types.

---

## Classification Rationale — Type B

This sprint creates new Artisan commands that automatically mutate member point balances on a schedule. This is a **new product capability**: members will start receiving points and losing points due to automated processes they did not manually trigger.

Type B triggers present:
- New product capability (birthday automation + point expiry runs on a cron)
- Automated mutation of `Member.total_points` and creation of `Transaction` records

CTO review is required before this sprint is considered complete.

---

## Technical Reference

### Models involved

| Model | Relevant fields |
|---|---|
| `LoyaltyProgram` | `settings` (JSON), `type` (Points/Stamps), `status` |
| `Member` | `merchant_id`, `birthday` (date), `total_points`, `last_activity_at` |
| `Transaction` | `merchant_id`, `member_id`, `loyalty_program_id`, `type`, `points`, `balance_before`, `balance_after`, `created_at` |

### Scope: Points campaigns only

Birthday automation and point expiry only apply to `LoyaltyProgramType::Points` campaigns. Stamp campaigns do not use points and are excluded from both commands.

### Birthday award window

A member is eligible for a birthday bonus on any day where:

```
today >= birthday.setYear(now()->year) - valid_days_before
AND
today <= birthday.setYear(now)->year) + valid_days_after
```

If `valid_days_before = 3` and `valid_days_after = 7`, the window opens 3 days before the member's birthday and closes 7 days after. The existing `BirthdayReward::isEligible()` method already implements this logic.

**Deduplication:** A member must not receive a birthday transaction more than once per calendar year. Check: `Transaction::where('member_id', $id)->where('type', 'birthday')->whereYear('created_at', now()->year)->exists()`.

### Point expiry: inactivity model

`expiration_duration` means: if a member has had no activity for N months/years, their points expire.

Activity is tracked by `Member.last_activity_at`. This field is set whenever a transaction is created for the member (earn, redeem, adjust).

Expiry condition: `last_activity_at < now()->sub(duration months/years)` AND `total_points > 0`.

**Deduplication:** Only expire if no `Expire` transaction exists for this member in this campaign created within the past 24 hours (prevents double-run on the same day).

### Transaction creation pattern

Both commands must create transactions using the same pattern as existing code:

```php
Transaction::create([
    'merchant_id'       => $merchant->id,
    'member_id'         => $member->id,
    'loyalty_program_id'=> $campaign->id,
    'created_by'        => null,             // system-generated
    'type'              => TransactionType::Birthday, // or ::Expire
    'points'            => $points,           // positive for birthday, negative for expire
    'balance_before'    => $member->total_points,
    'balance_after'     => $member->total_points + $points,
    'note'              => '...',
    'created_at'        => now(),
]);
$member->increment('total_points', $points);
// For expiry: $member->update(['total_points' => 0]);
```

`last_activity_at` must be updated when a birthday transaction is created. It must NOT be updated for expiry transactions (expiry is not activity).

---

## Tasks

### Task 1 — `ProcessBirthdayRewards` Artisan command

**File:** `app/Console/Commands/ProcessBirthdayRewards.php`

```
php artisan loyalty:process-birthday-rewards
```

**Logic:**

```
foreach active merchant
  foreach active Points campaign where settings.birthday_enabled = true
    foreach member of this merchant where birthday is not null
      if BirthdayReward::isEligible()-equivalent window check passes
        if no birthday Transaction exists for this member this calendar year
          create Transaction (type=birthday, points=settings.birthday_points)
          update member.total_points += birthday_points
          update member.last_activity_at = now()
          log: "Birthday bonus awarded: {member.name}, {birthday_points} pts, merchant {merchant.id}"
```

The command must be **idempotent**: running it twice on the same day must not double-award.

The command must respect **multi-tenancy**: each member is scoped to their merchant's campaign.

**Command signature:** `loyalty:process-birthday-rewards`
**Description:** `Award birthday bonus points to eligible members`

---

### Task 2 — `ProcessPointExpiry` Artisan command

**File:** `app/Console/Commands/ProcessPointExpiry.php`

```
php artisan loyalty:process-point-expiry
```

**Logic:**

```
foreach active merchant
  foreach active Points campaign where settings.expiration_type != 'never'
                                  and settings.expiration_duration > 0
    compute cutoff = now()->sub(duration months or years)
    foreach member of this merchant where total_points > 0
                                     and last_activity_at < cutoff
      if no Expire transaction for this member in this campaign in past 24 hours
        points_to_expire = member.total_points
        create Transaction (type=expire, points=-points_to_expire)
        update member.total_points = 0
        log: "Points expired: {member.name}, {points_to_expire} pts, merchant {merchant.id}"
```

The command must be **idempotent**: the 24-hour guard prevents double-expiry on the same day.

**Command signature:** `loyalty:process-point-expiry`
**Description:** `Expire points for members who have exceeded the inactivity window`

---

### Task 3 — Schedule both commands

**File:** `routes/console.php`

Add to the existing schedule:

```php
Schedule::command(ProcessBirthdayRewards::class)->dailyAt('08:00');
Schedule::command(ProcessPointExpiry::class)->dailyAt('02:00');
```

Birthday at 08:00 — merchant-friendly hour, members may check portal mid-morning.
Expiry at 02:00 — low-traffic window alongside existing `ProcessExpiredTrials`.

---

### Task 4 — Tests

**File:** `tests/Feature/BirthdayAndExpiryAutomationTest.php`

Tests required:

**Birthday command:**
- `test_birthday_bonus_awarded_on_birthday_date` — member with birthday today, campaign has `birthday_enabled=true`, expect Transaction created + total_points increased
- `test_birthday_bonus_not_awarded_if_birthday_not_in_window` — member's birthday is 30 days away, expect no transaction
- `test_birthday_bonus_not_awarded_twice_in_same_year` — run command twice, expect only one birthday transaction
- `test_birthday_bonus_not_awarded_when_birthday_disabled` — `birthday_enabled=false`, expect no transaction
- `test_birthday_bonus_not_awarded_for_stamp_campaign` — stamps campaign, expect no transaction
- `test_birthday_bonus_respects_valid_days_before` — birthday tomorrow, `valid_days_before=1`, expect awarded
- `test_birthday_bonus_respects_valid_days_after` — birthday was yesterday, `valid_days_after=1`, expect awarded

**Expiry command:**
- `test_points_expired_after_inactivity_window` — member last active 13 months ago, campaign expires after 12 months, expect Expire transaction + total_points = 0
- `test_points_not_expired_within_activity_window` — member last active 11 months ago, 12-month expiry, expect no transaction
- `test_points_not_expired_when_total_points_zero` — member already has 0 points, expect no transaction
- `test_points_not_expired_twice_on_same_day` — run command twice, expect only one Expire transaction
- `test_points_not_expired_for_never_expiry_type` — `expiration_type=never`, expect no transaction
- `test_points_not_expired_for_stamp_campaign` — stamps campaign, expect no transaction
- `test_expiry_uses_years_when_type_is_years` — `expiration_type=years`, `expiration_duration=1`, member last active 13 months ago, expect no expiry (only 1 year = 12 months has not passed by enough)

---

## Acceptance Criteria

| # | Criterion |
|---|---|
| AC-1 | `php artisan loyalty:process-birthday-rewards` runs without error on an empty database |
| AC-2 | Birthday bonus is created as a `birthday` Transaction for eligible members |
| AC-3 | Birthday bonus is not awarded twice in the same calendar year |
| AC-4 | Birthday bonus respects `valid_days_before` and `valid_days_after` window |
| AC-5 | Birthday bonus only applies to Points campaigns with `birthday_enabled = true` |
| AC-6 | `php artisan loyalty:process-point-expiry` runs without error on an empty database |
| AC-7 | Points are expired (Transaction type=expire, total_points=0) for inactive members past the window |
| AC-8 | Points are not expired for members active within the window |
| AC-9 | Points are not expired twice for the same member on the same day |
| AC-10 | Point expiry only applies to Points campaigns with `expiration_type != 'never'` |
| AC-11 | Both commands are scheduled in `routes/console.php` |
| AC-12 | `php artisan test` — zero failures |

---

## Commit Message

```
Sprint MVP-004 — Birthday and Expiry Automation

- ProcessBirthdayRewards: daily command, awards birthday points to eligible members
- ProcessPointExpiry: daily command, expires points after inactivity window
- Both scheduled in routes/console.php (08:00 and 02:00)
- BirthdayAndExpiryAutomationTest: 13 tests covering idempotency and edge cases

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

---

## Related Documents

- [EXECUTE.md](../EXECUTE.md)
- [Sprint-Classification.md](../Sprint-Classification.md)
- [Product-State.md](../Product-State.md)
- `app/Models/BirthdayReward.php` — `isEligible()` method reference
- `app/Enums/TransactionType.php` — Birthday, Expire cases
- `app/Console/Commands/ProcessExpiredTrials.php` — scheduling pattern reference
