<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardRedemptionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        	if (Schema::hasTable('reward_redemption_histories')) { return; }
        	Schema::create('reward_redemption_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('reward_item_id')->unsigned();
            $table->integer('unit')->unsigned()->default(1);
            $table->integer('unit_point')->unsigned()->default(0);
            $table->integer('total_point')->unsigned()->default(0);
            $table->boolean('is_delivered')->default(0);
            $table->boolean('is_refunded')->default(0);
            $table->bigInteger('delivered_by')->unsigned()->nullable()->default(NULL);
            $table->bigInteger('refunded_by')->unsigned()->nullable()->default(NULL);
            $table->dateTime('redeemed_at');
            $table->dateTime('delivered_at')->nullable()->default(NULL)->comment('NULL if not delivered yet.');
            $table->dateTime('refunded_at')->nullable()->default(NULL)->comment('NULL if no refund action.');
            $table->timestamps();
            $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);

            // Define foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reward_item_id')->references('id')->on('reward_items');

          	// Indexes
            $table->index('user_id');
            $table->index(['user_id', 'reward_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_redemption_histories');
    }
}
