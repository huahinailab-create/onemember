<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds three composite indexes identified in the Sprint 5.5.4 performance audit.
 *
 * loyalty_programs (merchant_id, status)
 *   — All campaign list and dashboard queries filter by merchant_id + status.
 *     The original (merchant_id, is_active) index covers a legacy column no
 *     longer used for filtering.
 *
 * redemptions (merchant_id, redeemed_at)
 *   — Dashboard KPI "Redeemed Today" queries WHERE merchant_id = ? AND DATE(redeemed_at) = ?.
 *     Without this index the query scans every redemption row for the tenant.
 *
 * members (merchant_id, total_points)
 *   — Dashboard "Top Members" queries ORDER BY total_points DESC LIMIT 5.
 *     Composite index allows the DB to skip a full-tenant sort.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_programs', function (Blueprint $table) {
            $table->index(['merchant_id', 'status'], 'loyalty_programs_merchant_status_index');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->index(['merchant_id', 'redeemed_at'], 'redemptions_merchant_redeemed_at_index');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index(['merchant_id', 'total_points'], 'members_merchant_total_points_index');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_programs', function (Blueprint $table) {
            $table->dropIndex('loyalty_programs_merchant_status_index');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropIndex('redemptions_merchant_redeemed_at_index');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('members_merchant_total_points_index');
        });
    }
};
