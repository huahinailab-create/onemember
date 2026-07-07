<?php

namespace Tests\Feature;

use App\Jobs\SendWebhook;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/** PLATFORM-002 Part 4 — outbound webhooks: fan-out, signing, retry, logging. */
class WebhookFrameworkTest extends TestCase
{
    use RefreshDatabase;

    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create(['user_id' => $user->id]);
    }

    private function endpoint(array $overrides = []): WebhookEndpoint
    {
        return WebhookEndpoint::create(array_merge([
            'merchant_id' => $this->merchant->id,
            'name'        => 'CRM sync',
            'url'         => 'https://example.test/hooks',
            'secret'      => 'whsec_test',
            'events'      => ['member.created'],
            'active'      => true,
        ], $overrides));
    }

    public function test_domain_event_creates_delivery_and_queues_job_for_subscribed_endpoint(): void
    {
        Queue::fake();
        $endpoint = $this->endpoint();
        $this->endpoint(['events' => ['order.placed'], 'name' => 'other']); // not subscribed

        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->assertSame(1, WebhookDelivery::count());
        $delivery = WebhookDelivery::first();
        $this->assertSame('member.created', $delivery->event);
        $this->assertSame($endpoint->id, $delivery->webhook_endpoint_id);
        Queue::assertPushed(SendWebhook::class, 1);
    }

    public function test_wildcard_subscription_receives_all_events(): void
    {
        Queue::fake();
        $this->endpoint(['events' => ['*']]);

        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        $this->assertSame(1, WebhookDelivery::where('event', 'member.created')->count());
    }

    public function test_webhooks_are_tenant_scoped(): void
    {
        Queue::fake();
        $otherOwner = User::factory()->create();
        $other      = Merchant::factory()->create(['user_id' => $otherOwner->id]);
        $this->endpoint(['merchant_id' => $other->id, 'events' => ['*']]);

        Member::factory()->create(['merchant_id' => $this->merchant->id]);

        // Other merchant's endpoint must NOT receive this merchant's event.
        $this->assertSame(0, WebhookDelivery::count());
    }

    public function test_delivery_is_signed_and_marked_delivered(): void
    {
        Http::fake(['example.test/*' => Http::response('ok', 200)]);
        $endpoint = $this->endpoint();
        $delivery = WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event'               => 'member.created',
            'payload'             => ['member_id' => 1],
        ]);

        (new SendWebhook($delivery))->handle();

        Http::assertSent(function ($request) {
            $timestamp = $request->header('X-OneMember-Timestamp')[0];
            $signature = $request->header('X-OneMember-Signature')[0];

            return $request->url() === 'https://example.test/hooks'
                && $request->header('X-OneMember-Event')[0] === 'member.created'
                && hash_equals(hash_hmac('sha256', $timestamp . '.' . $request->body(), 'whsec_test'), $signature);
        });

        $this->assertSame('delivered', $delivery->fresh()->status);
        $this->assertNotNull($delivery->fresh()->delivered_at);
    }

    public function test_failed_response_throws_for_retry_and_logs_error(): void
    {
        Http::fake(['example.test/*' => Http::response('nope', 500)]);
        $endpoint = $this->endpoint();
        $delivery = WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event'               => 'member.created',
            'payload'             => [],
        ]);

        try {
            (new SendWebhook($delivery))->handle();
            $this->fail('Expected retry exception');
        } catch (\RuntimeException) {
            // expected — triggers the queue retry cycle
        }

        $fresh = $delivery->fresh();
        $this->assertSame('pending', $fresh->status);
        $this->assertSame(1, $fresh->attempts);
        $this->assertSame(500, $fresh->response_code);
    }

    public function test_endpoint_auto_disables_after_consecutive_failures(): void
    {
        $endpoint = $this->endpoint();

        foreach (range(1, WebhookEndpoint::MAX_CONSECUTIVE_FAILURES) as $i) {
            WebhookDelivery::create([
                'webhook_endpoint_id' => $endpoint->id,
                'event'               => 'member.created',
                'payload'             => [],
                'status'              => $i === 1 ? 'pending' : 'failed',
            ]);
        }

        $last = WebhookDelivery::where('status', 'pending')->first();
        (new SendWebhook($last))->failed(new \RuntimeException('HTTP 500'));

        $this->assertSame('failed', $last->fresh()->status);
        $this->assertFalse($endpoint->fresh()->active);
        $this->assertNotNull($endpoint->fresh()->disabled_at);
    }

    public function test_inactive_endpoint_delivery_is_skipped(): void
    {
        Http::fake();
        $endpoint = $this->endpoint(['active' => false]);
        $delivery = WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event'               => 'member.created',
            'payload'             => [],
        ]);

        (new SendWebhook($delivery))->handle();

        Http::assertNothingSent();
        $this->assertSame('failed', $delivery->fresh()->status);
    }
}
