<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->nullable(); 
            $table->string('name')->nullable(); 
            $table->string('email')->nullable(); 
            $table->string('phone')->nullable(); 
            $table->string('start_date')->nullable(); 
            $table->string('end_date')->nullable(); 
            $table->unsignedInteger('adults')->default(1);   // ✅ number of adults
            $table->unsignedInteger('children')->default(0); // ✅ number of children
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
