<?php

namespace App\Apps\Queue;

use App\Marketplace\Sdk\AppProvider;

/** PLATFORM-002 Part 8 — Queue App SDK provider (reference implementation). */
class QueueAppProvider extends AppProvider
{
    public function key(): string
    {
        return 'queue';
    }

    public function routesFile(): ?string
    {
        return __DIR__ . '/routes/web.php';
    }

    public function navigation(): array
    {
        return [
            ['route' => 'queue.tickets.index', 'icon' => 'bi-people-fill', 'label' => 'apps.name_queue'],
        ];
    }

    public function permissions(): array
    {
        return ['queue.tickets.manage', 'queue.counters.manage'];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'avg_service_minutes', 'type' => 'integer', 'label' => 'queue.setting_avg_service', 'default' => 5],
        ];
    }
}
