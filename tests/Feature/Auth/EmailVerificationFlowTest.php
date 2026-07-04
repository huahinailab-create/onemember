<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    // ── Registration ──────────────────────────────────────────────────────────

    public function test_unverified_user_redirected_from_dashboard_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_sees_verification_prompt(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertOk();
        $response->assertSee(__('auth.verify_email_heading'));
    }

    // ── Verification ──────────────────────────────────────────────────────────

    public function test_clicking_verification_link_marks_email_verified(): void
    {
        $user = User::factory()->unverified()->create();
        Event::fake();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)->get($url);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    public function test_verification_link_redirects_to_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    // ── Session refresh after verification ────────────────────────────────────

    public function test_verification_prompt_redirects_to_dashboard_after_email_is_verified(): void
    {
        // Simulates the original-browser tab refreshing after verification happened elsewhere
        $user = User::factory()->unverified()->create();

        // User is on verify-email page (unverified)
        $this->actingAs($user)->get('/verify-email')->assertOk();

        // Email is marked verified externally (e.g. different browser)
        $user->markEmailAsVerified();

        // Same session, page refresh — should now redirect to dashboard
        $response = $this->actingAs($user)->get('/verify-email');
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_status_endpoint_returns_verified_false_for_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->getJson('/verify-email/status');

        $response->assertOk()->assertJson(['verified' => false]);
    }

    public function test_status_endpoint_returns_verified_true_after_verification(): void
    {
        $user = User::factory()->unverified()->create();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->getJson('/verify-email/status');

        $response->assertOk()->assertJson(['verified' => true]);
    }

    // ── Dashboard access ──────────────────────────────────────────────────────

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Either renders dashboard or redirects to onboarding — both are valid for a verified user
        $this->assertContains($response->getStatusCode(), [200, 302]);
        if ($response->getStatusCode() === 302) {
            $this->assertStringContainsString('onboarding', $response->headers->get('Location'));
        }
    }

    public function test_verified_user_is_not_redirected_to_verification_notice(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');

        $this->assertNotEquals(route('verification.notice'), $response->headers->get('Location'));
    }

    // ── Onboarding gate ───────────────────────────────────────────────────────

    public function test_verified_user_without_merchant_redirected_to_onboarding(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('onboarding.index'));
    }

    public function test_unverified_user_cannot_access_onboarding(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/onboarding');

        $response->assertRedirect(route('verification.notice'));
    }
}
