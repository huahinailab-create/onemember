<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('points');     // LoyaltyProgramType enum
            $table->text('description')->nullable();
            $table->decimal('points_per_unit', 8, 2)->default(1.00); // points earned per 1 currency unit
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
    }
};
