<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PLATFORM-002 Part 4 — outbound webhook endpoints + delivery log.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('url', 500);
            $table->string('secret', 100);           // HMAC signing key
            $table->json('events');                  // subscribed domain event names, or ['*']
            $table->boolean('active')->default(true);
            $table->timestamp('disabled_at')->nullable(); // auto-disabled after repeated failure
            $table->timestamps();

            $table->index(['merchant_id', 'active']);
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_endpoint_id')->constrained()->cascadeOnDelete();
            $table->string('event', 100);
            $table->json('payload');
            $table->string('status', 20)->default('pending'); // pending|delivered|failed
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['webhook_endpoint_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhook_endpoints');
    }
};
