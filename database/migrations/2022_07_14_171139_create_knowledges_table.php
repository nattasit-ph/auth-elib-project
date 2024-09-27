<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('knowledges')) {
            Schema::create('knowledges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id');
            $table->string('slug');     
            $table->string('title');
            $table->longText('data_blocks')->nullable()->default(NULL);
            $table->text('cover_file_path')->nullable();
            $table->integer('cover_file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
            $table->text('ref_url')->nullable()->default(NULL);
            $table->unsignedBigInteger('category_id');
            $table->unsignedInteger('read_time')->nullable()->default(NULL)->comment('นับจากจำนวนตัวอักษรทั้งหมด /500 แล้วปัดขึ้น หน่วยเป็นนาที');
            $table->unsignedInteger('total_view')->default(0);
            $table->unsignedInteger('total_share')->default(0);
            $table->unsignedInteger('total_action')->default(0);
            $table->dateTime('published_at')->nullable()->default(NULL);
            $table->dateTime('edit_at')->nullable()->default(NULL);

            $table->boolean('is_recommended')->default(0)->nullable()->default(NULL);
            $table->boolean('is_promoted')->default(0)->nullable()->default(NULL);

            $table->boolean('status')->default(1);
            $table->dateTime('approved_at')->nullable()->default(NULL);
            $table->unsignedBigInteger('approved_by')->nullable()->default(NULL);
            $table->unsignedBigInteger('created_by')->nullable()->default(NULL);
            $table->unsignedBigInteger('updated_by')->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

            // Indexes
            $table->index('slug');
            $table->index('user_org_id');
            $table->index(['user_org_id', 'status']);
            $table->index(['user_org_id', 'slug']);
            $table->index(['user_org_id', 'is_recommended']);
            $table->index(['user_org_id', 'is_promoted']);
            });
        }

        if (!Schema::hasTable('knowledge_comments')) {
            Schema::create('knowledge_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_org_id');
                $table->unsignedBigInteger('knowledge_id');
                $table->unsignedBigInteger('user_id');
                $table->text('comment')->nullable()->default(NULL);
                $table->boolean('status');
                $table->timestamps();

                // Define foreign keys
                $table->foreign('knowledge_id')->references('id')->on('knowledges')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Indexes
                $table->index('user_org_id');
                $table->index('knowledge_id');
                $table->index('user_id');
                $table->index(['knowledge_id', 'status']);
            });
        }

        if (!Schema::hasTable('knowledge_favorites')) {
            Schema::create('knowledge_favorites', function (Blueprint $table) {
                $table->unsignedBigInteger('user_org_id');
                $table->unsignedBigInteger('knowledge_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                // Primary key
                $table->primary(['knowledge_id', 'user_id']);

                // Define foreign keys
                $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
                $table->foreign('knowledge_id')->references('id')->on('knowledges')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Indexes
                $table->index('user_org_id');
                $table->index('knowledge_id');
                $table->index('user_id');
                $table->index(['knowledge_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('knowledge_actions')) {
            Schema::create('knowledge_actions', function (Blueprint $table) {
                $table->unsignedBigInteger('user_org_id');
                $table->unsignedBigInteger('knowledge_id');
                $table->unsignedBigInteger('user_id');
                $table->string('action');
                $table->timestamps();

                // Primary key
                $table->primary(['knowledge_id', 'user_id']);

                // Define foreign keys
                $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');
                $table->foreign('knowledge_id')->references('id')->on('knowledges')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                // Indexes
                $table->index('user_org_id');
                $table->index('knowledge_id');
                $table->index('user_id');
                $table->index(['knowledge_id', 'user_id']);
                $table->index(['knowledge_id', 'action']);
            });
        }

        if (!Schema::hasTable('knowledge_categories')) {
            Schema::create('knowledge_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id');
            $table->string('slug');
            $table->string('title');

            $table->float('weight')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable()->default(NULL);
            $table->unsignedBigInteger('updated_by')->nullable()->default(NULL);
            $table->timestamps();

            // Define foreign keys
            $table->foreign('user_org_id')->references('id')->on('user_orgs')->onDelete('cascade');

            // Indexes
            $table->index('user_org_id');
            $table->index(['user_org_id', 'status']);
            });
        }

        if (!Schema::hasTable('knowledge_attachments')) {
            Schema::create('knowledge_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('knowledge_id');
                $table->string('title');
                $table->text('file_path')->nullable();
                    $table->integer('file_size')->unsigned()->nullable()->default(NULL)->comment('in bytes');
                $table->timestamps();
                
                // Indexes
                $table->index('knowledge_id');

                // Define foreign keys
                $table->foreign('knowledge_id')->references('id')->on('knowledges')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('knowledges_slogans')) {
            Schema::create('knowledges_slogans', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->boolean('status')->default(1);
                $table->unsignedBigInteger('created_by')->nullable()->default(NULL);
                $table->unsignedBigInteger('updated_by')->nullable()->default(NULL);
                $table->timestamps();
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
        Schema::dropIfExists('knowledges');
        Schema::dropIfExists('knowledge_comments');
        Schema::dropIfExists('knowledge_favorites');
        Schema::dropIfExists('knowledge_actions');
        Schema::dropIfExists('knowledge_categories');
        Schema::dropIfExists('knowledge_attachments');
        Schema::dropIfExists('knowledges_slogans');
        
    }
}
