<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DECISION-066 — RELEASE-2B Mobile Merchant Experience
 *
 * Verifies that the merchant app layout contains the correct mobile navigation
 * markup: sidebar toggle, close button, ESC handler, language switcher.
 */
class MobileNavTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedMerchant(): User
    {
        $user = User::factory()->create();
        Merchant::factory()->create([
            'user_id'                 => $user->id,
            'onboarding_completed_at' => now(),
            'settings'                => ['onboarding_step' => 5],
        ]);
        return $user;
    }

    // ── Sidebar toggle button ─────────────────────────────────────────────────

    public function test_topbar_has_sidebar_toggle_button(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('class="topbar-toggle"', false);
        $response->assertSee('bi-list', false);
    }

    // ── Mobile close button in sidebar ───────────────────────────────────────

    public function test_sidebar_has_mobile_close_button(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('btn-close-white', false);
    }

    public function test_sidebar_close_button_wired_to_alpine_state(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // Close button must set sidebarOpen = false (not just any @click)
        $response->assertSee('sidebar-close-btn', false);
        $response->assertSee('@click.stop="sidebarOpen = false"', false);
    }

    public function test_nav_links_close_sidebar_on_click(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // All nav links must carry @click="sidebarOpen = false" so sidebar
        // collapses immediately when the user taps a link on mobile.
        $count = substr_count($response->content(), '@click="sidebarOpen = false"');
        // Dashboard, Members, Campaigns, Rewards, Transactions, Reports,
        // Subscription, Settings = 8 links minimum
        $this->assertGreaterThanOrEqual(8, $count, 'All nav links must close the sidebar on click');
    }

    public function test_sidebar_backdrop_closes_sidebar_on_tap(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('sidebar-backdrop', false);
        $response->assertSee('@click="sidebarOpen = false"', false);
    }

    // ── ESC key handler ───────────────────────────────────────────────────────

    public function test_layout_has_escape_key_handler(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('keydown.escape.window', false);
    }

    // ── Scroll-lock Alpine effect ─────────────────────────────────────────────

    public function test_layout_has_scroll_lock_effect(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('om-sidebar-open', false);
    }

    // ── Sidebar aria accessibility ────────────────────────────────────────────

    public function test_sidebar_has_aria_label(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('aria-label=', false);
        $response->assertSee('role="navigation"', false);
    }

    // ── Language switcher ─────────────────────────────────────────────────────

    public function test_language_switcher_rendered_in_app_topbar(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('action="/locale"', false);
        $response->assertSee('lang-switcher-btn', false);
    }

    // ── Domain regression: domain routing still works ─────────────────────────

    public function test_dashboard_not_served_on_corporate_domain(): void
    {
        $response = $this->get('http://onemember.co/dashboard');

        // Corporate domain redirects /dashboard to app domain
        $response->assertRedirect('https://app.onemember.co/dashboard');
    }
}
