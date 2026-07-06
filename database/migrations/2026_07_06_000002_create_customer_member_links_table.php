<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PH2-001A — bridge between global identity and per-merchant Member records.
// The members table is never altered (ADR-008 "link, don't merge").
// One live link per (customer, merchant) is enforced in IdentityService —
// a partial unique index is not portable to MySQL 8 (same deviation as
// FINAL-002); a plain composite index covers the lookups.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_member_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('linked_via', 30);               // registration | scan_to_join | claim_existing | merchant_invite
            $table->timestamp('linked_at');
            $table->timestamp('unlinked_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'merchant_id']);
            $table->index(['merchant_id', 'unlinked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_member_links');
    }
};
