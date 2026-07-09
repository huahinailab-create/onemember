<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PLATFORM-002 Part 5 — merchant API keys (hash-at-rest, plaintext shown once).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('key_prefix', 12);          // "om_live_XXXX" hint shown in UI
            $table->string('key_hash', 64)->unique();  // sha256 of the full key
            $table->json('abilities');                 // e.g. ["members:read"] or ["*"]
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['merchant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
