<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->nullable()->after('product_qty');
            $table->boolean('enable_stock_alerts')->default(false)->after('low_stock_threshold');
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'backordered', 'discontinued'])->default('in_stock')->after('enable_stock_alerts');
            $table->boolean('allow_backorders')->default(false)->after('stock_status');
            $table->boolean('track_inventory')->default(true)->after('allow_backorders');
            $table->integer('reserved_qty')->default(0)->after('track_inventory');
            $table->dateTime('stock_last_updated')->nullable()->after('reserved_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'low_stock_threshold',
                'enable_stock_alerts',
                'stock_status',
                'allow_backorders',
                'track_inventory',
                'reserved_qty',
                'stock_last_updated',
            ]);
        });
    }
};
