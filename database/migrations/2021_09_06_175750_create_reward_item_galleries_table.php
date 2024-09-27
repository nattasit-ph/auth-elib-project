<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardItemGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        	if (Schema::hasTable('reward_item_galleries')) { return; }
        	Schema::create('reward_item_galleries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reward_item_id')->unsigned();
          	$table->string('file_path')->nullable()->default(NULL);
            $table->integer('file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
            $table->boolean('is_cover')->default(0);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('reward_item_id')->references('id')->on('reward_items')->onDelete('cascade');

          	// Indexes
            $table->index('reward_item_id');
            $table->index(['reward_item_id', 'is_cover']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_item_galleries');
    }
}
