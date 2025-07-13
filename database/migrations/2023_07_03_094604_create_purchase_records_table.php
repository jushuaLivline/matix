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
        Schema::create('purchase_records', function (Blueprint $table) {
            $table->id();
            $table->string("purchase_record_no")->nullable();
            $table->string("voucher_class")->nullable();
            $table->string("slip_type")->nullable();
            $table->string("purchase_category")->nullable();
            $table->dateTime("arrival_date")->nullable();
            $table->string("supplier_code")->nullable();
            $table->string("machine_number")->nullable();
            $table->string("branch_number")->nullable();
            $table->string("department_code")->nullable();
            $table->string("line_cord")->nullable();
            $table->string("expense_item")->nullable();
            $table->string("subsidy_items")->nullable();
            $table->string("part_number")->nullable();
            $table->string("product_name")->nullable();
            $table->string("standard")->nullable();
            $table->string("where_used_code")->nullable();
            $table->integer("quantity")->nullable();
            $table->string("unit_code")->nullable();
            $table->double("unit_price")->nullable();
            $table->double("amount_of_money")->nullable();
            $table->string("tax_classification")->nullable();
            $table->string("slip_no")->nullable();
            $table->string("project_number")->nullable();
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('purchase_records');
    }
};
