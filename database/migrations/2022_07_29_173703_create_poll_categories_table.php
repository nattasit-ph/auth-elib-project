<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('poll_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id');
            $table->string('slug');
            $table->string('title');

            $table->float('weight')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable()->default(NULL);
            $table->unsignedBigInteger('updated_by')->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

            // Indexes
            $table->index('user_org_id');
            $table->index(['user_org_id', 'status']);
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::dropIfExists('poll_categories');
    }
 }
