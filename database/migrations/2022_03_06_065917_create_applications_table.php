<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('internal_application_id')->default('0');
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('internship_post_id')->constrained('internship_posts')->cascadeOnDelete();
            $table->tinyInteger('status')->nullable()->comment("application status  0: 応募済" , "applied 1: 合格済 ",  "qualified  2: 完了",  "done　 3: 不合格",  "not-qualified 4: 辞退済  declined");
            $table->tinyInteger('is_admin_read')->nullable()->default(0)->comment("0: false, not read yet", "1: true. already read");
            $table->string('cancel_reason')->nullable();
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
        Schema::dropIfExists('applications');
    }
}
