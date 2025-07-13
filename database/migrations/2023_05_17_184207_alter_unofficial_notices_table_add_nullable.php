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
        Schema::table('unofficial_notices', function (Blueprint $table) {
            $table->unsignedBigInteger("product_id")->nullable()->change();
            $table->string("acceptance")->nullable()->change();
            $table->string("delivery_destination_code")->nullable()->change();
            $table->string("year_and_month")->nullable()->change();

            for($i = 1; $i < 32; $i++){
                $table->string("day_". $i)->nullable()->change();
            }

            $table->string("current_month")->nullable()->change();
            $table->string("next_month")->nullable()->change();
            $table->string("two_months_later")->nullable()->change();
            $table->string("instruction_class")->nullable()->change();
            $table->string("direct_shipping_destination")->nullable()->change();
            $table->string("uniform_number")->nullable()->change();
            $table->string("cycle")->nullable()->change();
            $table->string("number_of_accomodated")->nullable()->change();
            $table->string("aisin_factory")->nullable()->change();
            $table->string("responsible_person")->nullable()->change();
            $table->string("minimum_delivery_unit")->nullable()->change();
            $table->string("number_per_day")->nullable()->change();
            $table->string("number_of_cards")->nullable()->change();
            $table->string("kanban_number")->nullable()->change();
            $table->string("standard_stock")->nullable()->change();
            $table->string("sp_tp_classification")->nullable()->change();
            $table->string("manufactorer_factory")->nullable()->change();
            $table->string("manufactorer_factory_destination")->nullable()->change();
            $table->string("data_partition")->nullable()->change();
            $table->string("current_month_order_rate_factored_number")->nullable()->change();
            $table->string("color_mode")->nullable()->change();
            $table->string("customer_part_number")->nullable()->change();
            $table->string("customer")->nullable()->change();
            $table->string("design_change_code")->nullable()->change();
            $table->string("input_category")->nullable()->change();
            $table->unsignedBigInteger("creator")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unofficial_notices', function (Blueprint $table) {
            $table->unsignedBigInteger("product_id")->nullable(false)->change();
            $table->string("acceptance")->nullable(false)->change();
            $table->string("delivery_destination_code")->nullable(false)->change();
            $table->string("year_and_month")->nullable(false)->change();

            for($i = 1; $i < 32; $i++){
                $table->string("day_". $i)->nullable(false)->change();
            }

            $table->string("current_month")->nullable(false)->change();
            $table->string("next_month")->nullable(false)->change();
            $table->string("two_months_later")->nullable(false)->change();
            $table->string("instruction_class")->nullable(false)->change();
            $table->string("direct_shipping_destination")->nullable(false)->change();
            $table->string("uniform_number")->nullable(false)->change();
            $table->string("cycle")->nullable(false)->change();
            $table->string("number_of_accomodated")->nullable(false)->change();
            $table->string("aisin_factory")->nullable(false)->change();
            $table->string("responsible_person")->nullable(false)->change();
            $table->string("minimum_delivery_unit")->nullable(false)->change();
            $table->string("number_per_day")->nullable(false)->change();
            $table->string("number_of_cards")->nullable(false)->change();
            $table->string("kanban_number")->nullable(false)->change();
            $table->string("standard_stock")->nullable(false)->change();
            $table->string("sp_tp_classification")->nullable(false)->change();
            $table->string("manufactorer_factory")->nullable(false)->change();
            $table->string("manufactorer_factory_destination")->nullable(false)->change();
            $table->string("data_partition")->nullable(false)->change();
            $table->string("current_month_order_rate_factored_number")->nullable(false)->change();
            $table->string("color_mode")->nullable(false)->change();
            $table->string("customer_part_number")->nullable(false)->change();
            $table->string("customer")->nullable(false)->change();
            $table->string("design_change_code")->nullable(false)->change();
            $table->string("input_category")->nullable(false)->change();
            $table->unsignedBigInteger("creator")->nullable(false)->change();
        });
    }
};
