<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('banners')) { return; }
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id')->nullable()->default(NULL);
            $table->string('title');
            $table->string('file_path')->nullable()->default(NULL);
            $table->integer('file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
            $table->string('system')->nullable()->default(NULL)->comment('belib, learnext, km');
            $table->boolean('for_mobile')->default(1);	
    			$table->boolean('for_web')->default(1);
            $table->string('display_area')->default('homepage'); // login, homepage
            $table->text('line_1')->nullable()->default(NULL);
            $table->text('line_2')->nullable()->default(NULL);
            $table->text('line_3')->nullable()->default(NULL);
            $table->string('text_color')->nullable()->default(NULL);
            $table->text('external_url')->nullable()->default(NULL);
            $table->text('internal_url')->nullable()->default(NULL);
            
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
	    		$table->index(['status', 'user_org_id', 'for_mobile']);
	    		$table->index(['status', 'user_org_id', 'for_web']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
}
