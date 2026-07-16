<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CUSTOMER-001A — one-time passwords for customer authentication and
// contact-change verification. Codes are stored hashed; destination is the
// normalized phone (E.164) or lowercased email the code was sent to. For
// change_email / change_phone the destination IS the pending new value —
// verifying the code applies it, so no separate "pending change" column
// exists anywhere.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('channel', 10);          // sms | email
            $table->string('destination');          // +66812345678 | user@example.com
            $table->string('purpose', 30);          // login | register | change_email | change_phone | password_reset
            $table->string('code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();

            $table->index(['destination', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_otps');
    }
};
