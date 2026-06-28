<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('purchase_amount', 10, 2)->nullable()->after('loyalty_program_id');
            $table->string('invoice_number', 100)->nullable()->after('purchase_amount');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['purchase_amount', 'invoice_number']);
        });
    }
};
