<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOrgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('user_orgs')) { return; }
        Schema::create('user_orgs', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->string('slug')->nullable()->default(NULL);
            $table->boolean('is_bd')->default(0);
            $table->string('name_en');
            $table->string('name_th');
            $table->text('address_en')->nullable()->default(NULL);
            $table->text('address_th')->nullable()->default(NULL);
            $table->string('contact_email');
            $table->string('phone')->nullable()->default(NULL);
            $table->string('fax')->nullable()->default(NULL);
            $table->string('facebook')->nullable()->default(NULL);
            $table->string('twitter')->nullable()->default(NULL);
            $table->string('youtube')->nullable()->default(NULL);
            $table->string('line')->nullable()->default(NULL);
            $table->string('logo_path')->nullable()->default(NULL);
            $table->string('barcode_logo_path')->nullable()->default(NULL)->comment('For barcode printing only');
            $table->string('barcode_library_name')->nullable()->default(NULL)->comment('For barcode printing only');
            $table->string('barcode_library_name_position', 1)->nullable()->default(NULL)->comment('For barcode printing only, t = top / b = bottom');
            $table->string('barcode_cover_position', 1)->nullable()->default(NULL)->comment('For barcode printing only, f = front / b = back');
            $table->longText('data_info')->nullable()->default(NULL);
            $table->longText('data_contact')->nullable()->default(NULL);
            $table->timestamp('registry_at')->nullable();
            $table->dateTime('accessible_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->unsignedInteger('user_limit')->default(9999);
            $table->unsignedBigInteger('storage_limit')->nullable()->default(NULL)->comment('Size in GB / NULL = Unlimit');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_orgs');
    }
}
