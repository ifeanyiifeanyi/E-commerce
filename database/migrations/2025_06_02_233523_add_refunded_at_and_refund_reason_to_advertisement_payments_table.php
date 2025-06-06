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
        Schema::table('advertisement_payments', function (Blueprint $table) {
            $table->datetime('refunded_at')->nullable();
    $table->text('refund_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisement_payments', function (Blueprint $table) {
            $table->dropColumn(['refunded_at', 'refund_reason']);
        });
    }
};
