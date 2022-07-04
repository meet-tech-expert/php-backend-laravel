<?php

use App\Libraries\PasswordResetServices;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', PasswordResetServices::$userTypes)->default(PasswordResetServices::$userTypes['Admin']);
            $table->unsignedBigInteger('user_id');
            $table->string('email')->index();
            $table->string('token');
            $table->boolean('is_used')->default(0);
            $table->timestamp('expired_at'); 
            $table->index(['user_id', 'token']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_reset_requests');
    }
}
