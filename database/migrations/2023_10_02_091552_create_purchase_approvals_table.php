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
        Schema::create('purchase_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_record_no')->nullable();
            $table->integer('order_of_approval')->nullable();
            $table->string('approver_employee_code')->nullable();
            $table->dateTime("approval_date")->nullable();
            $table->dateTime('denial_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_approvals');
    }
};
