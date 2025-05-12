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
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->text('device_info')->nullable();
            $table->string('browser_info')->nullable();
            $table->string('os_info')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('registration_source')->nullable();
            $table->string('referral_source')->nullable();
            $table->text('customer_notes')->nullable();
            $table->json('marketing_preferences')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->enum('account_status', ['active', 'inactive', 'suspended', 'banned'])
                ->default('active');
            $table->enum('customer_segment', ['regular', 'premium', 'vip', 'new'])
                ->default('regular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumns([
                'city',
                'state',
                'postal_code',
                'last_login_at',
                'last_login_ip',
                'device_info',
                'browser_info',
                'os_info',
                'latitude',
                'longitude',
                'registration_source',
                'referral_source',
                'customer_notes',
                'marketing_preferences',
                'last_activity_at',
                'account_status',
                'customer_segment',
            ]);
        });
    }
};
