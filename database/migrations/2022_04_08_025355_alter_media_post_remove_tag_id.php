<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMediaPostRemoveTagId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_tag_id');
            $table->dropColumn('page_views');
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
            $table->foreignId('media_tag_id')->default(1)->nullable()->constrained('media_tags')->nullOnDelete();
            $table->integer('page_views')->default(0);
        });
    }
}
