<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationLogsTableAddIsRead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('notification_logs', 'is_read')) {
    		Schema::table('notification_logs', function($table)
			{
                $table->boolean('is_read')->default(0);
			});
 		}
        if (!Schema::hasColumn('notification_logs', 'url')) {
            Schema::table('notification_logs', function($table)
            {
                $table->text('url')->nullable();
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
