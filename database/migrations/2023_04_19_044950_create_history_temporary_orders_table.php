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
        Schema::create('history_temporary_orders', function (Blueprint $table) {
            $table->id();
            $table->string('item_code',20);
            $table->string('accessioning_code',2);
            $table->char('customer_code',6);
            $table->char('order_yearmonth',6);
            $table->integer('day_1_amount')->default('0');
            $table->integer('day_2_amount')->default('0');
            $table->integer('day_3_amount')->default('0');
            $table->integer('day_4_amount')->default('0');
            $table->integer('day_5_amount')->default('0');
            $table->integer('day_6_amount')->default('0');
            $table->integer('day_7_amount')->default('0');
            $table->integer('day_8_amount')->default('0');
            $table->integer('day_9_amount')->default('0');
            $table->integer('day_10_amount')->default('0');
            $table->integer('day_11_amount')->default('0');
            $table->integer('day_12_amount')->default('0');
            $table->integer('day_13_amount')->default('0');
            $table->integer('day_14_amount')->default('0');
            $table->integer('day_15_amount')->default('0');
            $table->integer('day_16_amount')->default('0');
            $table->integer('day_17_amount')->default('0');
            $table->integer('day_18_amount')->default('0');
            $table->integer('day_19_amount')->default('0');
            $table->integer('day_20_amount')->default('0');
            $table->integer('day_21_amount')->default('0');
            $table->integer('day_22_amount')->default('0');
            $table->integer('day_23_amount')->default('0');
            $table->integer('day_24_amount')->default('0');
            $table->integer('day_25_amount')->default('0');
            $table->integer('day_26_amount')->default('0');
            $table->integer('day_27_amount')->default('0');
            $table->integer('day_28_amount')->default('0');
            $table->integer('day_29_amount')->default('0');
            $table->integer('day_30_amount')->default('0');
            $table->integer('day_31_amount')->default('0');
            $table->integer('currentmonth_amount')->default('0');
            $table->integer('nextmonth_amount')->default('0');
            $table->integer('monthafternext_amount')->default('0');
            $table->char('instruction_classification',1);
            $table->char('direct_destination',4);
            $table->char('back_number',4);
            $table->string('cycle',5);
            $table->integer('capacity')->default('0');
            $table->string('aisin_plant_code',1);
            $table->string('charge_code',2);
            $table->integer('minimum_delivery_unit')->default('0');
            $table->integer('number_per_day')->default('0');
            $table->integer('number_of_cards')->default('0');
            $table->integer('number_of_kanban')->default('0');
            $table->integer('standard_inventory')->default('0');
            $table->string('sptp_classification',1);
            $table->string('factory_src',1);
            $table->string('factory_dest',1);
            $table->string('data_classification',1);
            $table->integer('current_month_order_rate_weighted_decomposition_number')->default('0');
            $table->string('color_code',7);
            $table->string('customer_item_code',20);
            $table->string('aisin_customer_code',4);
            $table->string('variation_code',1);
            $table->char('input_classification',1);
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
        Schema::dropIfExists('history_temporary_orders');
    }
};
