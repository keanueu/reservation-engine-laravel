<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kayaks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Single Kayak, Double Kayak
            $table->enum('type', ['single', 'double']);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('hourly_rate'); // in pesos, e.g. 300
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kayaks');
    }
};
