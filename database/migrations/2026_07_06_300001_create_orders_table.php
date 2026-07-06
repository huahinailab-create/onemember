<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// APP-003 — Basic Orders (Domain Model §1.10). The merchant is the seller
// of record; OneMember stores no payment entity — payment_status is the
// merchant's MANUAL confirmation that they received payment directly
// (ADR-011). Totals are snapshots in merchant currency.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_uuid')->unique();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('customer_phone', 30);
            $table->string('fulfillment_type', 20);            // pickup | delivery | shipping
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('placed');   // placed|accepted|ready|completed|cancelled
            $table->string('payment_status', 20)->default('unpaid'); // unpaid|paid (merchant-confirmed)
            $table->decimal('subtotal', 10, 2);
            $table->decimal('fulfillment_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index(['merchant_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);                       // snapshot
            $table->decimal('price', 10, 2);                   // snapshot
            $table->unsignedInteger('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
