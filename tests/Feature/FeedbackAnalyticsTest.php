<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class FeedbackAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function user(): User
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);
        return $user;
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'category'    => 'general',
            'subject'     => 'Test subject',
            'message'     => 'This is a test feedback message with enough chars.',
            'current_url' => 'http://localhost/dashboard',
            'browser'     => 'TestBrowser/1.0',
        ], $overrides);
    }

    // ---------------------------------------------------------------
    // Feedback submission — authenticated
    // ---------------------------------------------------------------

    public function test_authenticated_user_can_submit_feedback(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload())
             ->assertRedirect();
    }

    public function test_feedback_redirects_with_success_message(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload())
             ->assertSessionHas('success');
    }

    // ---------------------------------------------------------------
    // Feedback storage — JSON file written
    // ---------------------------------------------------------------

    public function test_feedback_writes_json_file_to_storage(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload());

        $files = Storage::disk('local')->files('feedback');
        $this->assertCount(1, $files);
    }

    public function test_feedback_json_file_contains_expected_fields(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload([
                 'category' => 'bug',
                 'subject'  => 'Something broke',
                 'message'  => 'The dashboard shows an error when I log in.',
             ]));

        $files   = Storage::disk('local')->files('feedback');
        $payload = json_decode(Storage::disk('local')->get($files[0]), true);

        $this->assertEquals('bug',                  $payload['category']);
        $this->assertEquals('Something broke',       $payload['subject']);
        $this->assertEquals($user->id,               $payload['user_id']);
        $this->assertArrayHasKey('id',               $payload);
        $this->assertArrayHasKey('submitted_at',     $payload);
        $this->assertArrayHasKey('merchant_id',      $payload);
    }

    // ---------------------------------------------------------------
    // Validation — missing and invalid fields
    // ---------------------------------------------------------------

    public function test_feedback_requires_category(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload(['category' => '']))
             ->assertSessionHasErrors('category');
    }

    public function test_feedback_rejects_invalid_category(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload(['category' => 'complaint']))
             ->assertSessionHasErrors('category');
    }

    public function test_feedback_requires_subject(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload(['subject' => '']))
             ->assertSessionHasErrors('subject');
    }

    public function test_feedback_requires_message_with_minimum_length(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload(['message' => 'short']))
             ->assertSessionHasErrors('message');
    }

    public function test_feedback_rejects_message_over_maximum_length(): void
    {
        Storage::fake('local');

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload(['message' => str_repeat('a', 5001)]))
             ->assertSessionHasErrors('message');
    }

    // ---------------------------------------------------------------
    // Authentication — guest blocked
    // ---------------------------------------------------------------

    public function test_guest_cannot_submit_feedback(): void
    {
        Storage::fake('local');

        $this->post(route('feedback.store'), $this->validPayload())
             ->assertRedirect(route('login'));
    }

    public function test_guest_does_not_write_feedback_file(): void
    {
        Storage::fake('local');

        $this->post(route('feedback.store'), $this->validPayload());

        $files = Storage::disk('local')->files('feedback');
        $this->assertCount(0, $files);
    }

    // ---------------------------------------------------------------
    // AnalyticsService — no-op when disabled
    // ---------------------------------------------------------------

    public function test_analytics_service_is_no_op_when_disabled(): void
    {
        config(['analytics.enabled' => false]);

        $service = app(AnalyticsService::class);

        // Should not throw when called
        $service->track('test_event', ['foo' => 'bar']);
        $service->page('TestPage');

        $this->assertTrue(true); // Reached without exception
    }

    public function test_analytics_service_never_throws_on_exception_method(): void
    {
        config(['analytics.enabled' => true, 'analytics.provider' => 'null']);

        $service = app(AnalyticsService::class);

        $service->exception(new \RuntimeException('Test error'));

        $this->assertTrue(true);
    }

    // ---------------------------------------------------------------
    // AnalyticsService — event dispatched on feedback submission
    // ---------------------------------------------------------------

    public function test_feedback_submission_calls_analytics_track(): void
    {
        Storage::fake('local');

        $mock = Mockery::mock(AnalyticsService::class);
        $mock->shouldReceive('track')
             ->once()
             ->with('feedback_submitted', Mockery::type('array'), Mockery::any(), Mockery::any());
        $this->app->instance(AnalyticsService::class, $mock);

        $user = $this->user();

        $this->actingAs($user)
             ->post(route('feedback.store'), $this->validPayload());
    }

    // ---------------------------------------------------------------
    // AnalyticsService helper methods
    // ---------------------------------------------------------------

    public function test_merchant_activity_score_returns_integer_between_0_and_100(): void
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);

        $service = app(AnalyticsService::class);
        $score   = $service->merchantActivityScore($merchant);

        $this->assertIsInt($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    public function test_activation_metrics_returns_expected_keys(): void
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);

        $service = app(AnalyticsService::class);
        $metrics = $service->activationMetrics($merchant);

        $this->assertArrayHasKey('registered_at',              $metrics);
        $this->assertArrayHasKey('onboarding_completed_at',   $metrics);
        $this->assertArrayHasKey('first_campaign_created_at', $metrics);
        $this->assertArrayHasKey('first_member_added_at',     $metrics);
        $this->assertArrayHasKey('first_purchase_at',         $metrics);
        $this->assertArrayHasKey('first_redemption_at',       $metrics);
        $this->assertArrayHasKey('is_fully_activated',        $metrics);
    }
}
