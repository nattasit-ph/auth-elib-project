<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardEarningHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        	if (Schema::hasTable('reward_earning_histories')) { return; }
        	Schema::create('reward_earning_histories', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('reward_activity_id')->unsigned();
            $table->string('model_type');
            $table->bigInteger('model_id')->unsigned();
            $table->integer('point')->unsigned()->default(0);
            $table->timestamps();

          	$table->primary(['user_id', 'reward_activity_id', 'model_type', 'model_id'], 'pk');

            // Define foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reward_activity_id')->references('id')->on('reward_activities');

          	// Indexes
            $table->index('user_id');
            $table->index(['user_id', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_earning_histories');
    }
}
