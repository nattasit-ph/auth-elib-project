<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('login_histories')) { return; }
    	Schema::create('login_histories', function (Blueprint $table) {
    		$table->id();
    		$table->unsignedBigInteger('user_org_id')->nullable()->default(NULL);
    		$table->unsignedBigInteger('user_id')->nullable()->default(NULL);
    		$table->string('username')->nullable()->default(NULL);
    		$table->string('email');
    		$table->string('device');
    		$table->string('device_id')->nullable();
    		$table->string('ip');
    		$table->string('status');
    		$table->unsignedInteger('attempt')->default(0);
    		$table->timestamps();

         // Define foreign keys
         $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
    		$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

         // Indexes
    		$table->index('user_id');
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
    	Schema::dropIfExists('login_histories');
    }
 }
