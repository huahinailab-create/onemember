<?php

namespace App\Marketplace\Sdk\Contracts;

/**
 * PLATFORM-002 Part 2 — Plugin SDK contract.
 * Implemented by AppProvider subclasses (or standalone app classes) that
 * expose migrationsPath(). See docs/dev/plugin-sdk.md.
 */
interface ProvidesMigrations
{
    public function migrationsPath(): ?string;
}
