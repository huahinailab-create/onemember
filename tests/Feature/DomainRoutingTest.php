<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * DECISION-065 — Domain-Aware Routing
 *
 * Verifies that the three domain groups route correctly:
 *   onemember.co      → corporate website
 *   www.onemember.co  → 301 redirect to onemember.co
 *   app.onemember.co  → merchant application
 */
class DomainRoutingTest extends TestCase
{
    // 1. Corporate home is served on onemember.co
    public function test_corporate_home_served_on_corporate_domain(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertStatus(200);
    }

    // 2. www.onemember.co redirects to onemember.co with 301
    public function test_www_redirects_to_corporate_domain(): void
    {
        $response = $this->get('http://www.onemember.co/');

        $response->assertStatus(301);
        $response->assertRedirect('https://onemember.co/');
    }

    // 3. www redirect preserves path
    public function test_www_redirect_preserves_path(): void
    {
        $response = $this->get('http://www.onemember.co/pricing');

        $response->assertStatus(301);
        $response->assertRedirect('https://onemember.co/pricing');
    }

    // 4. /login on corporate domain redirects to app.onemember.co/login
    public function test_corporate_login_redirects_to_app_domain(): void
    {
        $response = $this->get('http://onemember.co/login');

        $response->assertStatus(301);
        $response->assertRedirect('https://app.onemember.co/login');
    }

    // 5. /register on corporate domain redirects to app.onemember.co/register
    public function test_corporate_register_redirects_to_app_domain(): void
    {
        $response = $this->get('http://onemember.co/register');

        $response->assertStatus(301);
        $response->assertRedirect('https://app.onemember.co/register');
    }

    // 6. /dashboard on corporate domain redirects to app.onemember.co/dashboard
    public function test_corporate_dashboard_redirects_to_app_domain(): void
    {
        $response = $this->get('http://onemember.co/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('https://app.onemember.co/dashboard');
    }

    // 7. app.onemember.co root redirects to merchant login
    public function test_app_root_redirects_to_login(): void
    {
        $response = $this->get('http://app.onemember.co/');

        $response->assertRedirect('http://app.onemember.co/login');
    }

    // 8. App routes are NOT served on corporate domain (404)
    public function test_app_routes_not_served_on_corporate_domain(): void
    {
        $response = $this->get('http://onemember.co/members');

        $response->assertStatus(404);
    }
}
