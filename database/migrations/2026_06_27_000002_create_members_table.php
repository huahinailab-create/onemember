<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('member_code')->unique();        // QR / barcode identifier
            $table->date('birthday')->nullable();
            $table->string('status')->default('active');   // MemberStatus enum
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('lifetime_points')->default(0);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'status']);
            $table->index(['merchant_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
