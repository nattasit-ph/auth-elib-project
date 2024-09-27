<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_org_id');
            $table->string('name');
            $table->boolean('is_default')->default(0);
            $table->longText('data_info')->nullable()->default(NULL);

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

        if (!Schema::hasColumn('users', 'user_department_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('user_department_id')->nullable()->default(NULL)->after('user_org_id');

                // Indexes
                $table->index('user_department_id');

                // Define foreign keys
                $table->foreign('user_department_id')->references('id')->on('user_departments')->onDelete('set null');
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
        Schema::dropIfExists('user_groups');
    }
}
