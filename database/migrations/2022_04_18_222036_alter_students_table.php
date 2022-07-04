<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('students', function (Blueprint $table) {
            $table->string('family_name')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('family_name_furigana')->nullable()->change();
            $table->string('first_name_furigana')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->unsignedBigInteger('education_facility_id')->constrained('education_facilities')->nullable()->change();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'students',
            function (Blueprint $table) {
                $table->string('family_name')->nullable(false)->change();
                $table->string('first_name')->nullable(false)->change();
                $table->string('family_name_furigana')->nullable(false)->change();
                $table->string('first_name_furigana')->nullable(false)->change();
                $table->string('password')->nullable(false)->change();
                $table->unsignedBigInteger('education_facility_id')->constrained('education_facilities')->nullable(false)->change();
            }
        );
    }
}
