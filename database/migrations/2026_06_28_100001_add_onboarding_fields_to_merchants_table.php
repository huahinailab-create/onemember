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
            $table->string('business_type')->nullable()->after('contact_person');
            $table->string('website')->nullable()->after('phone');
            $table->timestamp('onboarding_completed_at')->nullable()->after('settings');
        });

        // Existing merchants are already configured — mark them as onboarding complete.
        DB::table('merchants')->whereNotNull('name')->update([
            'onboarding_completed_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'website', 'onboarding_completed_at']);
        });
    }
};
