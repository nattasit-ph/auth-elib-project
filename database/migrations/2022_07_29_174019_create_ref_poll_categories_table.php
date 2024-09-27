<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefPollCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_poll_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('poll_id');
            $table->unsignedBigInteger('poll_category_id');

            // Primary key
            $table->primary(['poll_id', 'poll_category_id']);

    		// Define foreign keys
    		$table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
    		$table->foreign('poll_category_id')->references('id')->on('poll_categories')->onDelete('cascade');

	         // Indexes
    		$table->index('poll_id');
    		$table->index('poll_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_poll_categories');
    }
}
