<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('status');
            $table->string('payment_status')->nullable()->default('pending')->after('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'payment_status']);
        });
    }
};
