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
        Schema::create('sales_plans', function (Blueprint $table) {
            $table->id();
            $table->string("year_month")->nullable();
            $table->string("product_number")->nullable();
            $table->string("part_number")->nullable();
            $table->string("amount_category")->nullable();
            $table->string("supplier_code")->nullable();
            $table->string("customer_code")->nullable();
            $table->string("department_code")->nullable();
            $table->string("line_code")->nullable();
            $table->integer("quantity")->nullable();
            $table->double("unit_price")->nullable();
            $table->double("amount")->nullable();
            $table->dateTime("created_at")->useCurrent();
            $table->unsignedBigInteger("creator")->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger("updator")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_plans');
    }
};
