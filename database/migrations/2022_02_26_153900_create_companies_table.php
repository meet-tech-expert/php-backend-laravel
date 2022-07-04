<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('internal_company_id');
            $table->string('name');
            $table->string('furigana_name')->nullable();
            $table->string('logo_img')->nullable();
            $table->foreignId('business_industry_id')->nullable()->constrained('business_industries')->cascadeOnDelete();
            $table->string('office_address')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('office_email1')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('office_email2')->nullable();
            $table->string('office_email3')->nullable();
            $table->string('website_url')->nullable();
            $table->string('client_liason')->nullable();
            $table->longText('admin_memo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
