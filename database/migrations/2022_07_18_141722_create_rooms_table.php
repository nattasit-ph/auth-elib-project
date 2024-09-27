<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        //room_types table
        if (!Schema::hasTable('room_types')) {
            Schema::create('room_types', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_org_id');
                $table->string('title');
                $table->string('description')->nullable()->default(NULL);
                $table->float('weight')->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->unsignedInteger('created_by')->nullable()->default(NULL);
                $table->unsignedInteger('updated_by')->nullable()->default(NULL);
                $table->timestamps();
                // Indexes
                $table->index('status');
                // Define foreign keys
                $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
            });
        }

        //rooms table
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_org_id');
                $table->string('title');
                $table->longText('description');
                $table->string('slug')->nullable()->default(NULL);
                $table->text('facilities');
                $table->string('max_seats')->nullable()->default(NULL);
                $table->string('open_time')->nullable()->default(NULL);
                $table->string('closed_time')->nullable()->default(NULL);
                $table->unsignedBigInteger('room_type_id')->nullable()->default(NULL);
                $table->boolean('status')->default(1);
                $table->timestamps();
                $table->unsignedInteger('created_by')->nullable()->default(NULL);
                $table->unsignedInteger('updated_by')->nullable()->default(NULL);
                
                // Indexes
                $table->index('status');
                $table->index('slug');
                $table->index(['slug', 'status']);
                // $table->index('room_type_id');
	            // $table->index(['room_type_id', 'status']);
    
                // Define foreign keys
                $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
                $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
                
            });
        }

        //room_bookings table
        if (!Schema::hasTable('room_bookings')) {
            Schema::create('room_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime');
                $table->unsignedBigInteger('room_id');
                $table->unsignedBigInteger('user_id');
                $table->boolean('status')->default(1);
                $table->timestamps();
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
    
                // Indexes
                $table->index('status');
                $table->index('user_id');
                $table->index('room_id');
                $table->index(['room_id', 'status']);
    
                // Define foreign keys
                 $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
                 $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        //room_galleries table
        if (!Schema::hasTable('room_galleries')) {
            Schema::create('room_galleries', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('room_id')->unsigned();
                  $table->string('file_path')->nullable()->default(NULL);
                $table->integer('file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
                $table->boolean('is_cover')->default(0);
                $table->timestamps();
    
                // Define foreign keys
                $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
    
                  // Indexes
                $table->index('room_id');
                $table->index(['room_id', 'is_cover']);
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
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_bookings');
        Schema::dropIfExists('room_galleries');
    }
}
