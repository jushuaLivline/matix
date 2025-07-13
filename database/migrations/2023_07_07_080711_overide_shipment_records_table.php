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
        Schema::dropIfExists('shipment_records');
        
        Schema::create('shipment_records', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_no')->nullable();
            $table->integer('serial_number')->nullable();
            $table->string('slip_no')->nullable();
            $table->string('voucher_class')->nullable();
            $table->string('delivery_destination_code')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('delivery_no')->nullable();
            $table->string('acceptance')->nullable();
            $table->string('drop_ship_code')->nullable();
            $table->string('product_no')->nullable();
            $table->string('line_code')->nullable();
            $table->string('department_code')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('unit_price')->nullable();
            $table->string('remarks')->nullable()->nullable();
            $table->string('plant')->nullable();
            $table->dateTime('closing_date')->nullable();
            $table->string('ai_slip_type')->nullable();
            $table->string('classification')->nullable();
            $table->string('uniform_no')->nullable();
            $table->string('accomodation_no')->nullable();
            $table->string('kanban_no')->nullable();
            $table->string('instruction_no')->nullable();
            $table->string('ai_delivery_no')->nullable();
            $table->string('ai_jersey_no')->nullable();
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
        //
    }
};
