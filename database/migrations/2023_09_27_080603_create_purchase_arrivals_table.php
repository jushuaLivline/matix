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
        Schema::create('purchase_arrivals', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_no');
            $table->string('purchase_order_details_no');
            $table->integer('branch_number');
            $table->date('arrival_day');
            $table->integer('arrival_quantity');
            $table->string('slip_no');
            $table->boolean('unable_to_resharpen_flag')->nullable();
            $table->text('remarks')->nullable();
            $table->date('purchase_receipt_date')->nullable();
            $table->string('purchase_record_no')->nullable();
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
        Schema::dropIfExists('purchase_arrivals');
    }
};
