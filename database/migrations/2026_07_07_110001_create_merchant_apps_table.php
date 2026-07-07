<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PLATFORM-002 Part 1 — per-merchant App state (version/enabled/config).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('app_key', 50);
            $table->string('version', 20)->default('1.0.0');
            $table->boolean('enabled')->default(true);
            $table->json('config')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();

            $table->unique(['merchant_id', 'app_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_apps');
    }
};
