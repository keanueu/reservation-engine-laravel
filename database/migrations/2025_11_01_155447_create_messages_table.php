<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index(); // guest session id (UUID)
            $table->unsignedBigInteger('user_id')->nullable()->index(); // if logged-in user (admin replies null as admin)
            $table->enum('sender', ['user','ai','admin'])->index();
            $table->text('message');
            $table->json('meta')->nullable(); // optional structured data (e.g., {"intent":"book_room","dates":"2025-11-10/2025-11-12"})
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

