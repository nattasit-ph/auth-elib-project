<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableRegisDeviceGenPassword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'registry_device')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('registry_device')->nullable()->default('web')->after('data_contact');
                $table->string('registry_device_id')->nullable()->default(NULL)->after('registry_device');
                $table->string('rand')->nullable()->default(NULL)->after('remember_token');
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
