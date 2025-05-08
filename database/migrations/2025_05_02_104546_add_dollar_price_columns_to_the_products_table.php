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
                $table->decimal('selling_price_usd', 10, 2)->nullable()->after('selling_price');
                $table->decimal('discount_price_usd', 10, 2)->nullable()->after('discount_price');
                $table->string('default_currency')->default('NGN')->after('product_code');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('selling_price_usd');
            $table->dropColumn('discount_price_usd');
            $table->dropColumn('default_currency');
        });
    }
};
