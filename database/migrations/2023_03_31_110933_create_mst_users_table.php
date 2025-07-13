<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->string('user_name')->nullable()->default(null);
            $table->string('email')->unique();
            $table->string('password')->nullable()->default(null);
            $table->dateTime('last_login_dt')->default(null);
            $table->dateTime('email_verified_dt')->default(null);
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('forgot_password_code', 100)->nullable();
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
        Schema::dropIfExists('users');
    }
};
