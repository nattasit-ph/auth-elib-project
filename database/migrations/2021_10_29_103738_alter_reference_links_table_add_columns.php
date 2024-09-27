<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReferenceLinksTableAddColumns extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('reference_links', 'agency')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->string('agency')->nullable()->default(null)->after('title');
		   	});
	    }
		if (!Schema::hasColumn('reference_links', 'group')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	 $table->json('group')->nullable()->after('agency');
		   	});
	    }
        if (!Schema::hasColumn('reference_links', 'category')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->bigInteger('category')->unsigned()->nullable()->default(NULL)->after('group');
		   	});
	    }
        if (!Schema::hasColumn('reference_links', 'description')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	 $table->json('description')->nullable()->after('category');
		   	});
	    }
        if (!Schema::hasColumn('reference_links', 'total_resources')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->string('total_resources')->nullable()->default(NULL)->after('description');
		   	});
	    }
        if (!Schema::hasColumn('reference_links', 'cover_image_path')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->text('cover_image_path')->nullable()->default(NULL)->after('file_path');
		   	});
	    }
         if (!Schema::hasColumn('reference_links', 'url')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->text('url')->nullable()->default(null)->after('cover_image_path');
		   	});
	    }

		if (!Schema::hasColumn('reference_links', 'weight')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->float('weight')->default(0)->after('system');
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
