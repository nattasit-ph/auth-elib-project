<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (Schema::hasTable('pages')) { return; }
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id');
            $table->string('system')->default('belib')->comment('belib = Belib system, km = KM system');
            $table->string('slug');
            $table->string('title_en');
            $table->string('title_th');
            $table->longText('data_blocks')->nullable()->default(NULL);
            $table->text('cover_file_path')->nullable();
            $table->integer('cover_file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
            $table->text('ref_url')->nullable()->default(NULL);
            $table->unsignedInteger('total_view')->default(0);
            $table->unsignedInteger('total_share')->default(0);

            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable()->default(NULL);
            $table->unsignedBigInteger('updated_by')->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

            // Indexes
            $table->index('slug');
            $table->index('user_org_id');
            $table->index(['user_org_id', 'status']);
            $table->index(['user_org_id', 'slug']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
