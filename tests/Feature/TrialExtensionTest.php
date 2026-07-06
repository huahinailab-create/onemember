<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\TrialExtension;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrialExtensionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);
        $merchantUser = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'             => $merchantUser->id,
            'subscription_status' => SubscriptionStatus::Trial,
            'trial_ends_at'       => now()->addDays(3),
        ]);
    }

    private function extend(array $data)
    {
        return $this->actingAs($this->admin)
            ->post(route('admin.merchants.extend-trial', $this->merchant), $data);
    }

    public function test_admin_can_extend_trial_by_30_days(): void
    {
        $before = $this->merchant->trial_ends_at;

        $this->extend(['preset' => '30', 'reason' => 'pilot merchant'])->assertRedirect();

        $ext = TrialExtension::first();
        $this->assertSame(30, $ext->days);
        $this->assertSame($this->admin->id, $ext->admin_user_id);
        $this->assertSame('pilot merchant', $ext->reason);
        $this->assertTrue($this->merchant->fresh()->trial_ends_at->gt($before));
        $this->assertEqualsWithDelta($before->copy()->addDays(30)->timestamp, $this->merchant->fresh()->trial_ends_at->timestamp, 5);
    }

    public function test_admin_can_extend_by_60_and_custom_days(): void
    {
        $this->extend(['preset' => '60', 'reason' => 'x'])->assertRedirect();
        $this->assertSame(60, TrialExtension::latest('id')->first()->days);

        $this->extend(['preset' => 'custom', 'custom_days' => 14, 'reason' => 'y'])->assertRedirect();
        $this->assertSame(14, TrialExtension::latest('id')->first()->days);
    }

    public function test_reason_is_required(): void
    {
        $this->extend(['preset' => '30'])->assertSessionHasErrors(['reason']);
        $this->assertSame(0, TrialExtension::count());
    }

    public function test_custom_days_required_when_custom_preset(): void
    {
        $this->extend(['preset' => 'custom', 'reason' => 'x'])->assertSessionHasErrors(['custom_days']);
    }

    public function test_extension_reactivates_expired_trial_from_now(): void
    {
        $this->merchant->update([
            'subscription_status' => SubscriptionStatus::Expired,
            'trial_ends_at'       => now()->subDays(10),
        ]);

        $this->extend(['preset' => '30', 'reason' => 'win back'])->assertRedirect();

        $merchant = $this->merchant->fresh();
        $this->assertSame(SubscriptionStatus::Trial, $merchant->subscription_status);
        // From now, not from the past date
        $this->assertEqualsWithDelta(now()->addDays(30)->timestamp, $merchant->trial_ends_at->timestamp, 5);
    }

    public function test_extension_is_audited(): void
    {
        $this->extend(['preset' => '30', 'reason' => 'audit me']);

        $this->assertTrue(AuditLog::where('event', 'trial.extended')->where('merchant_id', $this->merchant->id)->exists());
    }

    public function test_non_admin_cannot_extend(): void
    {
        $merchantUser = $this->merchant->owner;

        $this->actingAs($merchantUser)
            ->post(route('admin.merchants.extend-trial', $this->merchant), ['preset' => '30', 'reason' => 'x'])
            ->assertForbidden();

        $this->assertSame(0, TrialExtension::count());
    }

    public function test_detail_page_shows_extension_form_and_history(): void
    {
        TrialExtension::create([
            'merchant_id'       => $this->merchant->id,
            'admin_user_id'     => $this->admin->id,
            'days'              => 30,
            'new_trial_ends_at' => now()->addDays(30),
            'reason'            => 'earlier extension',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.merchants.show', $this->merchant, absolute: false))
            ->assertOk()
            ->assertSee('Trial Extension')
            ->assertSee('earlier extension');
    }

    public function test_index_filters_ending_soon_extended_and_expired(): void
    {
        // ending soon (3 days) = $this->merchant
        $extended = Merchant::factory()->create(['subscription_status' => SubscriptionStatus::Trial, 'trial_ends_at' => now()->addDays(40)]);
        TrialExtension::create(['merchant_id' => $extended->id, 'admin_user_id' => $this->admin->id, 'days' => 30, 'new_trial_ends_at' => now()->addDays(40), 'reason' => 'r']);
        $expired = Merchant::factory()->create(['subscription_status' => SubscriptionStatus::Expired, 'trial_ends_at' => now()->subDay()]);

        $this->actingAs($this->admin)->get(route('admin.merchants.index', ['trial' => 'ending_soon'], absolute: false))
            ->assertOk()->assertSee($this->merchant->name)->assertDontSee($expired->name);

        $this->actingAs($this->admin)->get(route('admin.merchants.index', ['trial' => 'extended'], absolute: false))
            ->assertOk()->assertSee($extended->name)->assertDontSee($expired->name);

        $this->actingAs($this->admin)->get(route('admin.merchants.index', ['trial' => 'expired'], absolute: false))
            ->assertOk()->assertSee($expired->name);
    }
}
