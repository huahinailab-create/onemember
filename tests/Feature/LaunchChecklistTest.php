<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\LoyaltyProgramType;
use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Reward;
use App\Models\User;
use App\Services\LaunchChecklistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * LAUNCH-001 checklist, evolved by MERCHANT-READY-001 / MR-001:
 * launch checklist progress, launch completion, deterministic next
 * recommended action, merchant health card, and tenant isolation.
 */
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

    /** Completes every checklist item for the given merchant (incl. commerce). */
    private function completeEverything(Merchant $merchant): void
    {
        $merchant->update([
            'name'          => 'Shop',
            'business_type' => 'Restaurant & Café',
            'logo_path'     => 'logos/shop.png',
            'settings'      => array_merge($merchant->settings ?? [], [
                'installed_apps' => ['commerce'],
                'launch_flags'   => ['qr_poster_viewed' => true, 'storefront_visited' => true],
            ]),
        ]);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $merchant->id, 'loyalty_program_id' => $campaign->id]);
        Member::factory()->create(['merchant_id' => $merchant->id]);
        Product::factory()->create(['merchant_id' => $merchant->id]);
    }

    // ── Checklist progress ──────────────────────────────────────────────

    public function test_checklist_starts_with_only_store_url_done(): void
    {
        $c = $this->service->for($this->merchant->fresh());

        // No commerce app → 7 items (no product / storefront)
        $this->assertSame(7, $c['total']);
        // Store URL is auto-generated at registration → the only done item
        $this->assertSame(1, $c['done']);
        $this->assertSame((int) round(1 / 7 * 100), $c['percent']);
        $this->assertFalse($c['launch_ready']);

        $items = collect($c['items'])->keyBy('key');
        $this->assertTrue($items['store_url']['done']);
        $this->assertFalse($items['profile']['done']);
        $this->assertFalse($items['logo']['done']);
        $this->assertFalse($items['campaign']['done']);
    }

    public function test_progress_counts_track_completed_items(): void
    {
        $this->merchant->update(['business_type' => 'Restaurant & Café', 'logo_path' => 'logos/x.png']);
        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $campaign->id]);

        $c = $this->service->for($this->merchant->fresh());

        // profile + logo + store_url + campaign + reward = 5 of 7
        $this->assertSame(5, $c['done']);
        $this->assertSame((int) round(5 / 7 * 100), $c['percent']);
        $this->assertFalse($c['launch_ready']);
    }

    public function test_commerce_items_only_appear_when_app_installed(): void
    {
        $keys = collect($this->service->for($this->merchant)['items'])->pluck('key');
        $this->assertFalse($keys->contains('product'));
        $this->assertFalse($keys->contains('storefront'));

        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['installed_apps' => ['commerce']])]);

        $c = $this->service->for($this->merchant->fresh());
        $keys = collect($c['items'])->pluck('key');
        $this->assertTrue($keys->contains('product'));
        $this->assertTrue($keys->contains('storefront'));
        $this->assertSame(9, $c['total']);
    }

    // ── Launch completion ───────────────────────────────────────────────

    public function test_completing_everything_reaches_launch_ready(): void
    {
        $this->completeEverything($this->merchant);

        $c = $this->service->for($this->merchant->fresh());

        $this->assertSame(9, $c['total']);
        $this->assertSame(9, $c['done']);
        $this->assertSame(100, $c['percent']);
        $this->assertTrue($c['launch_ready']);
        $this->assertNull($this->service->nextAction($this->merchant->fresh()));
    }

    public function test_dashboard_shows_launch_ready_badge_at_100_percent(): void
    {
        $this->completeEverything($this->merchant);

        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(__('launch_check.launch_ready', [], 'en'));
    }

    public function test_dashboard_shows_checklist_and_health_card(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(__('launch_check.title', [], 'en'))
            ->assertSee(__('launch_check.health_title', [], 'en'))
            ->assertSee(__('launch_check.next_title', [], 'en'));
    }

    // ── Next recommended action (deterministic) ─────────────────────────

    public function test_next_action_is_first_incomplete_item_in_fixed_order(): void
    {
        // Fresh merchant → profile first
        $this->assertSame('profile', $this->service->nextAction($this->merchant->fresh())['key']);

        $this->merchant->update(['business_type' => 'Restaurant & Café']);
        $this->assertSame('logo', $this->service->nextAction($this->merchant->fresh())['key']);

        $this->merchant->update(['logo_path' => 'logos/x.png']);
        // store_url already done → campaign is next (no commerce app)
        $this->assertSame('campaign', $this->service->nextAction($this->merchant->fresh())['key']);

        $campaign = LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Active]);
        $this->assertSame('reward', $this->service->nextAction($this->merchant->fresh())['key']);

        Reward::factory()->create(['merchant_id' => $this->merchant->id, 'loyalty_program_id' => $campaign->id]);
        $this->assertSame('member', $this->service->nextAction($this->merchant->fresh())['key']);

        Member::factory()->create(['merchant_id' => $this->merchant->id]);
        $this->assertSame('qr_poster', $this->service->nextAction($this->merchant->fresh())['key']);
    }

    public function test_next_action_prefers_product_before_campaign_with_commerce(): void
    {
        $this->merchant->update([
            'business_type' => 'Restaurant & Café',
            'logo_path'     => 'logos/x.png',
            'settings'      => array_merge($this->merchant->settings, ['installed_apps' => ['commerce']]),
        ]);

        $this->assertSame('product', $this->service->nextAction($this->merchant->fresh())['key']);
    }

    public function test_next_action_is_stable_across_repeated_calls(): void
    {
        $first  = $this->service->nextAction($this->merchant->fresh());
        $second = $this->service->nextAction($this->merchant->fresh());

        $this->assertSame($first['key'], $second['key']);
        $this->assertSame($first['url'], $second['url']);
    }

    // ── Visit flags ─────────────────────────────────────────────────────

    public function test_viewing_qr_poster_marks_flag(): void
    {
        $this->assertFalse(collect($this->service->for($this->merchant)['items'])->firstWhere('key', 'qr_poster')['done']);

        $this->actingAs($this->user)->get(route('launch-kit.poster', absolute: false))->assertOk();

        $this->assertTrue(collect($this->service->for($this->merchant->fresh())['items'])->firstWhere('key', 'qr_poster')['done']);
    }

    public function test_visiting_own_storefront_marks_flag(): void
    {
        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['installed_apps' => ['commerce']])]);

        $this->actingAs($this->user)
            ->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk();

        $this->assertTrue(collect($this->service->for($this->merchant->fresh())['items'])->firstWhere('key', 'storefront')['done']);
    }

    public function test_guest_storefront_visit_does_not_mark_flag(): void
    {
        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['installed_apps' => ['commerce']])]);

        $this->get(route('storefront.show', $this->merchant->slug, absolute: false))->assertOk();

        $this->assertFalse(collect($this->service->for($this->merchant->fresh())['items'])->firstWhere('key', 'storefront')['done']);
    }

    // ── Tenant isolation ────────────────────────────────────────────────

    public function test_checklist_state_is_tenant_scoped(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        $other = Merchant::factory()->create([
            'user_id'                 => $otherUser->id,
            'onboarding_completed_at' => now(),
            'business_type'           => null,
            'settings'                => null,
        ]);

        // Merchant A completes everything — Merchant B must be untouched.
        $this->completeEverything($this->merchant);

        $this->assertTrue($this->service->for($this->merchant->fresh())['launch_ready']);

        $b = $this->service->for($other->fresh());
        $this->assertFalse($b['launch_ready']);
        $this->assertSame(1, $b['done']); // only the auto-generated store URL
    }

    public function test_another_merchant_visiting_storefront_does_not_mark_owner_flag(): void
    {
        $this->merchant->update(['settings' => array_merge($this->merchant->settings, ['installed_apps' => ['commerce']])]);

        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        Merchant::factory()->create(['user_id' => $otherUser->id, 'onboarding_completed_at' => now()]);

        // A different merchant's owner browses this storefront
        $this->actingAs($otherUser)
            ->get(route('storefront.show', $this->merchant->slug, absolute: false))
            ->assertOk();

        $this->assertFalse(collect($this->service->for($this->merchant->fresh())['items'])->firstWhere('key', 'storefront')['done']);
    }

    // ── Merchant health card ────────────────────────────────────────────

    public function test_health_statuses_follow_green_amber_red_rules(): void
    {
        // Fresh merchant: name but no business_type → profile amber;
        // no logo → amber; slug set → green; no campaign/member → red.
        $rows = collect($this->service->health($this->merchant->fresh())['rows'])->keyBy('key');

        $this->assertSame('amber', $rows['profile']['status']);
        $this->assertSame('amber', $rows['logo']['status']);
        $this->assertSame('green', $rows['store_url']['status']);
        $this->assertSame('red', $rows['campaigns']['status']);
        $this->assertSame('red', $rows['members']['status']);
        $this->assertFalse($rows->has('products')); // commerce not installed

        $this->completeEverything($this->merchant);
        $health = $this->service->health($this->merchant->fresh());
        $rows = collect($health['rows'])->keyBy('key');

        $this->assertSame('green', $rows['profile']['status']);
        $this->assertSame('green', $rows['logo']['status']);
        $this->assertSame('green', $rows['products']['status']);
        $this->assertSame('green', $rows['campaigns']['status']);
        $this->assertSame('green', $rows['members']['status']);
        $this->assertSame('green', $rows['storefront']['status']);
        $this->assertSame(100, $health['percent']);
        $this->assertTrue($health['launch_ready']);
    }

    public function test_health_campaigns_amber_when_none_active(): void
    {
        LoyaltyProgram::factory()->create(['merchant_id' => $this->merchant->id, 'type' => LoyaltyProgramType::Points, 'status' => CampaignStatus::Paused]);

        $rows = collect($this->service->health($this->merchant->fresh())['rows'])->keyBy('key');

        $this->assertSame('amber', $rows['campaigns']['status']);
    }
}
