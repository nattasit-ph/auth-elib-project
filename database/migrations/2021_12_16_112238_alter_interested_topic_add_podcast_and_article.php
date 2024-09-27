<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInterestedTopicAddPodcastAndArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('interested_topic', 'data_podcast')) {
            Schema::table('interested_topic', function($table)
            {
                $table->longText('data_podcast')->nullable()->after('file_size');
            });
        }
        if (!Schema::hasColumn('interested_topic', 'data_article')) {
            Schema::table('interested_topic', function($table)
            {
                $table->longText('data_article')->nullable()->after('file_size');
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
