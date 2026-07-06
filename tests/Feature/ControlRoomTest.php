<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ControlRoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControlRoomTest extends TestCase
{
    use RefreshDatabase;

    private ControlRoomService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ControlRoomService::class);
    }

    private function internal(string $key): array
    {
        return collect($this->service->internal())->firstWhere('key', $key);
    }

    private function external(string $name): array
    {
        return collect($this->service->external())->firstWhere('service', $name);
    }

    // ── Access control ───────────────────────────────────────────────────

    public function test_admin_can_open_control_room(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);

        $this->actingAs($admin)
            ->get(route('admin.control-room', absolute: false))
            ->assertOk()
            ->assertSee('OneMember Control Room')
            ->assertSee('External Services');
    }

    public function test_merchant_cannot_open_control_room(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('admin.control-room', absolute: false))
            ->assertForbidden();
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('admin.control-room', absolute: false))->assertRedirect();
    }

    // ── Internal checks ──────────────────────────────────────────────────

    public function test_internal_checks_cover_required_items(): void
    {
        $keys = collect($this->service->internal())->pluck('key')->all();

        foreach ([
            'app_status', 'version', 'environment', 'debug', 'database', 'queue',
            'failed_jobs', 'scheduler', 'mail', 'resend_domain', 'storage_link',
            'disk', 'backup_path', 'last_backup', 'ssl_domains', 'last_deploy',
        ] as $expected) {
            $this->assertContains($expected, $keys);
        }
    }

    public function test_debug_on_is_critical(): void
    {
        config(['app.debug' => true]);
        $this->assertSame(ControlRoomService::CRITICAL, $this->internal('debug')['status']);

        config(['app.debug' => false]);
        $this->assertSame(ControlRoomService::HEALTHY, $this->internal('debug')['status']);
    }

    public function test_database_check_is_healthy_in_tests(): void
    {
        $this->assertSame(ControlRoomService::HEALTHY, $this->internal('database')['status']);
    }

    public function test_failed_jobs_reflects_table(): void
    {
        $this->assertSame(ControlRoomService::HEALTHY, $this->internal('failed_jobs')['status']);

        \Illuminate\Support\Facades\DB::table('failed_jobs')->insert([
            'uuid' => (string) \Illuminate\Support\Str::uuid(), 'connection' => 'database', 'queue' => 'default',
            'payload' => '{}', 'exception' => 'x', 'failed_at' => now(),
        ]);
        $this->assertSame(ControlRoomService::WARNING, $this->internal('failed_jobs')['status']);
    }

    // ── Feature flags & warnings ────────────────────────────────────────

    public function test_feature_flags_reported(): void
    {
        config(['features.identity' => true]);
        $flags = collect($this->service->featureFlags())->keyBy('key');

        $this->assertTrue($flags->has('identity'));
        $this->assertSame(ControlRoomService::HEALTHY, $flags['identity']['status']);
    }

    public function test_warnings_flag_debug_and_sync_queue(): void
    {
        config(['app.debug' => true, 'queue.default' => 'sync']);
        $warnings = $this->service->warnings();

        $this->assertTrue(collect($warnings)->contains(fn ($w) => str_contains($w, 'APP_DEBUG')));
        $this->assertTrue(collect($warnings)->contains(fn ($w) => str_contains($w, 'sync')));
    }

    // ── External services ────────────────────────────────────────────────

    public function test_external_register_lists_all_services(): void
    {
        $names = collect($this->service->external())->pluck('service')->all();

        foreach (['DigitalOcean', 'Laravel Forge', 'Resend', 'GitHub', 'Cloudflare', 'Stripe', 'Sentry', 'PostHog'] as $svc) {
            $this->assertContains($svc, $names);
        }
    }

    public function test_external_service_without_config_is_manual(): void
    {
        config(['services.resend.key' => null]);
        $this->assertSame(ControlRoomService::MANUAL, $this->external('Resend')['status']);
        $this->assertFalse($this->external('Resend')['configured']);
    }

    public function test_external_service_with_config_is_healthy(): void
    {
        config(['services.resend.key' => 're_test_123']);
        $resend = $this->external('Resend');
        $this->assertSame(ControlRoomService::HEALTHY, $resend['status']);
        $this->assertTrue($resend['configured']);
    }

    public function test_digitalocean_always_manual_in_phase_1(): void
    {
        // No credential detection wired → always manual
        $this->assertSame(ControlRoomService::MANUAL, $this->external('DigitalOcean')['status']);
    }

    public function test_every_external_row_has_purpose_action_and_notes(): void
    {
        foreach ($this->service->external() as $svc) {
            $this->assertNotEmpty($svc['purpose']);
            $this->assertNotEmpty($svc['action']);
            $this->assertNotEmpty($svc['notes']);
        }
    }
}
