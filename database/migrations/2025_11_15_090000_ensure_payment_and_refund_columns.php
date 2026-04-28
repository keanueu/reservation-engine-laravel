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
        // bookings table
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'payment_id')) {
                    $table->string('payment_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('bookings', 'paymongo_refund_id')) {
                    $table->string('paymongo_refund_id')->nullable()->after('refund_amount');
                }
            });
        }

        // booking_extensions table
        if (Schema::hasTable('booking_extensions')) {
            Schema::table('booking_extensions', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_extensions', 'payment_id')) {
                    $table->string('payment_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('booking_extensions', 'paymongo_refund_id')) {
                    $table->string('paymongo_refund_id')->nullable()->after('payment_id');
                }
            });
        }

        // boat_bookings table
        if (Schema::hasTable('boat_bookings')) {
            Schema::table('boat_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('boat_bookings', 'payment_id')) {
                    $table->string('payment_id')->nullable()->after('status');
                }
                if (!Schema::hasColumn('boat_bookings', 'paymongo_refund_id')) {
                    $table->string('paymongo_refund_id')->nullable()->after('payment_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'paymongo_refund_id')) {
                    $table->dropColumn('paymongo_refund_id');
                }
                if (Schema::hasColumn('bookings', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
            });
        }

        if (Schema::hasTable('booking_extensions')) {
            Schema::table('booking_extensions', function (Blueprint $table) {
                if (Schema::hasColumn('booking_extensions', 'paymongo_refund_id')) {
                    $table->dropColumn('paymongo_refund_id');
                }
                if (Schema::hasColumn('booking_extensions', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
            });
        }

        if (Schema::hasTable('boat_bookings')) {
            Schema::table('boat_bookings', function (Blueprint $table) {
                if (Schema::hasColumn('boat_bookings', 'paymongo_refund_id')) {
                    $table->dropColumn('paymongo_refund_id');
                }
                if (Schema::hasColumn('boat_bookings', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
            });
        }
    }
};
