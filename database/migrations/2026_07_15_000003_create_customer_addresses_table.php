<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CUSTOMER-001B — the customer's permanent address book. Addresses belong
// ONLY to the customer (never to a merchant): merchants receive a plain-text
// snapshot of the one address chosen for an order, never a reference into
// this table. Administrative areas are generic (admin_area_1 = largest …
// admin_area_4 = smallest) so no country's structure is hardcoded; the
// meaning of each level lives in config/customer_address.php.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('label', 50);                       // Home, Work, ที่ทำงาน …
            $table->string('recipient_name', 150);
            $table->string('phone', 30)->nullable();           // E.164 when normalizable

            $table->char('country', 2);                        // ISO 3166-1 alpha-2
            $table->string('admin_area_1', 120)->nullable();   // province / state / region
            $table->string('admin_area_2', 120)->nullable();   // district / county
            $table->string('admin_area_3', 120)->nullable();   // subdistrict / township
            $table->string('admin_area_4', 120)->nullable();   // ward / village
            $table->string('postal_code', 16)->nullable();

            $table->string('line1', 255);                      // street address
            $table->string('line2', 255)->nullable();
            $table->string('building', 120)->nullable();
            $table->string('floor', 20)->nullable();
            $table->string('unit', 50)->nullable();            // room / unit
            $table->string('landmark', 255)->nullable();
            $table->string('delivery_instructions', 500)->nullable();

            // Reserved for future GPS/pin features — never required (charter).
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);       // false = archived

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'is_default']);
            $table->index(['customer_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
