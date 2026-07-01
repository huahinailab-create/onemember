<?php

namespace Tests\Feature\DevTools;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevLogViewerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['devtools.enabled' => true]);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_log_viewer_renders(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/logs');
        $response->assertOk();
        $response->assertSee('Log Viewer');
    }

    public function test_log_viewer_with_search_filter(): void
    {
        $response = $this->actingAs($this->user)->get('/dev/logs?search=ERROR&level=error');
        $response->assertOk();
    }

    public function test_clear_logs_empties_file(): void
    {
        $logPath = storage_path('logs/laravel.log');
        file_put_contents($logPath, "Test log line\n");

        $this->actingAs($this->user)->delete('/dev/logs');

        $this->assertEquals('', file_get_contents($logPath));
    }
}
