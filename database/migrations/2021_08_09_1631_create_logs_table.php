<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('logs')) { return; }
    	Schema::create('logs', function (Blueprint $table) {
    		$table->id();
    		$table->unsignedBigInteger('user_org_id')->nullable()->default(NULL);
    		$table->string('channel')->nullable()->default(NULL);
    		$table->string('severity')->nullable()->default(NULL);
    		$table->string('module')->nullable()->default(NULL);
    		$table->text('title');
    		$table->text('description');
    		$table->string('username')->nullable()->default(NULL);
    		$table->string('email')->nullable()->default(NULL);
    		$table->string('ip');
    		$table->unsignedBigInteger('user_id')->nullable()->default(NULL);
    		$table->unsignedBigInteger('course_id')->nullable()->default(NULL);
    		$table->timestamps();

         // Define foreign keys
         $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
    		$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

         // Indexes
    		$table->index('user_id');
    		$table->index('course_id');
    		$table->index('user_org_id');
    		$table->index(['user_org_id', 'user_id']);
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::dropIfExists('logs');
    }
 }
