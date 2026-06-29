<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('trial_ends_at');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->string('stripe_price_id')->nullable()->after('stripe_subscription_id');
            $table->string('billing_email')->nullable()->after('stripe_price_id');
            $table->timestamp('subscription_renews_at')->nullable()->after('billing_email');
            $table->boolean('cancel_at_period_end')->default(false)->after('subscription_renews_at');
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_subscription_id',
                'stripe_price_id',
                'billing_email',
                'subscription_renews_at',
                'cancel_at_period_end',
            ]);
        });
    }
};
