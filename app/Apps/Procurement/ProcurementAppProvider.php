<?php

namespace App\Apps\Procurement;

use App\Marketplace\Sdk\AppProvider;

/** PLATFORM-002 Part 9 — Procurement App SDK provider. */
class ProcurementAppProvider extends AppProvider
{
    public function key(): string
    {
        return 'procurement';
    }

    public function routesFile(): ?string
    {
        return __DIR__ . '/routes/web.php';
    }

    public function navigation(): array
    {
        return [
            ['route' => 'procurement.index', 'icon' => 'bi-truck', 'label' => 'apps.name_procurement'],
        ];
    }

    public function permissions(): array
    {
        return ['procurement.suppliers.manage', 'procurement.requests.manage', 'procurement.orders.approve'];
    }
}
