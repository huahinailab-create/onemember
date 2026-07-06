<?php

namespace Tests\Feature;

use App\Enums\MemberStatus;
use App\Mail\WinbackAlertEmail;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WinbackAlertTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'settings'                => ['winback_days' => 30],
        ]);
    }

    private function makeMember(int $inactiveDays, MemberStatus $status = MemberStatus::Active): Member
    {
        return Member::factory()->create([
            'merchant_id'      => $this->merchant->id,
            'status'           => $status,
            'last_activity_at' => now()->subDays($inactiveDays)->subHours(2),
        ]);
    }

    // ── Command ──────────────────────────────────────────────────────────

    public function test_winback_email_sent_for_member_crossing_threshold(): void
    {
        Mail::fake();
        $this->makeMember(30);

        $this->artisan('loyalty:send-winback-alerts')->assertSuccessful();

        Mail::assertQueued(WinbackAlertEmail::class, function ($mail) {
            return $mail->hasTo($this->user->email) && $mail->days === 30;
        });
    }

    public function test_no_email_for_member_long_past_threshold(): void
    {
        Mail::fake();
        $this->makeMember(60);

        $this->artisan('loyalty:send-winback-alerts')->assertSuccessful();

        Mail::assertNotQueued(WinbackAlertEmail::class);
    }

    public function test_no_email_for_recently_active_member(): void
    {
        Mail::fake();
        $this->makeMember(5);

        $this->artisan('loyalty:send-winback-alerts')->assertSuccessful();

        Mail::assertNotQueued(WinbackAlertEmail::class);
    }

    public function test_no_email_when_winback_disabled(): void
    {
        Mail::fake();
        $this->merchant->update(['settings' => ['winback_days' => 0]]);
        $this->makeMember(30);

        $this->artisan('loyalty:send-winback-alerts')->assertSuccessful();

        Mail::assertNotQueued(WinbackAlertEmail::class);
    }

    public function test_no_email_for_inactive_member(): void
    {
        Mail::fake();
        $this->makeMember(30, MemberStatus::Inactive);

        $this->artisan('loyalty:send-winback-alerts')->assertSuccessful();

        Mail::assertNotQueued(WinbackAlertEmail::class);
    }

    // ── Dashboard alert ──────────────────────────────────────────────────

    public function test_dashboard_shows_winback_alert_when_members_at_risk(): void
    {
        $this->makeMember(45);

        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('win-back nudge');
    }

    public function test_dashboard_hides_winback_alert_when_disabled(): void
    {
        $this->merchant->update(['settings' => ['winback_days' => 0]]);
        $this->makeMember(45);

        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('win-back nudge');
    }

    // ── Settings ─────────────────────────────────────────────────────────

    public function test_merchant_can_update_winback_days(): void
    {
        $this->actingAs($this->user)->put(route('settings.preferences.update'), [
            'currency'                    => 'THB',
            'timezone'                    => 'Asia/Bangkok',
            'date_format'                 => 'DD/MM/YYYY',
            'default_expiration_type'     => 'never',
            'default_birthday_enabled'    => 1,
            'locale'                      => 'th',
            'email_product_updates'       => 1,
            'email_tips'                  => 1,
            'email_feature_announcements' => 1,
            'winback_days'                => 45,
            'country'                     => 'TH',
        ])->assertRedirect();

        $this->assertSame(45, $this->merchant->fresh()->settings['winback_days']);
    }
}
