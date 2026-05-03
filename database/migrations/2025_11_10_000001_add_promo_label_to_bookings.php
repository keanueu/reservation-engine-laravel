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
            if (!Schema::hasColumn('bookings', 'promo_label')) {
                $table->string('promo_label')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('bookings', 'discount_id')) {
                $table->unsignedBigInteger('discount_id')->nullable()->after('promo_label');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['promo_label', 'discount_id']);
        });
    }
};
