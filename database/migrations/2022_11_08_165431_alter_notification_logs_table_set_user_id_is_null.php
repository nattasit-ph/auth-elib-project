<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationLogsTableSetUserIdIsNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('notification_logs', 'user_id')) {
    		Schema::table('notification_logs', function($table)
			{
                $table->unsignedBigInteger('user_id')->nullable()->default(NULL)->change();
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
