<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAddAdminVerify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'data_setting')) {
            Schema::table('users', function (Blueprint $table) {
                $table->longText('data_setting')->nullable()->default(NULL)->after('data_contact');
                $table->string('admin_verified_state')->nullable()->default('0')->comment('0-Verified, 1-Wait for approval')->after('expires_at');
                $table->string('admin_verified_by')->nullable()->default(NULL)->after('admin_verified_state');
                $table->string('admin_verified_at')->nullable()->default(NULL)->after('admin_verified_by');
                $table->string('email_verified_state')->nullable()->default('0')->comment('0-Verified, 1-Wait for approval')->after('admin_verified_at');
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
