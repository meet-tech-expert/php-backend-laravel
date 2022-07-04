<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaDescToTableMediaPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_posts', function (Blueprint $table) {
            $table->string('seo_meta_description')->nullable()->after('seo_ogp');
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
            $table->dropColumn('seo_meta_description');
        });
    }
}
