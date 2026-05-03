<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('boat_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('boat_bookings', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('boat_bookings', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('boat_bookings', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'paid_at']);
        });
    }
};
