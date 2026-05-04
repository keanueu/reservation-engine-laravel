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
                if (!Schema::hasColumn('bookings', 'deposit_amount')) {
                    $table->decimal('deposit_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('bookings', 'deposit_fee')) {
                    $table->decimal('deposit_fee', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('bookings', 'total_to_charge')) {
                    $table->decimal('total_to_charge', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refund_status')) {
                    $table->string('refund_status')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refund_requested_amount')) {
                    $table->decimal('refund_requested_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refund_fee')) {
                    $table->decimal('refund_fee', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refund_amount')) {
                    $table->decimal('refund_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refund_reason')) {
                    $table->text('refund_reason')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refunded_at')) {
                    $table->timestamp('refunded_at')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'paymongo_refund_id')) {
                    $table->string('paymongo_refund_id')->nullable();
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
                    $table->string('paymongo_refund_id')->nullable();
                }
            });
        }

        // boat_bookings table
        if (Schema::hasTable('boat_bookings')) {
            Schema::table('boat_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('boat_bookings', 'payment_id')) {
                    $table->string('payment_id')->nullable()->after('status');
                }
                if (!Schema::hasColumn('boat_bookings', 'deposit_amount')) {
                    $table->decimal('deposit_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('boat_bookings', 'deposit_fee')) {
                    $table->decimal('deposit_fee', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('boat_bookings', 'total_to_charge')) {
                    $table->decimal('total_to_charge', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('boat_bookings', 'paymongo_refund_id')) {
                    $table->string('paymongo_refund_id')->nullable();
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
                foreach ([
                    'refunded_at',
                    'refund_reason',
                    'refund_amount',
                    'refund_fee',
                    'refund_requested_amount',
                    'refund_status',
                    'total_to_charge',
                    'deposit_fee',
                    'deposit_amount',
                ] as $column) {
                    if (Schema::hasColumn('bookings', $column)) {
                        $table->dropColumn($column);
                    }
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
                foreach (['total_to_charge', 'deposit_fee', 'deposit_amount'] as $column) {
                    if (Schema::hasColumn('boat_bookings', $column)) {
                        $table->dropColumn($column);
                    }
                }
                if (Schema::hasColumn('boat_bookings', 'payment_id')) {
                    $table->dropColumn('payment_id');
                }
            });
        }
    }
};
