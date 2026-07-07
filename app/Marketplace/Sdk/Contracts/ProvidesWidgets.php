<?php

namespace App\Marketplace\Sdk\Contracts;

/**
 * PLATFORM-002 Part 2 — Plugin SDK contract.
 * Implemented by AppProvider subclasses (or standalone app classes) that
 * expose widgets(). See docs/dev/plugin-sdk.md.
 */
interface ProvidesWidgets
{
    public function widgets(): array;
}
