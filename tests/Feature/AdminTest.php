<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DECISION-068 — RELEASE-3A Platform Admin Foundation
 *
 * Verifies that /admin routes are restricted to is_admin users only.
 * Merchants and guests cannot access the admin area.
 */
class AdminTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function adminUser(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function merchantUser(): User
    {
        $user = User::factory()->create(['is_admin' => false]);
        Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
        ]);
        return $user;
    }

    // ── 1. Access control: guest ──────────────────────────────────────────────

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('http://app.onemember.co/admin/dashboard');
        $response->assertRedirect();
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }

    public function test_guest_is_redirected_from_admin_merchants(): void
    {
        $response = $this->get('http://app.onemember.co/admin/merchants');
        $response->assertRedirect();
    }

    // ── 2. Access control: merchant blocked ───────────────────────────────────

    public function test_merchant_user_is_denied_admin_dashboard(): void
    {
        $user = $this->merchantUser();
        $response = $this->actingAs($user)->get('http://app.onemember.co/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_merchant_user_is_denied_admin_merchant_list(): void
    {
        $user = $this->merchantUser();
        $response = $this->actingAs($user)->get('http://app.onemember.co/admin/merchants');
        $response->assertStatus(403);
    }

    public function test_merchant_user_is_denied_admin_merchant_detail(): void
    {
        $merchant = Merchant::factory()->create();
        $user     = $this->merchantUser();
        $response = $this->actingAs($user)
            ->get('http://app.onemember.co/admin/merchants/' . $merchant->id);
        $response->assertStatus(403);
    }

    // ── 3. Access control: admin allowed ──────────────────────────────────────

    public function test_admin_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/dashboard');
        $response->assertOk();
    }

    public function test_admin_user_can_access_merchant_list(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants');
        $response->assertOk();
    }

    public function test_admin_user_can_access_merchant_detail(): void
    {
        $merchant = Merchant::factory()->create();
        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants/' . $merchant->id);
        $response->assertOk();
    }

    // ── 4. Dashboard metrics ──────────────────────────────────────────────────

    public function test_dashboard_shows_correct_total_merchant_count(): void
    {
        Merchant::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/dashboard');

        $response->assertOk();
        $response->assertSee('3', false);
    }

    public function test_dashboard_shows_correct_total_member_count(): void
    {
        $merchant = Merchant::factory()->create();
        Member::factory()->count(5)->create(['merchant_id' => $merchant->id]);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/dashboard');

        $response->assertOk();
        $response->assertSee('5', false);
    }

    public function test_dashboard_shows_recent_merchant_registrations(): void
    {
        $merchant = Merchant::factory()->create(['name' => 'Test Bistro XYZ']);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Test Bistro XYZ', false);
    }

    public function test_dashboard_contains_admin_layout_markers(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/dashboard');

        $response->assertOk();
        $response->assertSee('ADMIN', false);
        $response->assertSee('OneMember', false);
    }

    // ── 5. Merchant list ──────────────────────────────────────────────────────

    public function test_merchant_list_displays_merchant_names(): void
    {
        Merchant::factory()->create(['name' => 'Lucky Noodle Shop']);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants');

        $response->assertOk();
        $response->assertSee('Lucky Noodle Shop', false);
    }

    public function test_merchant_list_filters_by_search(): void
    {
        Merchant::factory()->create(['name' => 'Alpha Coffee']);
        Merchant::factory()->create(['name' => 'Beta Tea']);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants?search=Alpha');

        $response->assertOk();
        $response->assertSee('Alpha Coffee', false);
        $response->assertDontSee('Beta Tea', false);
    }

    public function test_merchant_list_shows_member_counts(): void
    {
        $merchant = Merchant::factory()->create(['name' => 'Count Test Shop']);
        Member::factory()->count(4)->create(['merchant_id' => $merchant->id]);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants');

        $response->assertOk();
        $response->assertSee('4', false);
    }

    // ── 6. Merchant detail ────────────────────────────────────────────────────

    public function test_merchant_detail_shows_correct_merchant(): void
    {
        $merchant = Merchant::factory()->create(['name' => 'Detail Test Shop']);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants/' . $merchant->id);

        $response->assertOk();
        $response->assertSee('Detail Test Shop', false);
    }

    public function test_merchant_detail_shows_owner_info(): void
    {
        $user     = User::factory()->create(['name' => 'Detail Owner']);
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants/' . $merchant->id);

        $response->assertOk();
        $response->assertSee('Detail Owner', false);
    }

    public function test_merchant_detail_shows_member_count(): void
    {
        $merchant = Merchant::factory()->create(['name' => 'Member Count Shop']);
        Member::factory()->count(7)->create(['merchant_id' => $merchant->id]);

        $response = $this->actingAs($this->adminUser())
            ->get('http://app.onemember.co/admin/merchants/' . $merchant->id);

        $response->assertOk();
        $response->assertSee('7', false);
    }

    // ── 7. is_admin flag ─────────────────────────────────────────────────────

    public function test_is_admin_defaults_to_false_for_new_users(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->is_admin);
    }

    public function test_is_admin_can_be_set_true(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->assertTrue($user->is_admin);
    }

    public function test_regular_user_without_merchant_is_denied_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $response = $this->actingAs($user)->get('http://app.onemember.co/admin/dashboard');
        $response->assertStatus(403);
    }
}
