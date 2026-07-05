<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Enums\MemberStatus;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounterModeTest extends TestCase
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
            'settings'                => ['counter_mode' => true],
        ]);
    }

    public function test_counter_page_requires_authentication(): void
    {
        $this->get('/counter')->assertRedirect();
    }

    public function test_counter_page_loads_when_counter_mode_enabled(): void
    {
        $this->actingAs($this->user)
            ->get('/counter')
            ->assertOk();
    }

    public function test_counter_page_redirects_when_counter_mode_disabled(): void
    {
        $this->merchant->update(['settings' => ['counter_mode' => false]]);

        $this->actingAs($this->user)
            ->get('/counter')
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_counter_search_finds_member_by_phone(): void
    {
        $member = Member::factory()->create([
            'merchant_id' => $this->merchant->id,
            'phone'       => '0812345678',
            'status'      => MemberStatus::Active,
        ]);

        $this->actingAs($this->user)
            ->get('/counter?q=0812345678')
            ->assertOk()
            ->assertSee($member->name);
    }

    public function test_counter_search_excludes_other_merchants_members(): void
    {
        $otherUser     = User::factory()->create(['email_verified_at' => now()]);
        $otherMerchant = Merchant::factory()->create(['user_id' => $otherUser->id]);
        Member::factory()->create([
            'merchant_id' => $otherMerchant->id,
            'name'        => 'Foreign Member Zeta',
            'phone'       => '0899999999',
            'status'      => MemberStatus::Active,
        ]);

        $this->actingAs($this->user)
            ->get('/counter?q=0899999999')
            ->assertOk()
            ->assertDontSee('Foreign Member Zeta');
    }

    public function test_counter_search_excludes_inactive_members(): void
    {
        Member::factory()->create([
            'merchant_id' => $this->merchant->id,
            'name'        => 'Inactive Member Omega',
            'phone'       => '0811111111',
            'status'      => MemberStatus::Inactive,
        ]);

        $this->actingAs($this->user)
            ->get('/counter?q=0811111111')
            ->assertOk()
            ->assertDontSee('Inactive Member Omega');
    }

    public function test_purchase_from_counter_redirects_back_to_counter(): void
    {
        LoyaltyProgram::factory()->create([
            'merchant_id' => $this->merchant->id,
            'type'        => LoyaltyProgramType::Points,
            'status'      => CampaignStatus::Active,
            'settings'    => ['spend_amount' => 100, 'points_awarded' => 1],
        ]);
        $member = Member::factory()->create([
            'merchant_id' => $this->merchant->id,
            'status'      => MemberStatus::Active,
        ]);

        $this->actingAs($this->user)
            ->post(route('members.purchases.store', $member), [
                'purchase_amount' => 300,
                'return_to'       => 'counter',
            ])
            ->assertRedirect(route('counter', absolute: false))
            ->assertSessionHas('purchase_success');
    }
}
