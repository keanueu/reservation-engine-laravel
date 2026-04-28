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
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['user_id', 'id'], 'messages_user_id_id_index');
            $table->index(['session_id', 'created_at'], 'messages_session_created_at_index');
            $table->index(['sender', 'requires_admin', 'created_at'], 'messages_sender_requires_admin_created_at_index');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('created_at', 'bookings_created_at_index');
            $table->index('paid_at', 'bookings_paid_at_index');
            $table->index('status', 'bookings_status_index');
        });

        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->index('created_at', 'boat_bookings_created_at_index');
            $table->index('paid_at', 'boat_bookings_paid_at_index');
            $table->index('status', 'boat_bookings_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_user_id_id_index');
            $table->dropIndex('messages_session_created_at_index');
            $table->dropIndex('messages_sender_requires_admin_created_at_index');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_created_at_index');
            $table->dropIndex('bookings_paid_at_index');
            $table->dropIndex('bookings_status_index');
        });

        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->dropIndex('boat_bookings_created_at_index');
            $table->dropIndex('boat_bookings_paid_at_index');
            $table->dropIndex('boat_bookings_status_index');
        });
    }
};
