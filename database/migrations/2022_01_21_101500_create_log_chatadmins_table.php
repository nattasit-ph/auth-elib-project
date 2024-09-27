<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogChatadminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('log_chatadmins')) { return; }
        Schema::create('log_chatadmins', function (Blueprint $table) {
            $table->id();
    		$table->string('session_id')->nullable()->default(NULL);
    		$table->string('user_type')->nullable()->default(NULL);
            $table->text('message');
            $table->boolean('is_read')->default(0);
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

			// Indexes
    		$table->index('session_id');

            // Define foreign keys
            $table->foreign('session_id')->references('session_id')->on('log_chat_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
