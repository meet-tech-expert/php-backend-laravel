<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMediaPostMetaFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_posts', function (Blueprint $table) {
            $table->string('seo_slug', 255)->nullable()->change();
            $table->string('seo_ogp', 255)->nullable()->change();
            $table->longText('summery')->nullable()->change();
            $table->longText('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_posts', function (Blueprint $table) {
            $table->string('seo_slug', 255)->nullable(false)->change();
            $table->string('seo_ogp', 255)->nullable(false)->change();
            $table->longText('summery')->nullable(false)->change();
            $table->longText('description')->nullable(false)->change();
        });
    }
}
