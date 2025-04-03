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
            $table->unsignedBigInteger('measurement_unit_id')->nullable()->after('product_qty');
            $table->string('base_unit')->nullable()->after('measurement_unit_id');
            $table->decimal('conversion_factor', 10, 4)->default(1)->after('base_unit');
            $table->boolean('is_weight_based')->default(false)->after('conversion_factor');
            $table->boolean('allow_decimal_qty')->default(false)->after('is_weight_based');
            $table->decimal('min_order_qty', 10, 2)->default(1)->after('allow_decimal_qty');
            $table->decimal('max_order_qty', 10, 2)->nullable()->after('min_order_qty');

            // Add foreign key if using the measurement_units table
            $table->foreign('measurement_unit_id')
                ->references('id')
                ->on('measurement_units')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
