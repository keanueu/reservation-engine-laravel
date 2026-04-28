<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boat_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('boat_bookings', 'group_id')) {
                $table->uuid('group_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('boat_bookings', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('boat_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('boat_bookings', 'group_id')) {
                $table->dropColumn('group_id');
            }

            if (Schema::hasColumn('boat_bookings', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
