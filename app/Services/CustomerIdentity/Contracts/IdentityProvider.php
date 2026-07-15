<?php

namespace App\Services\CustomerIdentity\Contracts;

use App\Models\Customer;

/**
 * CUSTOMER-001A — ARCHITECTURE ONLY, no implementations yet (per charter).
 *
 * The seam future federated sign-in plugs into: Apple, Google, LINE,
 * Facebook, Enterprise SSO. Each provider will implement this contract and
 * register in a `customer_identity.identity_providers` config map; the
 * login screen renders one button per registered provider and a shared
 * callback controller resolves-or-creates the Customer via
 * resolveCustomer(). Provider-account linkage will live in a
 * `customer_identities` table (customer_id, provider, provider_user_id) —
 * deliberately not migrated until the first real provider ships, so the
 * schema can follow that provider's actual claims.
 */
interface IdentityProvider
{
    /** Machine key, e.g. 'google', 'line', 'apple'. */
    public function key(): string;

    /** Where to send the customer to authenticate with the provider. */
    public function redirectUrl(): string;

    /**
     * Handle the provider callback: verify the response, then find the
     * Customer linked to this provider account — or create one from the
     * provider's verified claims (email/phone) under the one-person-
     * one-identity rule (ADR-010).
     */
    public function resolveCustomer(array $callbackInput): Customer;
}
