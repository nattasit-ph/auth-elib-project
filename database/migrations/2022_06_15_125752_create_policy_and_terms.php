<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyAndTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('policy_and_terms')) {
            Schema::create('policy_and_terms', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 255);
                $table->longText('detail_th');
                $table->longText('detail_en');
                $table->integer('type');
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
        Schema::dropIfExists('policy_and_terms');
    }
}
