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
        Schema::create('shipment_records', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_no');
            $table->tinyInteger('serial_number');
            $table->string('slip_no');
            $table->string('voucher_class');
            $table->string('customer_code');
            $table->dateTime('due_date');
            $table->string('delivery_no');
            $table->string('acceptance');
            $table->string('drop_ship_code');
            $table->string('part_no');
            $table->string('line_code');
            $table->string('department_code');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->string('remarks')->nullable();
            $table->string('plant');
            $table->dateTime('closing_date');
            $table->string('ai_slip_type');
            $table->enum('classification', ['kanban', 'instruction']);
            $table->string('uniform_no');
            $table->string('accomodation_no');
            $table->string('kanban_no');
            $table->string('instruction_no');
            $table->string('ai_delivery_no');
            $table->string('ai_jersey_no');
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
        Schema::dropIfExists('shipment_records');
    }
};
