<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PH2-001A — append-only consent ledger (ADR-010, design package Doc 06).
// Rows are never updated or deleted; current state = latest row per
// (customer, merchant, data_type). data_type is a string (field-level
// granularity for scan-to-join: name/phone/email/birthday/postal_code;
// marketing/analytics categories arrive with PH2-001C).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('data_type', 30);
            $table->boolean('granted');
            $table->string('consent_version', 20);
            $table->string('source', 30);                   // registration | scan_to_join | privacy_centre | support
            $table->timestamp('acted_at');
            $table->timestamp('created_at')->nullable();

            $table->index(['customer_id', 'merchant_id', 'data_type', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
