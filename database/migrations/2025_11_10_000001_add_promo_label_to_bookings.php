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
            // store a human-friendly label (eg: "Senior Citizen", "PWD", "Xmas Sale")
            $table->string('promo_label')->nullable()->after('total_amount');
            // reference to discounts table when applicable
            $table->unsignedBigInteger('discount_id')->nullable()->after('promo_label');
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
