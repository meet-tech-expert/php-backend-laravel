<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternshipPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internship_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('work_category_id')->constrained('work_categories')->cascadeOnDelete();
            $table->tinyInteger('period')->comment(
                '1: 〜1ヶ月',
                '2: 〜3ヶ月',
                '3: 〜6ヶ月',
                '4: 〜1年'
            );
            $table->tinyInteger('workload')->comment(
                '1: ～10h / 週',
                '2: ～20h / 週',
                '3: ～30h / 週',
                '4: ～40h / 週'
            );
            $table->integer('wage_low');
            $table->integer('wage_high');
            $table->tinyInteger('target_grade')->comment(
                '1: 1年生以上',
                '2: 2年生以上',
                '3: 3年生以上',
                '4: 4年生以上',
                '5: 5年生以上',
                '6: 6年生以上',
            );
            $table->foreignId('internship_feature_id')->constrained('internship_features')->cascadeOnDelete();
            $table->string('application_step_1')->nullable();
            $table->string('application_step_2')->nullable();
            $table->string('application_step_3')->nullable();
            $table->string('application_step_4')->nullable();
            $table->string('seo_slug')->nullable();
            $table->string('seo_ogp')->nullable();
            $table->string('seo_meta_description')->nullable();
            $table->string('seo_featured_image')->nullable();
            $table->longText('description_corporate_profile')->nullable();
            $table->longText('description_internship_content')->nullable();
            $table->boolean('draft_or_public')->default(0)->nullable()->comment('0 : No, 1: Yes');
            $table->integer('page_views')->default(0)->nullable();
            $table->integer('status')->default(0)->comment('0: hiring  1: done');
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
        Schema::dropIfExists('internship_posts');
    }
}
