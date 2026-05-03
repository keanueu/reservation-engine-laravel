<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tracks when the hold-expiring warning email was sent
            // Null = not yet warned. Set to prevent duplicate warnings.
            $table->timestamp('warned_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('warned_at');
        });
    }
};
