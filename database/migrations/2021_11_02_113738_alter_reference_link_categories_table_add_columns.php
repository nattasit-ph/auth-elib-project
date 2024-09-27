<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReferenceLinkCategoriesTableAddColumns extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('reference_link_categories', 'slug')) {
	     		Schema::table('reference_link_categories', function (Blueprint $table) {
		      	$table->string('slug')->nullable()->default(null)->after('title');
		   	});
	    }

		 if (!Schema::hasColumn('reference_link_categories', 'created_by')) {
	     		Schema::table('reference_link_categories', function (Blueprint $table) {
		      	$table->unsignedBigInteger('created_by')->nullable()->after('status');
		   	});
	    }

		 if (!Schema::hasColumn('reference_link_categories', 'updated_by')) {
	     		Schema::table('reference_link_categories', function (Blueprint $table) {
		      	$table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
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
        //
    }
}
