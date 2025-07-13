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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->integer("data_partition");
            $table->string("voucher_class");
            $table->string("data_number");
            $table->string("serial_number");
            $table->dateTime("date");
            $table->string("supplier_code")->index();
            $table->string("slip_number");
            $table->string("part_number")->index()->nullable();
            $table->string("product_number")->index()->nullable();
            $table->string("standard")->nullable();
            $table->string("customer_code")->index()->nullable();
            $table->string("quantity")->nullable();
            $table->string("unit_code")->nullable();
            $table->double("unit_price")->nullable();
            $table->integer("amount_of_money")->nullable();
            $table->char("tax_classification", 1)->nullable();
            $table->string("expense_item")->nullable();
            $table->string("subsidy_items")->nullable();
            $table->string("department_code")->index()->nullable();
            $table->string("line_code")->index()->nullable();
            $table->string("edited_part_number")->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
