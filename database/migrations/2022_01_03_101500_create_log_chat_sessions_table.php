<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogChatSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('log_chat_sessions')) { return; }
        Schema::create('log_chat_sessions', function (Blueprint $table) {
            $table->id();
    		$table->string('chat_type')->nullable()->default(NULL)->comment('chatbot, chatadmin');
    		$table->string('session_id')->unique()->nullable()->default(NULL);
            $table->boolean('unread')->default(0);
            $table->text('lastest_message')->nullable();
            $table->dateTime('lastest_at')->nullable();
            $table->string('email')->comment('อีเมล์ผู้ใช้งาน');
    		$table->string('gender')->nullable()->default(NULL)->comment('f, m');
    		$table->string('country')->nullable()->default(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_chat_sessions');
    }
}
