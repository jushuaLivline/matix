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
        Schema::create('firm_orders', function (Blueprint $table) {
            $table->id();
            $table->string('confirm_order_no');
            $table->string('customer_code'); //destination code
            $table->string('delivery_no');
            $table->dateTime('due_date');
            $table->string('classification');
            $table->string('part_no');
            $table->string('plant');
            $table->string('acceptance');
            $table->string('uniform_no');
            $table->string('accomodation_no'); 
            $table->string('kanban_no');
            $table->string('instruction_no');
            $table->string('instruction_printed_flag');
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
        Schema::dropIfExists('firm_orders');
    }
};
