<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    		if (Schema::hasTable('reward_activities')) { return; }
        	Schema::create('reward_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_rog_id')->unsigned()->nullable()->default(NULL);
            $table->string('system')->comment('elib/elearn/km');
            $table->string('module');
            $table->string('title');
            $table->string('action_name');
            $table->integer('point')->unsigned()->default(0);
            $table->integer('weight')->nullable()->default(NULL);
            $table->boolean('status')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
            $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index(['action_name']);
            $table->index(['action_name', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_activities');
    }
}
