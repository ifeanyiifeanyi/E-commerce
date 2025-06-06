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
        Schema::create('advertisement_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('vendor_advertisements')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['card', 'bank_transfer', 'wallet']);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('payment_data')->nullable(); // Store payment gateway response
            $table->datetime('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_payments');
    }
};
