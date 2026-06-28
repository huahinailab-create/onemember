<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('subscription_plan', 50)->default('professional')->after('onboarding_completed_at');
            $table->string('subscription_status', 50)->default('trial')->after('subscription_plan');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
        });

        // Seed existing merchants onto a fresh 30-day trial.
        // They are development/test records; trial will be managed properly
        // once billing is implemented.
        DB::table('merchants')
            ->whereNull('trial_ends_at')
            ->update(['trial_ends_at' => now()->addDays(30)]);
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'subscription_status', 'trial_ends_at']);
        });
    }
};
