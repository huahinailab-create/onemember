<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CUSTOMER-001A — extends the existing PH2-001A identity record (ADR-010:
// one person, one Customer) into an authenticatable account. Additive only:
// every new column is nullable or defaulted, existing rows and the
// merchant-side member links are untouched.
//
// Two deliberate relaxations of PH2-001A assumptions:
//  - phone becomes NULLABLE: a customer may now register with email only
//    (the sprint charter requires the customer to choose phone OR email).
//    Phone remains the unique identity anchor WHEN present.
//  - email gains a UNIQUE index: it is now a login identifier. Production
//    deploys must verify no duplicate customer emails exist before running
//    this migration (dev/pilot data predates any email collection).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Structured name (existing `name` column stays the canonical
            // full name shown on the OneMember card; kept in sync on save)
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('nickname', 100)->nullable()->after('last_name');
            $table->string('display_name', 150)->nullable()->after('nickname');

            // Account & locale
            $table->char('country', 2)->default('TH')->after('locale');
            $table->string('timezone', 64)->nullable()->after('country');

            // Authentication
            $table->string('password')->nullable()->after('email'); // null = OTP-only account
            $table->rememberToken()->after('password');
            $table->timestamp('email_verified_at')->nullable()->after('remember_token');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('last_login_at')->nullable()->after('phone_verified_at');
            $table->string('status', 20)->default('active')->after('last_login_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->change();
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn([
                'first_name', 'last_name', 'nickname', 'display_name',
                'country', 'timezone', 'password', 'remember_token',
                'email_verified_at', 'phone_verified_at', 'last_login_at', 'status',
            ]);
        });
        // phone nullability is not reverted: rows created without a phone
        // would make a NOT NULL change fail. Acceptable for a down() path.
    }
};
