<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CUSTOMER-001C — OneMember Wallet MVP.
// customers.preferences: extensible JSON for customer-level preferences
// (communication channel, marketing consent today; notification settings
// later) so future preference features need no further schema change.
// orders.customer_id: orders placed while signed in belong to the wallet —
// genuine order history, no guessing by phone match. Guest orders keep NULL.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->json('preferences')->nullable()->after('locale');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('member_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('preferences');
        });
    }
};
