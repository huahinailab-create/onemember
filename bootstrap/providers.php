<?php

use App\Providers\AppServiceProvider;
use App\Providers\DomainEventServiceProvider;
use App\Providers\MarketplaceServiceProvider;

return [
    AppServiceProvider::class,
    DomainEventServiceProvider::class,
    MarketplaceServiceProvider::class,
];
