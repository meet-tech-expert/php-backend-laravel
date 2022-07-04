<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTargetGradeToRange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('internship_posts', function (Blueprint $table) {
            $table->dropColumn('target_grade');
            $table->tinyInteger('target_grade_from')->comment(
                '0: 1',
                '1: 2',
                '2: 3',
                '3: 4',
                '4: 5',
                '5: 6',
            )->nullable(false);
            $table->tinyInteger('target_grade_to')->comment(
                '0: 1',
                '1: 2',
                '2: 3',
                '3: 4',
                '4: 5',
                '5: 6',
            )->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('internship_posts', function (Blueprint $table) {
            $table->tinyInteger('target_grade')->comment(
                '1: 1年生以上',
                '2: 2年生以上',
                '3: 3年生以上',
                '4: 4年生以上',
                '5: 5年生以上',
                '6: 6年生以上',
            );
        });
    }
}
