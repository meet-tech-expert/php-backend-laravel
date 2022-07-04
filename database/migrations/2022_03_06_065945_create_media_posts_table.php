<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title',100);
            $table->longText('summery');
            $table->foreignId('media_tag_id')->constrained('media_tags')->cascadeOnDelete();
            $table->string('seo_slug',255);
            $table->string('seo_ogp',255);
            $table->string('seo_featured_image',255)->nullable();
            $table->longText('description');
            $table->boolean('is_draft')->default(0)->comment('0: Draft, 1: Public');
            $table->unsignedBigInteger('page_views')->default(0)->nullable();
            $table->boolean('status')->comment('0: Inactive, 1: Active');
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
        Schema::dropIfExists('media_posts');
    }
}
