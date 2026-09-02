<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('type');
            $table->unsignedInteger('price');
            $table->string('currency', 3)->default('RUB');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->unsignedInteger('value');
            $table->string('currency', 3)->nullable();
            $table->unsignedInteger('max_uses');
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('product_id')->constrained();
            $table->string('sku');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('final_amount');
            $table->string('currency', 3)->default('RUB');
            $table->string('status')->default('created');
            $table->string('issued_code')->nullable();
            $table->foreignId('promo_code_id')->nullable()->constrained();
            $table->string('idempotency_key')->nullable()->unique();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('license_keys', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('status')->default('available');
            $table->foreignId('order_id')->nullable()->unique()->constrained();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'id']);
        });

        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('order_public_id');
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('status');
            $table->unsignedInteger('amount');
            $table->string('currency', 3);
            $table->timestamp('event_created_at')->nullable();
            $table->boolean('processed')->default(false);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['order_public_id', 'processed']);
        });

        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->foreignId('order_id')->constrained();
            $table->string('supplier');
            $table->string('status');
            $table->string('code')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['request_id', 'supplier']);
        });

        Schema::create('promo_code_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained();
            $table->foreignId('order_id')->unique()->constrained();
            $table->timestamps();

            $table->unique(['promo_code_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_uses');
        Schema::dropIfExists('delivery_attempts');
        Schema::dropIfExists('payment_events');
        Schema::dropIfExists('license_keys');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('products');
    }
};
