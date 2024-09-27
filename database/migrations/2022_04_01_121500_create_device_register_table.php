<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_registers')) { return; }
        Schema::create('device_registers', function (Blueprint $table) {
            $table->id();
    		$table->string('device')->nullable()->default(NULL)->comment('i.e. android, ios');
    		$table->string('model')->nullable()->default(NULL)->comment('e.g. iPhone 12 mini');
    		$table->string('os')->nullable()->default(NULL)->comment('e.g. 15.4.1');
    		$table->string('device_id')->nullable()->default(NULL);
            $table->text('device_token')->nullable()->default(NULL)->comment('For push message');
    		$table->unsignedBigInteger('user_id')->nullable()->default(NULL);
            $table->boolean('status')->default(1);
            $table->timestamps();

			// Indexes
    		$table->index('user_id');

            // Define foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_registers');
    }
}
