<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnaire extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('lang', 2)->default('th');
            $table->longText('description')->nullable()->default(NULL);
            $table->text('contact_email');
            $table->unsignedInteger('total_views')->default(0);
            $table->boolean('status')->default(1);
            $table->dateTime('last_viewed_at')->nullable()->default(NULL);
            $table->unsignedInteger('created_by')->nullable()->default(NULL);
            $table->unsignedInteger('updated_by')->nullable()->default(NULL);
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('status');
            $table->index(['slug', 'status']);
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->string('section_label')->nullable()->default(NULL);
            $table->string('label')->nullable()->default(NULL);
            $table->string('input_type')->nullable()->default(NULL);
            $table->text('help_text')->nullable()->default(NULL);
            $table->text('options')->nullable()->default(NULL);
            $table->boolean('is_required')->default(0);
            $table->unsignedInteger('weight')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('form_id');

            // Define foreign keys
          	$table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
        });

        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('user_id')->nullable()->default(NULL)->comment('NULL = guest');
            $table->text('data_fields');
            $table->unsignedTinyInteger('status')->default(0)->comment('0 = waiting for review / 1 = reviewing / 2 = completed');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('form_id');
            $table->index(['user_id', 'form_id']);

            // Define foreign keys
	         $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
	         $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaire');
    }
}
