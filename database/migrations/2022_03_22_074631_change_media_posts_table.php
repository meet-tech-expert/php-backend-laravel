<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMediaPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_posts', function (Blueprint $table) {
            $table->integer('display_order')->nullable();
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
        Schema::table('media_posts', function (Blueprint $table) {
            $table->dropColumn('display_order');
            $table->dropColumn('public_date');
        });
    }
}
