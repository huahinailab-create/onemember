<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loyalty_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');                        // TransactionType enum
            $table->integer('points');                     // positive = credit, negative = debit
            $table->unsignedInteger('balance_before');
            $table->unsignedInteger('balance_after');
            $table->nullableMorphs('reference');           // polymorphic: redemption, birthday_reward, etc.
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['member_id', 'created_at']);
            $table->index(['merchant_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
