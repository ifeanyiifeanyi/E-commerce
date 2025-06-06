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
        Schema::create('advertisement_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('vendor_advertisements')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['expiry_warning', 'expired', 'payment_reminder', 'approved', 'rejected']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->datetime('sent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_notifications');
    }
};
