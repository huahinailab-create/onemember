<?php

namespace Tests\Feature;

use App\Models\LoyaltyProgram;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\Reward;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\CustomerPortalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────

    private function actingAsMerchant(array $settings = []): array
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'settings'                => array_merge([
                'locale'                   => 'en',
                'timezone'                 => 'Asia/Bangkok',
                'currency'                 => 'THB',
                'default_expiration_type'  => 'none',
                'default_expiration_value' => null,
                'email_notifications'      => [],
                'counter_mode'             => false,
            ], $settings),
        ]);
        $this->actingAs($user);
        return compact('user', 'merchant');
    }

    private function createMember(Merchant $merchant, array $attrs = []): Member
    {
        return Member::factory()->create(array_merge(['merchant_id' => $merchant->id], $attrs));
    }

    // ── public_uuid assignment ────────────────────────────────

    public function test_member_receives_public_uuid_on_create(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->assertNotNull($member->public_uuid);
        $this->assertTrue(Str::isUuid($member->public_uuid));
    }

    public function test_each_member_gets_unique_public_uuid(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $a = $this->createMember($merchant);
        $b = $this->createMember($merchant);

        $this->assertNotSame($a->public_uuid, $b->public_uuid);
    }

    public function test_portal_enabled_defaults_to_true(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        // DB default is true; fresh() reflects the persisted value
        $this->assertTrue((bool) $member->fresh()->portal_enabled);
    }

    // ── Public portal access ──────────────────────────────────

    public function test_portal_returns_200_for_valid_uuid(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertOk();
    }

    public function test_portal_returns_404_for_invalid_uuid(): void
    {
        $this->get(route('portal.show', Str::uuid()))
            ->assertNotFound();
    }

    public function test_portal_shows_member_name(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['name' => 'Test Customer']);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertOk()
            ->assertSee('Test Customer');
    }

    public function test_portal_shows_merchant_name(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertSee($merchant->name);
    }

    public function test_portal_shows_member_code(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertSee($member->member_code);
    }

    public function test_portal_does_not_expose_member_email(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['email' => 'secret@example.com']);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertDontSee('secret@example.com');
    }

    public function test_portal_does_not_expose_member_phone(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['phone' => '+66812345678']);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertDontSee('+66812345678');
    }

    public function test_portal_does_not_expose_internal_notes(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['notes' => 'VIP SECRET NOTES']);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertDontSee('VIP SECRET NOTES');
    }

    // ── Disabled portal ───────────────────────────────────────

    public function test_disabled_portal_shows_unavailable_message(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['portal_enabled' => false]);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertOk()
            ->assertSee(__('portal.disabled_title'));
    }

    public function test_disabled_portal_does_not_show_member_data(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, [
            'name'           => 'Hidden Customer',
            'portal_enabled' => false,
        ]);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertDontSee('Hidden Customer');
    }

    // ── Cross-tenant isolation ────────────────────────────────

    public function test_portal_only_shows_own_merchant_data(): void
    {
        ['merchant' => $merchant1] = $this->actingAsMerchant();
        $user2     = User::factory()->create();
        $merchant2 = Merchant::factory()->create(['user_id' => $user2->id]);
        $member1   = $this->createMember($merchant1, ['name' => 'Merchant One Customer']);
        $member2   = $this->createMember($merchant2, ['name' => 'Merchant Two Customer']);

        // Merchant1's member portal should not show merchant2 data
        $this->get(route('portal.show', $member1->public_uuid))
            ->assertSee('Merchant One Customer')
            ->assertDontSee('Merchant Two Customer');
    }

    public function test_member_uuid_from_one_merchant_cannot_reveal_another_merchant(): void
    {
        ['merchant' => $merchant1] = $this->actingAsMerchant();
        $user2     = User::factory()->create();
        $merchant2 = Merchant::factory()->create(['user_id' => $user2->id]);
        $member2   = $this->createMember($merchant2);

        // Accessing member2's UUID shows member2's data (with merchant2 context) —
        // not any merchant1 data. This verifies UUIDs are truly scoped.
        $this->get(route('portal.show', $member2->public_uuid))
            ->assertOk()
            ->assertSee($merchant2->name)
            ->assertDontSee($merchant1->name);
    }

    // ── QR code endpoint ──────────────────────────────────────

    public function test_qr_svg_endpoint_returns_svg(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('portal.qr', $member->public_uuid))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_qr_svg_is_deterministic(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $svg1 = $this->get(route('portal.qr', $member->public_uuid))->getContent();
        $svg2 = $this->get(route('portal.qr', $member->public_uuid))->getContent();

        $this->assertSame($svg1, $svg2);
    }

    public function test_qr_svg_returns_404_for_unknown_uuid(): void
    {
        $this->get(route('portal.qr', Str::uuid()))
            ->assertNotFound();
    }

    // ── Digital card endpoint ─────────────────────────────────

    public function test_card_endpoint_returns_200(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('portal.card', $member->public_uuid))
            ->assertOk();
    }

    public function test_card_shows_member_name_and_code(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['name' => 'Card Test Customer']);

        $this->get(route('portal.card', $member->public_uuid))
            ->assertSee('Card Test Customer')
            ->assertSee($member->member_code);
    }

    public function test_card_includes_qr_code_svg(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('portal.card', $member->public_uuid))
            ->assertSee('<svg', false); // QR and barcode are both inlined as SVGs
    }

    // ── Merchant portal controls ──────────────────────────────

    public function test_merchant_can_disable_portal(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['portal_enabled' => true]);

        $this->put(route('members.portal.toggle', $member))
            ->assertRedirect();

        $this->assertFalse((bool) $member->fresh()->portal_enabled);
    }

    public function test_merchant_can_enable_portal(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['portal_enabled' => false]);

        $this->put(route('members.portal.toggle', $member))
            ->assertRedirect();

        $this->assertTrue((bool) $member->fresh()->portal_enabled);
    }

    public function test_portal_toggle_requires_auth(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);
        auth()->logout();

        $this->put(route('members.portal.toggle', $member))
            ->assertRedirect(route('login'));
    }

    public function test_portal_toggle_prevents_cross_merchant_access(): void
    {
        ['merchant' => $merchant1] = $this->actingAsMerchant();
        $user2     = User::factory()->create();
        $merchant2 = Merchant::factory()->create(['user_id' => $user2->id]);
        $member2   = $this->createMember($merchant2);

        $this->put(route('members.portal.toggle', $member2))
            ->assertForbidden();
    }

    public function test_merchant_can_regenerate_qr(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);
        $oldUuid = $member->public_uuid;

        $this->post(route('members.portal.regenerate', $member))
            ->assertRedirect();

        $this->assertNotSame($oldUuid, $member->fresh()->public_uuid);
    }

    public function test_regenerate_prevents_cross_merchant_access(): void
    {
        ['merchant' => $merchant1] = $this->actingAsMerchant();
        $user2     = User::factory()->create();
        $merchant2 = Merchant::factory()->create(['user_id' => $user2->id]);
        $member2   = $this->createMember($merchant2);

        $this->post(route('members.portal.regenerate', $member2))
            ->assertForbidden();
    }

    // ── Analytics tracking ────────────────────────────────────

    public function test_portal_view_tracks_analytics(): void
    {
        $analytics = $this->mock(AnalyticsService::class);
        $analytics->shouldReceive('track')
            ->once()
            ->withArgs(fn ($event) => $event === 'portal_viewed');

        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);
        auth()->logout();

        $this->get(route('portal.show', $member->public_uuid));
    }

    public function test_card_download_tracks_analytics(): void
    {
        $analytics = $this->mock(AnalyticsService::class);
        $analytics->shouldReceive('track')
            ->once()
            ->withArgs(fn ($event) => $event === 'member_card_downloaded');

        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);
        auth()->logout();

        $this->get(route('portal.card', $member->public_uuid));
    }

    // ── Merchant show page portal card ────────────────────────

    public function test_member_show_displays_portal_card(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant);

        $this->get(route('members.show', $member))
            ->assertOk()
            ->assertSee(__('members.portal_card_title'));
    }

    public function test_member_show_displays_portal_link_when_enabled(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['portal_enabled' => true]);

        $this->get(route('members.show', $member))
            ->assertSee(route('portal.show', $member->public_uuid), false);
    }

    // ── CustomerPortalService — email stubs ───────────────────

    public function test_prepare_member_card_email_returns_array_with_portal_url(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member  = $this->createMember($merchant);
        $service = new CustomerPortalService();

        $data = $service->prepareMemberCardEmail($member);

        $this->assertArrayHasKey('portal_url', $data);
        $this->assertArrayHasKey('member_name', $data);
        $this->assertArrayHasKey('member_code', $data);
    }

    public function test_prepare_qr_email_returns_array_with_qr_url(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member  = $this->createMember($merchant);
        $service = new CustomerPortalService();

        $data = $service->prepareQrEmail($member);

        $this->assertArrayHasKey('qr_url', $data);
        $this->assertArrayHasKey('portal_url', $data);
    }

    public function test_prepare_welcome_email_returns_array(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member  = $this->createMember($merchant);
        $service = new CustomerPortalService();

        $data = $service->prepareWelcomeEmail($member);

        $this->assertArrayHasKey('member_name', $data);
        $this->assertArrayHasKey('portal_url', $data);
        $this->assertArrayHasKey('merchant_name', $data);
    }

    // ── Branding on portal ────────────────────────────────────

    public function test_portal_shows_merchant_primary_colour_in_css_vars(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $merchant->update(['brand_color' => '#ff5500']);
        $member = $this->createMember($merchant);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertSee('#ff5500', false);
    }

    // ── Portal rewards (points campaign) ─────────────────────

    public function test_portal_shows_available_rewards(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['total_points' => 500]);

        $program = LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'type'        => 'points',
            'status'      => 'active',
        ]);
        Reward::factory()->create([
            'merchant_id'       => $merchant->id,
            'loyalty_program_id' => $program->id,
            'status'            => 'active',
            'points_required'   => 100,
            'name'              => 'Free Coffee',
        ]);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertSee('Free Coffee');
    }

    public function test_portal_shows_locked_rewards(): void
    {
        ['merchant' => $merchant] = $this->actingAsMerchant();
        $member = $this->createMember($merchant, ['total_points' => 50]);

        $program = LoyaltyProgram::factory()->create([
            'merchant_id' => $merchant->id,
            'type'        => 'points',
            'status'      => 'active',
        ]);
        Reward::factory()->create([
            'merchant_id'        => $merchant->id,
            'loyalty_program_id' => $program->id,
            'status'             => 'active',
            'points_required'    => 500,
            'name'               => 'Locked Reward',
        ]);

        $this->get(route('portal.show', $member->public_uuid))
            ->assertSee('Locked Reward');
    }
}
