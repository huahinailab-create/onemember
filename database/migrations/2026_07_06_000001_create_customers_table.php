<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PH2-001A — OneMember Identity (ADR-008/ADR-010, design package Doc 03).
// One verified mobile phone = one global identity; duplicates prohibited.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_uuid')->unique();
            $table->string('onemember_id', 20)->unique();   // permanent, human-readable, on the card
            $table->string('name', 150);
            $table->string('phone', 30)->unique();          // E.164-normalised identity anchor
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->char('locale', 2)->default('th');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
