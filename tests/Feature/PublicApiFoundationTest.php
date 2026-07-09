<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** PLATFORM-002 Part 5 — API keys, auth, abilities, pagination, errors, tenancy. */
class PublicApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    private Merchant $merchant;
    private string $plaintext;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create(['user_id' => $user->id]);

        [, $this->plaintext] = ApiKey::generate($this->merchant, 'test key', ['members:read']);
    }

    public function test_ping_is_public(): void
    {
        $this->getJson('/api/v1/ping')
            ->assertOk()
            ->assertJson(['ok' => true, 'version' => 'v1']);
    }

    public function test_key_is_stored_hashed_with_prefix_only(): void
    {
        $key = ApiKey::first();

        $this->assertSame(hash('sha256', $this->plaintext), $key->key_hash);
        $this->assertStringStartsWith('om_live_', $key->key_prefix);
        $this->assertStringNotContainsString(substr($this->plaintext, 12), $key->key_prefix);
    }

    public function test_members_requires_api_key(): void
    {
        $this->getJson('/api/v1/members')
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'unauthenticated');
    }

    public function test_members_lists_only_own_tenant_paginated(): void
    {
        Member::factory()->count(3)->create(['merchant_id' => $this->merchant->id]);

        $otherOwner = User::factory()->create();
        $other      = Merchant::factory()->create(['user_id' => $otherOwner->id]);
        Member::factory()->create(['merchant_id' => $other->id, 'name' => 'Foreign Member']);

        $this->withToken($this->plaintext)->getJson('/api/v1/members')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data', 'links', 'meta' => ['current_page', 'total']])
            ->assertJsonMissing(['name' => 'Foreign Member']);
    }

    public function test_show_404s_for_foreign_member_with_error_envelope(): void
    {
        $otherOwner = User::factory()->create();
        $other      = Merchant::factory()->create(['user_id' => $otherOwner->id]);
        $foreign    = Member::factory()->create(['merchant_id' => $other->id]);

        $this->withToken($this->plaintext)->getJson("/api/v1/members/{$foreign->id}")
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'not_found');
    }

    public function test_ability_is_enforced(): void
    {
        [, $limited] = ApiKey::generate($this->merchant, 'no members', ['orders:read']);

        $this->withToken($limited)->getJson('/api/v1/members')
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'forbidden');
    }

    public function test_revoked_key_is_rejected_and_last_used_updates(): void
    {
        $key = ApiKey::first();

        $this->withToken($this->plaintext)->getJson('/api/v1/members')->assertOk();
        $this->assertNotNull($key->fresh()->last_used_at);

        $key->update(['revoked_at' => now()]);

        $this->withToken($this->plaintext)->getJson('/api/v1/members')->assertStatus(401);
    }

    public function test_rate_limit_headers_present(): void
    {
        $this->withToken($this->plaintext)->getJson('/api/v1/members')
            ->assertOk()
            ->assertHeader('X-RateLimit-Limit', 60);
    }
}
