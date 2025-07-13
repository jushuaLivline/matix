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
        Schema::create('history_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no',10);
            $table->char('customer_code',6);
            $table->datetime('supply_d');
            $table->integer('shipping_no')->default(0);
            $table->string('item_code',20);
            $table->char('instruction_classification',1);
            $table->string('factory_code',1);
            $table->string('accessioning_code',2);
            $table->string('back_number',5);
            $table->integer('day_6_amount')->default('0');
            $table->integer('day_7_amount')->default('0');
            $table->integer('capacity')->default('0');
            $table->string('aisin_delivery_no',20);
            $table->string('aisin_back_number',4);
            $table->integer('printed_flag')->default(0);
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
        Schema::dropIfExists('history_orders');
    }
};
