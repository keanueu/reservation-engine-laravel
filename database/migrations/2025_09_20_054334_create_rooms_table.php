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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_name')->nullable();
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
            $table->string('price')->nullable();
            $table->string('accommodates')->nullable();
            $table->longText('terms')->nullable();
            $table->string('room_type')->nullable();
            $table->string('beds')->nullable();
            $table->string('amenities')->nullable();
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
