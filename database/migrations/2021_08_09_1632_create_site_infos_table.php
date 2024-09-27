<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('site_infos')) { return; }
    	Schema::create('site_infos', function (Blueprint $table) {
    		$table->id();
    		$table->unsignedBigInteger('user_org_id');
    		$table->string('meta_label');
    		$table->string('meta_key');
    		$table->string('meta_input_type')->default('text')->comment('text, textarea');
    		$table->string('meta_help')->nullable()->default(NULL);
    		$table->string('meta_lang', 2)->nullable()->default('th');
    		$table->text('meta_value')->nullable()->default(NULL);
    		$table->text('meta_url')->nullable()->default(NULL);

    		$table->integer('weight')->nullable()->default(NULL);
    		$table->boolean('status')->default(1);
    		$table->unsignedBigInteger('created_by')->nullable()->default(NULL);
    		$table->unsignedBigInteger('updated_by')->nullable()->default(NULL);
    		$table->timestamps();

         // Define foreign keys
         $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

         // Indexes
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
    	Schema::dropIfExists('site_infos');
    }
 }
