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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('action_type', ['purchase', 'sale', 'adjustment', 'return', 'count', 'reserve', 'backorder']);
            $table->decimal('quantity_change', 10, 3);
            $table->decimal('previous_quantity', 10, 3);
            $table->decimal('new_quantity', 10, 3);
            $table->string('reference_type')->nullable(); // order, purchase, manual, etc.
            $table->string('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
