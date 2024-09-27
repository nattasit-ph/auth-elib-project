<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('polls')) {
            Schema::create('polls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_org_id');
                $table->bigInteger('knowledge_id')->unsigned()->nullable()->default(NULL);
                $table->string('question');
                $table->date('poll_start')->nullable()->default(NULL);
                $table->date('poll_end')->nullable()->default(NULL);
                $table->unsignedInteger('total_options')->default(0);
                $table->unsignedInteger('total_votes')->default(0);

                $table->boolean('status')->default(1);
                $table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
                $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
                $table->timestamps();

                // Define foreign keys
                $table->foreign('knowledge_id')->references('id')->on('knowledges')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('updated_by')->references('id')->on('users');
                $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

                // Indexes
                $table->index('status');
                // $table->index('knowledge_id');
                // $table->index(['status', 'knowledge_id']);
            });
        }

        if (!Schema::hasTable('poll_votes')) {
            Schema::create('poll_votes', function (Blueprint $table) {
                $table->bigInteger('poll_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('poll_option_id')->unsigned();
                $table->timestamps();
    
                $table->primary(['poll_id', 'user_id']);
    
                // Define foreign keys
                $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    
                // Indexes
                $table->index('poll_id');
                $table->index('user_id');
                $table->index(['poll_id', 'poll_option_id']);
            });
        }

        if (!Schema::hasTable('poll_options')) {
            Schema::create('poll_options', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('poll_id')->unsigned();
                $table->string('title');
    
                $table->boolean('status')->default(1);
                $table->bigInteger('created_by')->unsigned()->nullable()->default(NULL);
                $table->bigInteger('updated_by')->unsigned()->nullable()->default(NULL);
                $table->timestamps();
    
                // Define foreign keys
                $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('updated_by')->references('id')->on('users');
    
                // Indexes
                $table->index('status');
                $table->index('poll_id');
                $table->index(['status', 'poll_id']);
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
        Schema::dropIfExists('polls');
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
    }
}
