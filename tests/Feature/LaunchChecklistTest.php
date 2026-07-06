<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Reward;
use App\Models\User;
use App\Services\LaunchChecklistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaunchChecklistTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;
    private LaunchChecklistService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
            'business_type'           => null,
            'settings'                => ['counter_mode' => true],
        ]);
        $this->service = app(LaunchChecklistService::class);
    }

    public function test_checklist_starts_incomplete(): void
    {
        $this->merchant->update(['business_type' => null]);
        $c = $this->service->for($this->merchant->fresh());

        $this->assertSame(6, $c['total']);          // no commerce app → 6 items
        $this->assertLessThan(100, $c['percent']);
        $this->assertFalse(collect($c['items'])->firstWhere('key', 'campaign')['done']);
    }

    public function test_data_items_flip_to_done(): void
    {
        $this->merchant->update(['name' => 'Shop', 'business_type' => 'Restaurant & Café']);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $campaign->id]);
        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        $items = collect($this->service->for($this->merchant->fresh())['items'])->keyBy('key');

        $this->assertTrue($items['profile']['done']);
        $this->assertTrue($items['campaign']['done']);
        $this->assertTrue($items['reward']['done']);
        $this->assertTrue($items['member']['done']);
    }

    public function test_commerce_item_only_appears_when_app_installed(): void
    {
        $this->assertFalse(collect($this->service->for($this->merchant)['items'])->contains('key', 'storefront'));

        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['installed_apps' => ['commerce']])]);
        $this->assertTrue(collect($this->service->for($this->merchant->fresh())['items'])->contains('key', 'storefront'));
    }

    public function test_visiting_launch_kit_marks_flag(): void
    {
        $this->assertFalse(collect($this->service->for($this->merchant)['items'])->firstWhere('key', 'launch_kit')['done']);

        $this->actingAs($this->user)->get(route('launch-kit', absolute: false))->assertOk();

        $this->assertTrue(collect($this->service->for($this->merchant->fresh())['items'])->firstWhere('key', 'launch_kit')['done']);
    }

    public function test_visiting_counter_marks_flag(): void
    {
        $this->actingAs($this->user)->get(route('counter', absolute: false))->assertOk();

        $this->assertTrue(collect($this->service->for($this->merchant->fresh())['items'])->firstWhere('key', 'counter')['done']);
    }

    public function test_dashboard_shows_checklist_until_complete(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(__('launch_check.title', [], 'en'));
    }

    public function test_dashboard_hides_checklist_when_complete(): void
    {
        // Complete every item
        $this->merchant->update([
            'name' => 'Shop', 'business_type' => 'Restaurant & Café',
            'settings' => ['counter_mode' => true, 'launch_flags' => ['launch_kit_opened' => true, 'counter_tried' => true]],
        ]);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $campaign->id]);
        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->assertSame(100, $this->service->for($this->merchant->fresh())['percent']);

        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee(__('launch_check.title', [], 'en'));
    }
}
