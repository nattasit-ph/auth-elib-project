<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	// 1. reward_activities
    	if (Schema::hasTable('reward_activities')) { return; }
    	Schema::create('reward_activities', function (Blueprint $table) {
    		$table->id();
    		$table->string('title');
    		$table->string('module');
    		$table->string('action_name');
    		$table->integer('point')->unsigned()->default(0);
    		$table->integer('weight')->nullable()->default(NULL);
    		$table->boolean('status')->default(1);
    		$table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
    		$table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
    		$table->timestamps();

         // Indexes
    		$table->index('status');
    	});

    	// 2. reward_earning_histories
    	if (Schema::hasTable('reward_earning_histories')) { return; }
    	Schema::create('reward_earning_histories', function (Blueprint $table) {
    		$table->bigInteger('user_id')->unsigned();
    		$table->bigInteger('reward_activity_id')->unsigned();
    		$table->string('model_type');
    		$table->bigInteger('model_id')->unsigned();
    		$table->integer('point')->unsigned()->default(0);
    		$table->text('remark')->nullable()->default(NULL);
    		$table->timestamps();

    		$table->primary(['user_id', 'reward_activity_id', 'model_type', 'model_id'], 'pk');

         // Define foreign keys
    		$table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('reward_activity_id')->references('id')->on('reward_activities');

       	// Indexes
    		$table->index('user_id');
    		$table->index(['user_id', 'model_id']);
    	});

    	// 3. reward_categories
    	if (Schema::hasTable('reward_categories')) { return; }
    	Schema::create('reward_categories', function (Blueprint $table) {
    		$table->id();
    		$table->string('title');
    		$table->text('slug');
    		$table->integer('weight')->nullable()->default(NULL);
    		$table->boolean('status')->default(1);
    		$table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
    		$table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
    		$table->timestamps();

         // Indexes
    		$table->index('status');
    	});

    	// 4. reward_items
    	if (Schema::hasTable('reward_items')) { return; }
    	Schema::create('reward_items', function (Blueprint $table) {
    		$table->id();
    		$table->string('title');
    		$table->text('description')->nullable()->default(NULL);
    		$table->bigInteger('reward_category_id')->unsigned();
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
    		$table->foreign('reward_category_id')->references('id')->on('reward_categories')->onDelete('cascade');

       	// Indexes
    		$table->index('status');
    		$table->index('reward_category_id');
    		$table->index(['reward_category_id', 'status']);
    		$table->index(['reward_category_id', 'started_at', 'expired_at', 'status'], 'fk1');
    		$table->index(['started_at', 'expired_at', 'status']);
    	});

    	// 5. reward_item_galleries
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

    	// 6.reward_redemption_histories
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
    	Schema::dropIfExists('rewards_tables');
    }
 }
