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
        Schema::create('sales_performances', function (Blueprint $table) {
            $table->id();
            $table->string("product_number");
            $table->string("part_number");
            $table->string("amount_category");
            $table->string("supplier_code");
            $table->string("customer_code");
            $table->string("department_code");
            $table->string("line_code");
            $table->integer("quantity");
            $table->string("unit_price");
            $table->string("amount");
            $table->string("ai_slip_type");
            $table->unsignedBigInteger("creator");
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
        Schema::dropIfExists('sales_performances');
    }
};
