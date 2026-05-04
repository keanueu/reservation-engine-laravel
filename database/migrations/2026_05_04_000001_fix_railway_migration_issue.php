<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, mark the problematic migration as complete if it exists in migrations table
        try {
            DB::table('migrations')
                ->where('migration', '2025_11_15_090000_ensure_payment_and_refund_columns')
                ->delete();
            
            // Insert it as completed
            DB::table('migrations')->insert([
                'migration' => '2025_11_15_090000_ensure_payment_and_refund_columns',
                'batch' => DB::table('migrations')->max('batch') ?: 1
            ]);
        } catch (\Exception $e) {
            // Ignore if migrations table doesn't exist or other issues
        }

        // Now add all the columns that the problematic migration was supposed to add
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'payment_id')) {
                    $table->string('payment_id')->nullable();
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

        if (Schema::hasTable('booking_extensions')) {
            Schema::table('booking_extensions', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_extensions', 'payment_id')) {
                    $table->string('payment_id')->nullable();
                }
                if (!Schema::hasColumn('booking_extensions', 'paymongo_refund_id')) {
                    $table->string('paymongo_refund_id')->nullable();
                }
            });
        }

        if (Schema::hasTable('boat_bookings')) {
            Schema::table('boat_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('boat_bookings', 'payment_id')) {
                    $table->string('payment_id')->nullable();
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
        // Remove the migration record
        DB::table('migrations')
            ->where('migration', '2025_11_15_090000_ensure_payment_and_refund_columns')
            ->delete();
    }
};
