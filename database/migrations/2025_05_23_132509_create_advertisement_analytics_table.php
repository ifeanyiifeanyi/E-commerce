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
        Schema::create('advertisement_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('vendor_advertisements')->onDelete('cascade');
            $table->date('date');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('ctr', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_analytics');
    }
};
