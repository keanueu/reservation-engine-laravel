<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->unsignedBigInteger('boat_id')->nullable()->after('room_id');
    });
}
public function down()
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn('boat_id');
    });
}
};
