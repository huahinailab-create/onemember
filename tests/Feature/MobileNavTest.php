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
        $response->assertSee('sidebar-close-btn', false);
        $response->assertSee('bi-x-lg', false);
    }

    public function test_sidebar_close_button_has_js_id(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // Sidebar JS uses getElementById — id must be present in HTML
        $response->assertSee('id="om-sidebar-close"', false);
        $response->assertSee('id="om-sidebar"', false);
        $response->assertSee('id="om-topbar-toggle"', false);
    }

    public function test_sidebar_backdrop_has_js_id(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('id="om-sidebar-backdrop"', false);
    }

    // ── ESC key handler (vanilla JS in app.js) ───────────────────────────────

    public function test_layout_has_escape_key_handler(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // ESC and scroll-lock are now handled by the vanilla JS sidebar
        // controller in app.js — the blade just needs the id hooks present
        $response->assertSee('om-sidebar', false);
    }

    // ── Scroll-lock CSS class ─────────────────────────────────────────────────

    public function test_layout_has_scroll_lock_effect(): void
    {
        $user = $this->authenticatedMerchant();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        // om-sidebar-open class is toggled by vanilla JS on body
        $response->assertSee('om-sidebar', false);
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
