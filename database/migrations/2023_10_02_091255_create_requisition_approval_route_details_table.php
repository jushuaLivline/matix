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
        Schema::create('purchase_approval_route_details', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->nullable();
            $table->integer('approval_route_no')->nullable();
            $table->integer('order_of_approval')->nullable();
            $table->string('approver_employee_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisition_approval_route_details');
    }
};
