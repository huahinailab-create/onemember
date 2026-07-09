<?php

namespace App\Marketplace\Sdk\Contracts;

/**
 * PLATFORM-002 Part 2 — Plugin SDK contract.
 * Implemented by AppProvider subclasses (or standalone app classes) that
 * expose dashboardCards(). See docs/dev/plugin-sdk.md.
 */
interface ProvidesDashboardCards
{
    public function dashboardCards(): array;
}
