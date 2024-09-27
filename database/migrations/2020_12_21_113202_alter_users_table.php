<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     	if (!Schema::hasColumn('users', 'provider')) {
    		Schema::table('users', function($table)
			{
				$table->string('provider')->nullable()->after('remember_token');
            $table->string('provider_user_id')->nullable()->after('provider');

			    // $table->unsignedInteger('created_by')->nullable()->after('updated_at');
			    // $table->unsignedInteger('updated_by')->nullable()->after('created_by');
			    // $table->unsignedInteger('deleted_by')->nullable()->after('updated_by');
			    // $table->index('elib_user_id');
       //       $table->index('ori_user_id');
			});
    	}
    	
    	if (!Schema::hasColumn('users', 'gender')) {
    		Schema::table('users', function($table)
			{
				$table->char('gender', 1)->default('m')->after('avatar_original_path');
			});
    	}
    	if (!Schema::hasColumn('users', 'position')) {
    		Schema::table('users', function($table)
			{
				$table->string('position')->nullable()->after('gender');
			});
    	}
    	if (!Schema::hasColumn('users', 'about_me')) {
    		Schema::table('users', function($table)
			{
				$table->text('about_me')->nullable()->after('position');
			});
    	}
    	if (!Schema::hasColumn('users', 'last_login_ip')) {
    		Schema::table('users', function($table)
			{
				$table->string('last_login_ip')->nullable()->after('last_login_at');
			});
    	}
    	if (!Schema::hasColumn('users', 'deleted_by')) {
    		Schema::table('users', function($table)
			{
				$table->bigInteger('deleted_by')->unsigned()->nullable()->default(NULL)->after('updated_by');
			});
    	}
    	if (!Schema::hasColumn('users', 'display_name')) {
    		Schema::table('users', function($table)
			{
				$table->string('display_name')->nullable()->after('username');
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
