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
        if (Schema::hasTable('images')) {
            Schema::table('images', function (Blueprint $table) {
                if (!Schema::hasColumn('images', 'room_id')) {
                    $table->unsignedBigInteger('room_id')->nullable()->after('image');
                    // optional FK - if your `rooms` table uses bigIncrements
                    $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('images')) {
            Schema::table('images', function (Blueprint $table) {
                if (Schema::hasColumn('images', 'room_id')) {
                    $table->dropForeign(['room_id']);
                    $table->dropColumn('room_id');
                }
            });
        }
    }
};
