<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SCALE-001 index gaps (Scalability Review B-02/B-04).
// Note: the spec's partial-unique on members(merchant_id, phone) is not
// portable to MySQL 8 (no partial indexes); uniqueness stays enforced by
// validation (StoreMemberRequest/UpdateMemberRequest) and a plain composite
// index covers the lookups. Deviation recorded in the Engineering Backlog.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->index(['merchant_id', 'phone'], 'members_merchant_phone_index');
            $table->index(['merchant_id', 'last_activity_at'], 'members_merchant_last_activity_index');
            $table->index(['merchant_id', 'postal_code'], 'members_merchant_postal_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['loyalty_program_id', 'created_at'], 'transactions_program_created_index');
            $table->index(['merchant_id', 'created_at'], 'transactions_merchant_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('members_merchant_phone_index');
            $table->dropIndex('members_merchant_last_activity_index');
            $table->dropIndex('members_merchant_postal_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_program_created_index');
            $table->dropIndex('transactions_merchant_created_index');
        });
    }
};
