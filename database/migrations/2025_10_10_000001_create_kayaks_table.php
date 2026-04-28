<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kayaks', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'single' or 'double'
            $table->integer('hourly_rate');
            $table->integer('total_quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kayaks');
    }
};
