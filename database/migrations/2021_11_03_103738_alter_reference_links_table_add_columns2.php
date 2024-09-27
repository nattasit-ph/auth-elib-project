<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReferenceLinksTableAddColumns2 extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('reference_links', 'is_home')) {
	     		Schema::table('reference_links', function (Blueprint $table) {
		      	$table->boolean('is_home')->default(1)->after('weight');
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
