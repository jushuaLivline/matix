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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->date('arrival_day')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('voucher_class')->nullable();
            $table->string('data_number')->nullable();
            $table->integer('serial_number')->nullable();
            $table->string('data_partition')->nullable();
            $table->string('slip_no')->nullable();
            $table->string('part_no')->nullable();
            $table->string('product_name')->nullable();
            $table->string('expense_item')->nullable();
            $table->string('subsidy_item')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('unit_code')->nullable();
            $table->decimal('unit_price', 11, 2)->nullable();
            $table->decimal('payment', 11, 2)->nullable();
            $table->decimal('transfer_amount', 11, 2)->nullable();
            $table->decimal('bill_amount', 11, 2)->nullable();
            $table->string('tax_classification')->nullable();
            $table->string('edited_part_no')->nullable();
            $table->dateTime("created_at")->useCurrent();
            $table->unsignedBigInteger("creator_code")->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger("updator_code")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
};
