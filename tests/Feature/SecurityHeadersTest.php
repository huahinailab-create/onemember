<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    private function assertSecurityHeaders($response): void
    {
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_security_headers_present_on_guest_page(): void
    {
        $response = $this->get('http://onemember.co/');

        $this->assertSecurityHeaders($response);
    }

    public function test_security_headers_present_on_auth_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $this->assertSecurityHeaders($response);
    }

    public function test_csp_allows_self_and_bunny_fonts(): void
    {
        $response = $this->get('http://onemember.co/');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
    }

    public function test_csp_img_src_allows_blob_for_client_side_image_previews(): void
    {
        // OMEGA-001A media-upload preview renders the selected file via
        // URL.createObjectURL() before upload — blob: must be allowed.
        // Every other directive stays exactly as restrictive as before.
        $response = $this->get('http://onemember.co/');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("img-src 'self' data: blob:", $csp);
        $this->assertStringContainsString("script-src 'self' 'unsafe-inline'", $csp);
        $this->assertStringContainsString("connect-src 'self'", $csp);
        $this->assertStringContainsString("frame-src 'none'", $csp);
    }

    public function test_hsts_not_sent_over_http(): void
    {
        $response = $this->get('http://onemember.co/');

        $response->assertHeaderMissing('Strict-Transport-Security');
    }
}
