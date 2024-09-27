<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (Schema::hasTable('users')) { return; }
     	Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->nullable();
            $table->unsignedBigInteger('user_org_id');
            $table->boolean('is_tester')->default(0);
            $table->string('email')->unique();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_user_id')->nullable();
            $table->text('avatar_path')->nullable();
            $table->char('gender', 1)->default('m');	
            $table->string('contact_number')->nullable();
            $table->date('birthday')->nullable()->default(NULL);
            $table->integer('points')->unsigned()->default(0);
            $table->unsignedBigInteger('user_role_id')->nullable()->default(NULL);	
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('status')->default(1);
            $table->longText('data_info')->nullable()->default(NULL);
            $table->longText('data_contact')->nullable()->default(NULL);
            $table->timestamp('registry_at')->nullable();
            $table->dateTime('accessible_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken()->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

	         // Indexes
            $table->index('status');
            $table->index('user_org_id');
            $table->index('user_org_id', 'member_id');
	         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
