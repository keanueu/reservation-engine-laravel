<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('discounts')) {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->index();
                $table->text('description')->nullable();
                $table->string('type')->nullable();
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('amount_type')->default('percent');
                $table->boolean('combinable')->default(false);
                $table->boolean('active')->default(false)->index();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('discount_images')) {
            Schema::create('discount_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
                $table->string('filename')->nullable();
                $table->string('path')->nullable();
                $table->string('alt')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('discount_room')) {
            Schema::create('discount_room', function (Blueprint $table) {
                $table->id();
                $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
                $table->foreignId('room_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['discount_id', 'room_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_room');
        Schema::dropIfExists('discount_images');
        Schema::dropIfExists('discounts');
    }
};
