<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeInternshipPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('internship_posts', function (Blueprint $table) {
            $table->dropColumn('wage_high');
            $table->dropColumn('wage_low');
            $table->dropColumn('target_grade_to');
            $table->dropColumn('target_grade_from');
            $table->smallInteger('period')->comment(
                "1: 1ヶ月",
                "2: 1〜3ヶ月",
                "3: 3ヶ月〜"
            )->change();
            $table->smallInteger('workload')->comment(
                "1: 10h未満/週",
                "2: 10〜20h/週",
                "3: 20h以上/週"
            )->change();
            $table->smallInteger('wage')->comment(
                "1: ¥1,000〜",
                "2: ¥1,500〜",
                "3: ¥2,000〜"
            )->nullable(false);
            $table->smallInteger('target_grade')->comment(
                "1: 1〜2年生",
                "2: 1〜3年生",
                "3: 学年問わず"
            )->nullable(false);
            $table->timestamp('public_date')->nullable();

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
            $table->dropColumn('target_grade');
            $table->dropColumn('wage');
            $table->integer('wage_low')->nullable(false);
            $table->integer('wage_high')->nullable(false);

            $table->smallInteger('period')->comment(
                '0: 〜1ヶ月',
                '1: 〜3ヶ月',
                '2: 〜6ヶ月',
                '3: 〜1年'
            )->change();
            $table->smallInteger('target_grade_from')->comment(
                '0: 1',
                '1: 2',
                '2: 3',
                '3: 4',
                '4: 5',
                '5: 6',
            )->nullable(false);
            $table->smallInteger('target_grade_to')->comment(
                '0: 1',
                '1: 2',
                '2: 3',
                '3: 4',
                '4: 5',
                '5: 6',
            )->nullable(false);
            $table->smallInteger('workload')->comment(
                '0: ～10h / 週',
                '1: ～20h / 週',
                '2: ～30h / 週',
                '3: ～40h / 週'
            )->change();
        });
    }
}
