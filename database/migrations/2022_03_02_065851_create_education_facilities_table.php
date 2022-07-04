<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->tinyInteger('type')->comment("0: 大学,1: 大学院,2: 短期大学, 3: 専門学校, 4: 高校/高専, 5: その他");
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
        Schema::dropIfExists('education_facilities');
    }
}
