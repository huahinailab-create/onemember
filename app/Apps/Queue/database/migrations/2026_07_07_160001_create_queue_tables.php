<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PLATFORM-002 Part 8 — Queue App: counters + tickets.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);          // "Counter 1", "Pharmacy"
            $table->string('staff_name', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['merchant_id', 'active']);
        });

        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('queue_counter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('number');                    // daily sequence per merchant
            $table->string('type', 20)->default('walk_in');       // walk_in | reservation
            $table->boolean('priority')->default(false);
            $table->string('status', 20)->default('waiting');     // waiting|called|serving|done|no_show|cancelled
            $table->string('customer_name', 150)->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->timestamp('reserved_for')->nullable();        // reservations
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'status', 'priority']);
            $table->index(['merchant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
        Schema::dropIfExists('queue_counters');
    }
};
