<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\SubscriptionStatus;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MerchantHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantHealthTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);
    }

    private function signal(string $key): int
    {
        return collect(app(MerchantHealthService::class)->signals())->firstWhere('key', $key)['count'];
    }

    public function test_no_campaign_reward_member_transaction_signals(): void
    {
        Merchant::factory()->create();  // bare merchant → hits all four

        $this->assertSame(1, $this->signal('no_campaign'));
        $this->assertSame(1, $this->signal('no_reward'));
        $this->assertSame(1, $this->signal('no_members'));
        $this->assertSame(1, $this->signal('no_transactions'));
    }

    public function test_trial_ending_and_extended_signals(): void
    {
        Merchant::factory()->create(['subscription_status' => SubscriptionStatus::Trial, 'trial_ends_at' => now()->addDays(3)]);
        $m = Merchant::factory()->create(['subscription_status' => SubscriptionStatus::Trial, 'trial_ends_at' => now()->addDays(40)]);
        \App\Models\TrialExtension::create(['merchant_id' => $m->id, 'admin_user_id' => $this->admin->id, 'days' => 30, 'new_trial_ends_at' => now()->addDays(40), 'reason' => 'r']);

        $this->assertSame(1, $this->signal('trial_ending'));
        $this->assertSame(1, $this->signal('extended'));
    }

    public function test_high_usage_unpaid_signal(): void
    {
        $m = Merchant::factory()->create(['subscription_status' => SubscriptionStatus::Trial]);
        Member::factory()->count(20)->create(['merchant_id' => $m->id]);

        $paid = Merchant::factory()->create(['subscription_status' => SubscriptionStatus::Active]);
        Member::factory()->count(25)->create(['merchant_id' => $paid->id]);

        $this->assertSame(1, $this->signal('high_unpaid'));  // only the trial one
    }

    public function test_inactive_signal(): void
    {
        $m = Merchant::factory()->create();
        $member = Member::factory()->create(['merchant_id' => $m->id]);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $m->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Transaction::create([
            'merchant_id' => $m->id, 'member_id' => $member->id, 'loyalty_program_id' => $campaign->id,
            'type' => 'earn', 'points' => 5, 'balance_before' => 0, 'balance_after' => 5, 'created_at' => now()->subDays(45),
        ]);

        $this->assertSame(1, $this->signal('inactive'));  // has member, no recent tx
    }

    public function test_admin_dashboard_shows_health_widget(): void
    {
        $this->actingAs($this->admin)
            ->withSession(['locale' => 'en'])
            ->get(route('admin.dashboard', absolute: false))
            ->assertOk()
            ->assertSee('Merchant Health', false)
            ->assertSee(__('admin_health.no_campaign', [], 'en'));
    }

    public function test_health_filter_links_work_on_merchant_list(): void
    {
        $bare = Merchant::factory()->create(['name' => 'Bare Shop Alpha']);
        $withCampaign = Merchant::factory()->create(['name' => 'Active Shop Beta']);
        LoyaltyProgram::factory()->create(['merchant_id' => $withCampaign->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);

        $this->actingAs($this->admin)
            ->get(route('admin.merchants.index', ['health' => 'no_campaign'], absolute: false))
            ->assertOk()
            ->assertSee('Bare Shop Alpha')
            ->assertDontSee('Active Shop Beta');
    }
}
