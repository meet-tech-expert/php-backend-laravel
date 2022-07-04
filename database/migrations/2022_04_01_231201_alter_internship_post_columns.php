<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterInternshipPostColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('internship_posts', function (Blueprint $table) {
            // $table->dropConstrainedForeignId('internship_feature_id');
            // $table->dropConstrainedForeignId('work_category_id');

            $table->unsignedBigInteger('internship_feature_id')->nullable()->change();
            $table->unsignedBigInteger('work_category_id')->nullable()->change();

            $table->smallInteger('workload')->nullable()->comment('1: 10h未満/週')->change();
            $table->smallInteger('period')->nullable()->comment('1: 1ヶ月')->change();
            $table->smallInteger('target_grade')->nullable()->comment('1: 1〜2年生')->change();
            $table->smallInteger('wage')->nullable()->comment('1: ¥1,000〜')->change();
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
            $table->unsignedBigInteger('internship_feature_id')->nullable(false)->change();
            $table->unsignedBigInteger('work_category_id')->nullable(false)->change();
            $table->smallInteger('workload')->comment('1: 10h未満/週')->nullable(false)->change();
            $table->smallInteger('period')->comment('1: 1ヶ月')->nullable(false)->change();
            $table->smallInteger('target_grade')->nullable(false)->comment('1: 1〜2年生')->change();
            $table->smallInteger('wage')->nullable(false)->comment('1: ¥1,000〜')->change();
        });
    }
}
