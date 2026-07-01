<?php

namespace Tests\Feature\DevTools;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['devtools.enabled' => true]);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_performance_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/performance');
        $response->assertOk();
        $response->assertSee('Performance Tools');
    }

    public function test_run_cache_clear_succeeds(): void
    {
        $response = $this->actingAs($this->user)->post('/dev/performance/run', [
            'command' => 'cache:clear',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_run_disallowed_command_fails(): void
    {
        $response = $this->actingAs($this->user)->post('/dev/performance/run', [
            'command' => 'migrate:fresh',
        ]);
        $response->assertSessionHasErrors('command');
    }

    public function test_feature_flags_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/feature-flags');
        $response->assertOk();
        $response->assertSee('Feature Flags');
        $response->assertSee('DEV_TOOLS_ENABLED');
    }

    public function test_env_inspector_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/env-inspector');
        $response->assertOk();
        $response->assertSee('Environment Inspector');
    }

    public function test_queue_inspector_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/queue-inspector');
        $response->assertOk();
        $response->assertSee('Queue Inspector');
    }

    public function test_demo_reset_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/demo-reset');
        $response->assertOk();
        $response->assertSee('Demo Data Reset');
    }
}
