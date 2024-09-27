<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogChatbotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('log_chatbots')) { return; }
    	Schema::create('log_chatbots', function (Blueprint $table) {
    		$table->id();
    		$table->string('session_id')->nullable()->default(NULL);
    		$table->string('user_type')->nullable()->default(NULL);
    		$table->string('intent_id')->nullable()->default(NULL);
    		$table->string('intent_name')->nullable()->default(NULL);
    		$table->string('action')->nullable()->default(NULL);
    		$table->string('data_type')->nullable()->default(NULL)->comment('text, list, slide');
    		$table->longText('data');
    		$table->string('ip');
    		$table->timestamps();

			// Indexes
    		$table->index('session_id');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::dropIfExists('log_chatbots');
    }
 }
