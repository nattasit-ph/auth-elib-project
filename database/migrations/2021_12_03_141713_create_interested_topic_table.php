<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestedTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('interested_topic')) { return; }
        Schema::create('interested_topic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id')->nullable()->default(NULL);
            $table->string('title');
            $table->text('description');
            $table->string('file_path')->nullable()->default(NULL);
            $table->integer('file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
            $table->longText('data_tags')->nullable()->default(NULL);
            $table->longText('data_library')->nullable()->default(NULL);
            $table->longText('data_elibrary')->nullable()->default(NULL);
            $table->longText('data_km')->nullable()->default(NULL);
            $table->integer('weight')->nullable()->default(NULL);
            $table->boolean('status')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
            $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

            // Indexes
            $table->index(['status']);
            $table->index(['status', 'user_org_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interested_topic');
    }
}
