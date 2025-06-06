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
        Schema::create('vendor_advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained('advertisement_packages')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->enum('status', ['pending', 'active', 'paused', 'expired', 'rejected'])->default('pending');
            $table->decimal('amount_paid', 10, 2);
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->datetime('expires_at');
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->boolean('auto_renew')->default(false);
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->decimal('ctr', 8, 4)->default(0.0000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_advertisements');
    }
};
