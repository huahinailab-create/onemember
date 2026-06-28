<?php

namespace Tests\Feature;

use App\Console\Commands\ProcessExpiredTrials;
use App\Enums\MerchantStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\Merchant;
use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class SecurityLoggingTest extends TestCase
{
    use RefreshDatabase;

    private function mockSecurityLogger(): Mockery\MockInterface
    {
        $mock = Mockery::mock(SecurityLogger::class);
        $this->app->instance(SecurityLogger::class, $mock);
        return $mock;
    }

    // ---------------------------------------------------------------
    // Authentication events via the event subscriber
    // ---------------------------------------------------------------

    public function test_successful_login_creates_one_security_log(): void
    {
        $user = User::factory()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('loginSucceeded')
             ->once()
             ->with($user->id, $user->email, null);

        event(new Login('web', $user, false));
    }

    public function test_failed_login_creates_one_security_log(): void
    {
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('loginFailed')
             ->once()
             ->with('unknown@example.com');

        event(new Failed('web', null, ['email' => 'unknown@example.com']));
    }

    public function test_logout_creates_one_security_log(): void
    {
        $user = User::factory()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('logout')
             ->once()
             ->with($user->id, $user->email, null);

        event(new Logout('web', $user));
    }

    public function test_password_reset_request_creates_one_security_log(): void
    {
        $user = User::factory()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('passwordResetRequested')
             ->once()
             ->with($user->email);

        event(new PasswordResetLinkSent($user));
    }

    public function test_password_reset_completion_creates_one_security_log(): void
    {
        $user = User::factory()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('passwordResetCompleted')
             ->once()
             ->with($user->id, $user->email, null);

        event(new PasswordReset($user));
    }

    public function test_email_verification_creates_one_security_log(): void
    {
        $user = User::factory()->unverified()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('emailVerified')
             ->once()
             ->with($user->id, $user->email, null);

        event(new Verified($user));
    }

    public function test_merchant_registration_creates_one_security_log(): void
    {
        $user = User::factory()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('merchantRegistered')
             ->once()
             ->with($user->id, $user->email);

        event(new Registered($user));
    }

    // ---------------------------------------------------------------
    // Password changed (model observer)
    // ---------------------------------------------------------------

    public function test_password_change_creates_one_security_log(): void
    {
        $user = User::factory()->create();
        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('passwordChanged')
             ->once()
             ->with($user->id, $user->email, null);

        $user->update(['password' => bcrypt('NewStr0ng!Pass#2')]);
    }

    // ---------------------------------------------------------------
    // Trial expiration command
    // ---------------------------------------------------------------

    public function test_trial_expiration_command_logs_expiration(): void
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'             => $user->id,
            'subscription_status' => SubscriptionStatus::Trial,
            'subscription_plan'   => SubscriptionPlan::Professional,
            'trial_ends_at'       => now()->subDay(),
        ]);

        $mock = $this->mockSecurityLogger();

        $mock->shouldReceive('trialExpired')
             ->once()
             ->with($merchant->id, $merchant->name, 'professional');

        $mock->shouldReceive('subscriptionStatusChanged')
             ->once()
             ->with($merchant->id, 'trial', 'expired');

        $mock->shouldReceive('subscriptionPlanChanged')
             ->once()
             ->with($merchant->id, 'professional', 'free');

        $this->artisan(ProcessExpiredTrials::class)->assertSuccessful();

        $merchant->refresh();
        $this->assertEquals(SubscriptionStatus::Expired, $merchant->subscription_status);
        $this->assertEquals(SubscriptionPlan::Free, $merchant->subscription_plan);
    }

    // ---------------------------------------------------------------
    // SecurityLogger writes to the security channel
    // ---------------------------------------------------------------

    public function test_security_logger_writes_to_security_channel(): void
    {
        Log::shouldReceive('channel')
            ->with('security')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with('auth.login.succeeded', Mockery::type('array'));

        $logger = new SecurityLogger();
        $logger->loginSucceeded(1, 'test@example.com', null);
    }
}
