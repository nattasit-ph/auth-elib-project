<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFormSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_systems', function (Blueprint $table) {
            $table->string('system')->primary();
            $table->unsignedBigInteger('user_org_id')->default(1);
            $table->bigInteger('form_id')->nullable()->default(NULL);

            $table->index('system');
            $table->index(['system', 'form_id']);
        });

        // Insert some stuff
        DB::table('form_systems')->insert(
            array(
                'system' => 'belib',
                'form_id' => null
            )
        );
        DB::table('form_systems')->insert(
            array(
                'system' => 'km',
                'form_id' => null
            )
        );
        DB::table('form_systems')->insert(

            array(
                'system' => 'learnext',
                'form_id' => null
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_systems');
    }
}
