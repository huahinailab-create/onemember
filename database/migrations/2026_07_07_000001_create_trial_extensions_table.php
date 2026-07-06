<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// TRIAL-001 — history of admin-granted trial extensions (append-only record).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('days');
            $table->timestamp('previous_trial_ends_at')->nullable();
            $table->timestamp('new_trial_ends_at');
            $table->string('reason', 255);
            $table->timestamp('created_at')->nullable();

            $table->index(['merchant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_extensions');
    }
};
