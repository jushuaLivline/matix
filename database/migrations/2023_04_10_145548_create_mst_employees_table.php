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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('department_code')->nullable();
            $table->string('password')->nullable();
            $table->string('authorization_code')->nullable();
            $table->string('mail_address')->nullable();
            $table->boolean('purchasing_approval_request_email_notification_flag')->nullable();
            $table->boolean('delete_flag')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->string('creator_code')->nullable();
            $table->string('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updater_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
