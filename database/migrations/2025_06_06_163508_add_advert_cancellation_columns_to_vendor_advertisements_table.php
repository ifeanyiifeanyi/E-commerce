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
        Schema::table('vendor_advertisements', function (Blueprint $table) {
           
            $table->text('cancellation_reason')->nullable()->after('rejection_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_advertisements', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancelled_at']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn('cancelled_by');
        });
    }
};
