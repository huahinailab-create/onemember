<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('brand_color', 7)->default('#2563EB')->after('logo_path');
            $table->string('secondary_color', 7)->default('#1E293B')->after('brand_color');
            $table->string('business_tagline')->nullable()->after('secondary_color');
            $table->text('receipt_footer')->nullable()->after('business_tagline');
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('line_url')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'brand_color', 'secondary_color', 'business_tagline',
                'receipt_footer', 'facebook_url', 'instagram_url', 'line_url',
            ]);
        });
    }
};
