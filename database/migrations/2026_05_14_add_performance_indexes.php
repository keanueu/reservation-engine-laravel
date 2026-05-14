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
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('payment_status');
        });

        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->index('group_id');
            $table->index('email');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
        });

        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->dropIndex(['group_id']);
            $table->dropIndex(['email']);
            $table->dropIndex(['payment_status']);
        });
    }
};
