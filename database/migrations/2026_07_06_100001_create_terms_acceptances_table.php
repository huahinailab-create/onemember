<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CORE-001 — versioned, append-only record of merchant terms acceptance
// (GLOBAL-001 §10 legal requirement). `document` allows per-document
// granularity later; v1 records one bundled acceptance.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('document', 50);        // merchant-terms-bundle | privacy | ...
            $table->string('version', 30);         // e.g. v1-draft-2026-07
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('accepted_at');
            $table->timestamp('created_at')->nullable();

            $table->index(['merchant_id', 'document', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms_acceptances');
    }
};
