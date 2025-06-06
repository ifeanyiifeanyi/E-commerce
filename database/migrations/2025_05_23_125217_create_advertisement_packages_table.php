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
        Schema::create('advertisement_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('location', ['home_banner', 'home_sidebar', 'category_top', 'product_detail', 'search_results']);
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->integer('max_slots')->default(1); // How many ads can run simultaneously
            $table->json('features')->nullable(); // JSON array of features
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_packages');
    }
};
