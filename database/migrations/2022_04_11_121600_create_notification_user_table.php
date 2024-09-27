<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('notification_users')) { return; }
        Schema::create('notification_users', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_org_id');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('notification_id');
            $table->boolean('status')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
            $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
			$table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');

			// Indexes
			$table->index('user_org_id');
            $table->index('user_id');
            $table->index('notification_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_users');
    }
}
