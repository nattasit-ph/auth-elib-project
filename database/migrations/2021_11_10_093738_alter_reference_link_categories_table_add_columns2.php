<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReferenceLinkCategoriesTableAddColumns2 extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


		if (!Schema::hasColumn('reference_link_categories', 'cover_image_path')) {
	     		Schema::table('reference_link_categories', function (Blueprint $table) {
		      	$table->text('cover_image_path')->nullable()->default(NULL)->after('slug');
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
