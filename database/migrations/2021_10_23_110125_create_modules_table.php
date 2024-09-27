<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	  if (!Schema::hasTable('modules')) {
	        Schema::create('modules', function (Blueprint $table) {
	            $table->id();
	            $table->unsignedBigInteger('user_org_id');
	            $table->string('slug');
	            $table->string('name_en');
	            $table->string('name_th');
	            $table->string('backend_fa_icon')->nullable()->default(NULL);
	            $table->string('frontend_icon')->nullable()->default(NULL);
	            $table->boolean('has_categories')->default(1);
	            
	            $table->float('weight')->nullable();
	            $table->boolean('in_center')->default(1);
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
     		else {
     			 if (!Schema::hasColumn('modules', 'in_center')) {
     			 	Schema::table('modules', function (Blueprint $table) {
     			 		$table->boolean('in_center')->default(1)->after('weight');
     			 	});
     			 }
     		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
