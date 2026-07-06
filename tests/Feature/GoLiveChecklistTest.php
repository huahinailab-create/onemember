<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoLiveChecklistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoLiveChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function summary(): array
    {
        return app(GoLiveChecklistService::class)->summary();
    }

    private function check(string $key): array
    {
        return collect($this->summary()['checks'])->firstWhere('key', $key);
    }

    public function test_all_expected_checks_present(): void
    {
        $keys = collect($this->summary()['checks'])->pluck('key')->all();

        foreach ([
            'app_key', 'debug_off', 'mail_configured', 'queue_async', 'storage_linked',
            'backup_path', 'terms_version', 'admin_exists', 'plans_exist',
            'scheduler_documented', 'feature_flags',
        ] as $expected) {
            $this->assertContains($expected, $keys);
        }
    }

    public function test_app_key_and_terms_and_plans_pass_in_test_env(): void
    {
        $this->assertTrue($this->check('app_key')['pass']);
        $this->assertTrue($this->check('terms_version')['pass']);
        $this->assertTrue($this->check('plans_exist')['pass']);
        $this->assertTrue($this->check('feature_flags')['pass']);
    }

    public function test_admin_exists_reflects_reality(): void
    {
        $this->assertFalse($this->check('admin_exists')['pass']);

        User::factory()->create(['is_admin' => true]);
        $this->assertTrue($this->check('admin_exists')['pass']);
    }

    public function test_debug_off_is_critical_and_reflects_config(): void
    {
        config(['app.debug' => true]);
        $c = $this->check('debug_off');
        $this->assertFalse($c['pass']);
        $this->assertTrue($c['critical']);

        config(['app.debug' => false]);
        $this->assertTrue($this->check('debug_off')['pass']);
    }

    public function test_ready_flag_requires_all_critical_checks(): void
    {
        User::factory()->create(['is_admin' => true]);
        config(['app.debug' => false]);

        // storage:link may not exist in CI — ready depends on all criticals
        $summary = $this->summary();
        $this->assertSame($summary['critical_failed'] === [], $summary['ready']);
    }

    public function test_command_runs(): void
    {
        User::factory()->create(['is_admin' => true]);
        config(['app.debug' => false]);

        // Exit code depends on storage link etc.; assert it runs and prints the tally.
        $this->artisan('onemember:go-live-check')
            ->expectsOutputToContain('Passed');
    }

    public function test_admin_page_renders(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);

        $this->actingAs($admin)
            ->get(route('admin.go-live', absolute: false))
            ->assertOk()
            ->assertSee('Go-Live Readiness')
            ->assertSee('Terms version');
    }

    public function test_non_admin_cannot_access_go_live_page(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('admin.go-live', absolute: false))
            ->assertForbidden();
    }
}
