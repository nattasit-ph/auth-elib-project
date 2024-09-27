<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('page_attachments')) {
            Schema::create('page_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('page_id');
                $table->string('title');
                $table->text('file_path')->nullable();
                $table->integer('file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
                $table->timestamps();

                // Indexes
                $table->index('page_id');

                // Define foreign keys
                $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_attachments');
    }
}
