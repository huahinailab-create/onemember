<?php

namespace Tests\Feature;

use App\Enums\MerchantStatus;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaunchKitTest extends TestCase
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
        ]);
    }

    // ── Access control ───────────────────────────────────────────────────

    public function test_guest_is_redirected_from_launch_kit(): void
    {
        $this->get('/launch-kit')->assertRedirect();
    }

    public function test_merchant_can_open_launch_kit(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/launch-kit')
            ->assertOk()
            ->assertSee(__('launch.title', [], 'en'))
            ->assertSee('Join the OneMember Family.');
    }

    public function test_user_without_merchant_gets_403(): void
    {
        $bare = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($bare)->get('/launch-kit')->assertForbidden();
    }

    // ── Merchant-scoped QR / join link ───────────────────────────────────

    public function test_launch_kit_shows_this_merchants_join_link_and_qr(): void
    {
        $response = $this->actingAs($this->user)->get('/launch-kit');

        $response->assertOk()
            ->assertSee(route('join.show', $this->merchant->slug, absolute: false))
            ->assertSee('<svg', false); // inline QR SVG rendered
    }

    public function test_join_link_is_scoped_to_own_merchant(): void
    {
        $otherUser     = User::factory()->create(['email_verified_at' => now()]);
        $otherMerchant = Merchant::factory()->create([
            'user_id' => $otherUser->id,
            'name'    => 'Other Shop Zeta',
        ]);

        $this->actingAs($this->user)
            ->get('/launch-kit')
            ->assertOk()
            ->assertDontSee(route('join.show', $otherMerchant->slug, absolute: false));
    }

    // ── Offer configurability ────────────────────────────────────────────

    public function test_offer_variants_change_campaign_copy(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/launch-kit?offer=dessert')
            ->assertOk()
            ->assertSee(__('launch.offer_dessert', [], 'en'));
    }

    public function test_invalid_offer_falls_back_to_coffee(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/launch-kit?offer=lambo')
            ->assertOk()
            ->assertSee(__('launch.offer_coffee', [], 'en'));
    }

    // ── Printable pages ──────────────────────────────────────────────────

    public function test_poster_renders_with_qr_and_copy(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/launch-kit/poster')
            ->assertOk()
            ->assertSee($this->merchant->name)
            ->assertSee('Join the OneMember Family.')
            ->assertSee('<svg', false);
    }

    public function test_counter_card_renders(): void
    {
        $this->actingAs($this->user)
            ->get('/launch-kit/counter-card')
            ->assertOk()
            ->assertSee($this->merchant->name)
            ->assertSee('<svg', false);
    }

    public function test_staff_guide_renders_all_steps(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/launch-kit/staff-guide')
            ->assertOk()
            ->assertSee(__('launch.guide_step_search', [], 'en'))
            ->assertSee(__('launch.guide_step_add', [], 'en'))
            ->assertSee(__('launch.guide_step_purchase', [], 'en'))
            ->assertSee(__('launch.guide_step_redeem', [], 'en'))
            ->assertSee(__('launch.guide_step_counter', [], 'en'))
            ->assertSee(__('launch.guide_say_title', [], 'en'));
    }

    public function test_printables_are_guest_protected(): void
    {
        $this->get('/launch-kit/poster')->assertRedirect();
        $this->get('/launch-kit/counter-card')->assertRedirect();
        $this->get('/launch-kit/staff-guide')->assertRedirect();
    }

    // ── Public join landing ──────────────────────────────────────────────

    public function test_public_join_landing_renders_for_guest(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get(route('join.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee($this->merchant->name)
            ->assertSee('Join the OneMember Family.');
    }

    public function test_join_landing_404_for_unknown_slug(): void
    {
        $this->get('/join/no-such-shop')->assertNotFound();
    }

    public function test_join_landing_404_for_suspended_merchant(): void
    {
        $this->merchant->update(['status' => MerchantStatus::Suspended]);

        $this->get(route('join.show', $this->merchant->slug, absolute: false))
            ->assertNotFound();
    }

    // ── Localization ─────────────────────────────────────────────────────

    public function test_launch_kit_defaults_to_thai(): void
    {
        $this->actingAs($this->user)
            ->get('/launch-kit')
            ->assertOk()
            ->assertSee(__('launch.title', [], 'th'));
    }

    public function test_join_landing_renders_in_thai_by_default(): void
    {
        $this->get(route('join.show', $this->merchant->slug, absolute: false))
            ->assertOk()
            ->assertSee(__('launch.campaign_headline', [], 'th'));
    }

    // ── Navigation integration ───────────────────────────────────────────

    public function test_sidebar_contains_launch_kit_link(): void
    {
        $this->actingAs($this->user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('launch-kit', absolute: false));
    }

    public function test_onboarding_finish_links_to_launch_kit(): void
    {
        $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get(route('onboarding.finish', absolute: false))
            ->assertOk()
            ->assertSee(route('launch-kit', absolute: false))
            ->assertSee(__('onboarding.open_launch_kit', [], 'en'));
    }
}
