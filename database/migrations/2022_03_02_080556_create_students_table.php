<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('family_name');
            $table->string('first_name');
            $table->string('family_name_furigana');
            $table->string('first_name_furigana');
            $table->string('email_valid')->nullable();
            $table->string('email_invalid')->nullable();
            $table->string('password');
            $table->tinyInteger('is_email_approved')->comment('0: yes,1:no');
            $table->foreignId('education_facility_id')->constrained('education_facilities')->cascadeOnDelete();
            $table->string('university_name')->nullable();
            $table->year('graduate_year')->nullable();
            $table->string('graduate_month', 2)->nullable();
            $table->string('self_introduction')->nullable();
            $table->boolean('status')->default(1)->nullable();
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
        Schema::dropIfExists('students');
    }
}
