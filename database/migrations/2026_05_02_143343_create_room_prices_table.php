<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();

            // Date range this rate applies to (null = applies every year on those days)
            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('price', 10, 2);

            // Rate type: 'seasonal' | 'weekend' | 'holiday' | 'promo'
            $table->string('rate_type')->default('seasonal');

            // Human-readable label shown in the price breakdown (e.g. "Christmas Season")
            $table->string('label')->nullable();

            // Priority: higher number wins when multiple rates overlap the same date
            $table->unsignedTinyInteger('priority')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Prevent exact duplicate ranges for the same room
            $table->index(['room_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_prices');
    }
};
