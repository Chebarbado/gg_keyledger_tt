<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('image');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('reserved_until')->nullable()->after('status');
            $table->index(['status', 'reserved_until']);
        });

        $availableKeys = (int) DB::table('license_keys')->where('status', 'available')->count();
        $keyProductIds = DB::table('products')->where('type', 'key')->pluck('id');
        $perKey = max(1, intdiv(max($availableKeys, 1), max($keyProductIds->count(), 1)));

        foreach (DB::table('products')->get() as $product) {
            $stock = match ($product->type) {
                'key' => $perKey,
                'topup' => 50,
                default => 20,
            };
            DB::table('products')->where('id', $product->id)->update(['stock' => $stock]);
        }

        // для демо гонки — один товар с единственной единицей
        DB::table('products')->where('sku', 'KEY-GTA5')->update(['stock' => 1]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'reserved_until']);
            $table->dropColumn('reserved_until');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
