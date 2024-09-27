<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('event_kms')) {
            Schema::create('event_kms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_org_id');
                $table->string('title');
                $table->longText('description')->nullable()->default(NULL);
                $table->string('venue')->nullable()->default(NULL);
                $table->string('organizer')->nullable()->default(NULL);
                $table->string('email')->nullable()->default(NULL);
                $table->string('website')->nullable()->default(NULL);
                $table->string('facebook')->nullable()->default(NULL);
                $table->string('youtube')->nullable()->default(NULL);
                $table->date('event_start')->nullable()->default(NULL);
                $table->date('event_end')->nullable()->default(NULL);
                $table->text('cover_image_path')->nullable();
                $table->boolean('allow_join')->default(0);
                $table->boolean('status')->default(1);
                $table->unsignedInteger('total_views')->default(0);
                $table->unsignedInteger('total_joins')->default(0);
                $table->dateTime('published_at')->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
                $table->timestamps();
                
                // Define foreign keys
                $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

                // Index
                $table->index('status');
    
            });
        }
        
        if (!Schema::hasTable('event_join_kms')) {
            Schema::create('event_join_kms', function (Blueprint $table) {
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('user_id');
                $table->string('invitation_code')->nullable()->default(NULL);
                $table->dateTime('invited_at')->nullable()->default(NULL);
                $table->unsignedBigInteger('invited_by')->nullable()->default(NULL);
                $table->dateTime('joined_at')->nullable()->default(NULL);
		       	$table->timestamp('created_at');
		       	$table->timestamp('updated_at');
            
                // Define foreign keys
                $table->foreign('event_id')->references('id')->on('event_kms')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->primary(['event_id', 'user_id']);
                // Indexes
	            $table->index('invitation_code');

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
        Schema::dropIfExists('event_kms');
        Schema::dropIfExists('event_join_kms');
    }
}
