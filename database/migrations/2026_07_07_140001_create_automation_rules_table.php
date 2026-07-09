<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PLATFORM-002 Part 6 — merchant automation rules (WHEN event IF conditions THEN actions).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('trigger_event', 100);   // domain event name, e.g. 'member.created'
            $table->json('conditions');             // [{field, operator, value}] — ANDed
            $table->json('actions');                // [{type, params}]
            $table->boolean('enabled')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->unsignedInteger('run_count')->default(0);
            $table->timestamps();

            $table->index(['merchant_id', 'trigger_event', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
