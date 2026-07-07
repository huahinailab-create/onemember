<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PLATFORM-002 Part 4 — delivers one webhook with retry, signing, logging.
 *
 * Signature: X-OneMember-Signature = HMAC-SHA256(timestamp . '.' . body)
 * with the endpoint secret; X-OneMember-Timestamp carries the timestamp so
 * receivers can reject replays. Retries with exponential backoff; after
 * final failure the delivery is marked failed, and an endpoint that only
 * fails is auto-disabled after MAX_CONSECUTIVE_FAILURES.
 */
class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /** @return list<int> seconds before retries 2..5 */
    public function backoff(): array
    {
        return [30, 120, 600, 3600];
    }

    public function __construct(public readonly WebhookDelivery $delivery)
    {
    }

    public function handle(): void
    {
        $endpoint = $this->delivery->endpoint;

        if (! $endpoint || ! $endpoint->active) {
            $this->delivery->update(['status' => 'failed', 'last_error' => 'endpoint inactive']);

            return;
        }

        $body      = json_encode([
            'event'      => $this->delivery->event,
            'data'       => $this->delivery->payload,
            'created_at' => $this->delivery->created_at->toIso8601String(),
        ]);
        $timestamp = (string) now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp . '.' . $body, $endpoint->secret);

        $this->delivery->increment('attempts');

        $response = Http::timeout(10)
            ->withHeaders([
                'Content-Type'          => 'application/json',
                'User-Agent'            => 'OneMember-Webhooks/1.0',
                'X-OneMember-Event'     => $this->delivery->event,
                'X-OneMember-Timestamp' => $timestamp,
                'X-OneMember-Signature' => $signature,
            ])
            ->withBody($body, 'application/json')
            ->post($endpoint->url);

        if ($response->successful()) {
            $this->delivery->update([
                'status'        => 'delivered',
                'response_code' => $response->status(),
                'delivered_at'  => now(),
                'last_error'    => null,
            ]);

            return;
        }

        $this->delivery->update([
            'response_code' => $response->status(),
            'last_error'    => 'HTTP ' . $response->status(),
        ]);

        // Trigger the queue retry cycle.
        throw new \RuntimeException("Webhook delivery {$this->delivery->id} got HTTP {$response->status()}");
    }

    /** Runs after the final failed attempt (or a thrown non-HTTP error). */
    public function failed(\Throwable $exception): void
    {
        $this->delivery->update([
            'status'     => 'failed',
            'last_error' => mb_substr($exception->getMessage(), 0, 500),
        ]);

        $endpoint = $this->delivery->endpoint;
        if (! $endpoint) {
            return;
        }

        $recentFailures = $endpoint->deliveries()
            ->latest('id')->take(WebhookEndpoint::MAX_CONSECUTIVE_FAILURES)
            ->pluck('status');

        if ($recentFailures->count() === WebhookEndpoint::MAX_CONSECUTIVE_FAILURES
            && $recentFailures->every(fn ($s) => $s === 'failed')) {
            $endpoint->update(['active' => false, 'disabled_at' => now()]);
            Log::warning('Webhook endpoint auto-disabled after consecutive failures', [
                'endpoint_id' => $endpoint->id,
                'merchant_id' => $endpoint->merchant_id,
            ]);
        }
    }
}
