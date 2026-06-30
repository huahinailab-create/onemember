<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->uuid('public_uuid')->nullable()->unique()->after('member_code');
            $table->boolean('portal_enabled')->default(true)->after('public_uuid');
        });

        // Back-fill public_uuid for all existing members
        DB::table('members')->whereNull('public_uuid')->orderBy('id')->each(function (object $row) {
            DB::table('members')->where('id', $row->id)->update(['public_uuid' => (string) Str::uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['public_uuid', 'portal_enabled']);
        });
    }
};
