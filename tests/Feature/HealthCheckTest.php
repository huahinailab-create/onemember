<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_200(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_health_endpoint_returns_json(): void
    {
        $this->get('/up')->assertJsonStructure([
            'status',
            'app',
            'environment',
            'timestamp',
            'version',
        ]);
    }

    public function test_health_endpoint_status_is_ok(): void
    {
        $this->get('/up')->assertJson(['status' => 'ok']);
    }

    public function test_health_endpoint_exposes_no_sensitive_data(): void
    {
        $response = $this->get('/up');
        $json     = $response->json();

        $this->assertArrayNotHasKey('key',      $json);
        $this->assertArrayNotHasKey('password', $json);
        $this->assertArrayNotHasKey('database', $json);
        $this->assertArrayNotHasKey('secret',   $json);
    }

    public function test_health_endpoint_requires_no_authentication(): void
    {
        // Access as guest (no actingAs)
        $this->get('/up')->assertOk();
    }
}
