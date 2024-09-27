<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAddToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     	if (!Schema::hasColumn('users', 'token')) {
    		Schema::table('users', function($table)
			{
				$table->string('token', 255)->nullable()->after('status');
			});
 		}
		if (!Schema::hasColumn('users', 'elib_user_id')) {
    		Schema::table('users', function($table)
			{
				$table->unsignedBigInteger('elib_user_id')->nullable()->after('token');
				$table->unsignedBigInteger('ori_user_id')->nullable()->after('elib_user_id');
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
