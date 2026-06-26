<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reward_id')->nullable()->constrained()->nullOnDelete(); // for type=reward
            $table->string('name');
            $table->string('type');                          // BirthdayRewardType enum
            $table->unsignedInteger('value')->default(0);   // points amount or discount percentage
            $table->unsignedSmallInteger('valid_days_before')->default(0);  // days before birthday to unlock
            $table->unsignedSmallInteger('valid_days_after')->default(7);   // days after birthday to redeem
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['merchant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_rewards');
    }
};
