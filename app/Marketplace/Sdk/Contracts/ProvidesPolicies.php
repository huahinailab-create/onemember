<?php

namespace App\Marketplace\Sdk\Contracts;

/**
 * PLATFORM-002 Part 2 — Plugin SDK contract.
 * Implemented by AppProvider subclasses (or standalone app classes) that
 * expose policies(). See docs/dev/plugin-sdk.md.
 */
interface ProvidesPolicies
{
    public function policies(): array;
}
