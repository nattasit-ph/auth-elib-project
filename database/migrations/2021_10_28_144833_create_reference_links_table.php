<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferenceLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('reference_links')) { return; }
        Schema::create('reference_links', function (Blueprint $table) {
          $table->id();
          $table->string('title')->nullable()->default(NULL);
          $table->text('file_path')->nullable()->default(NULL);
          $table->text('external_url')->nullable()->default(NULL);
          $table->string('system')->nullable()->default(NULL)->comment('belib, learnext, km');
          $table->boolean('status')->default(1);
          $table->unsignedBigInteger('created_by')->nullable();
          $table->unsignedBigInteger('updated_by')->nullable();
          $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reference_links');
    }
}
