<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->string('points_required')->nullable()->change();
            $table->string('status')->default('draft')->after('is_active');
            $table->text('internal_notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['status', 'internal_notes']);
            $table->unsignedInteger('points_required')->nullable(false)->change();
        });
    }
};
