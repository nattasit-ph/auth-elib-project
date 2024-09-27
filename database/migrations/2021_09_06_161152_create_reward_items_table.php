<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        	if (Schema::hasTable('reward_items')) { return; }
        	Schema::create('reward_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable()->default(NULL);
            $table->bigInteger('reward_category_id')->unsigned()->nullable()->default(NULL);
            $table->boolean('is_digital')->default(0);
            $table->integer('point')->unsigned()->default(0);
            $table->integer('stock_avail')->unsigned()->default(0);
            $table->date('started_at')->nullable()->default(NULL);
            $table->date('expired_at')->nullable()->default(NULL);
          	$table->integer('max_per_user')->nullable()->default(NULL)->comment('NULL = Unlimited');
            $table->boolean('status')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
            $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('reward_category_id')->references('id')->on('reward_categories')->onDelete('set null');

          	// Indexes
            $table->index('status');
            $table->index('reward_category_id');
            $table->index(['reward_category_id', 'status']);
            $table->index(['reward_category_id', 'started_at', 'expired_at', 'status'], 'fk1');
            $table->index(['started_at', 'expired_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_items');
    }
}
