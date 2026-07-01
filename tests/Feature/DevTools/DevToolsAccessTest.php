<?php

namespace Tests\Feature\DevTools;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevToolsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_dev_tools_blocked_when_flag_disabled(): void
    {
        config(['devtools.enabled' => false]);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/dev');

        $response->assertNotFound();
    }

    public function test_dev_tools_blocked_in_production_even_with_flag(): void
    {
        config(['devtools.enabled' => true]);
        app()->detectEnvironment(fn () => 'production');

        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/dev');

        $response->assertNotFound();

        // Restore
        app()->detectEnvironment(fn () => 'testing');
    }

    public function test_dev_tools_accessible_with_flag_in_local_env(): void
    {
        config(['devtools.enabled' => true]);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/dev');

        $response->assertOk();
    }

    public function test_unauthenticated_user_redirected(): void
    {
        config(['devtools.enabled' => true]);

        $response = $this->get('/dev');
        $response->assertRedirect('/login');
    }
}
