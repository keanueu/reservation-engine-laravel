<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Check if requires_admin exists before adding it
            if (!Schema::hasColumn('messages', 'requires_admin')) {
                $table->boolean('requires_admin')->default(false)->after('message');
            }

            // Check if admin_id exists before adding it
            if (!Schema::hasColumn('messages', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['requires_admin', 'admin_id']);
        });
    }
};