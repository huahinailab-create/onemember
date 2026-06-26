<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_program_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('discount');   // RewardType enum
            $table->unsignedInteger('points_required');
            $table->decimal('value', 10, 2)->default(0);  // monetary value or discount amount
            $table->string('image_path')->nullable();
            $table->unsignedInteger('quantity_available')->nullable(); // null = unlimited
            $table->unsignedInteger('quantity_redeemed')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['loyalty_program_id', 'is_active']);
            $table->index(['merchant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
