<?php

namespace App\Webhooks;

use App\Events\Domain\DomainEvent;
use App\Jobs\SendWebhook;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;

/**
 * PLATFORM-002 Part 4 — fans domain events out to subscribed endpoints.
 *
 * Registered as a wildcard listener on 'App\Events\Domain\*'; creates one
 * delivery row per matching endpoint and queues SendWebhook for each.
 * Tenant-scoped by construction: only the event merchant's endpoints match.
 */
class WebhookDispatcher
{
    public function handle(string $eventName, array $data): void
    {
        $event = $data[0] ?? null;

        if (! $event instanceof DomainEvent || $event->merchantId() === null) {
            return;
        }

        WebhookEndpoint::where('merchant_id', $event->merchantId())
            ->where('active', true)
            ->get()
            ->filter(fn (WebhookEndpoint $endpoint) => $endpoint->subscribesTo($event->name()))
            ->each(function (WebhookEndpoint $endpoint) use ($event) {
                $delivery = WebhookDelivery::create([
                    'webhook_endpoint_id' => $endpoint->id,
                    'event'               => $event->name(),
                    'payload'             => $event->payload(),
                ]);

                SendWebhook::dispatch($delivery);
            });
    }
}
