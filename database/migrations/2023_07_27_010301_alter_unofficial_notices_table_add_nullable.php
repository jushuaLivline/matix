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
        Schema::dropIfExists('unofficial_notices');
        Schema::create('unofficial_notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("product_number")->nullable();
            $table->string("acceptance")->nullable();
            $table->string("delivery_destination_code")->nullable();
            $table->string("year_and_month")->nullable();

            for($i = 1; $i < 32; $i++){
                $table->string("day_". $i)->nullable();
            }

            $table->string("current_month")->nullable();
            $table->string("next_month")->nullable();
            $table->string("two_months_later")->nullable();
            $table->string("instruction_class")->nullable();
            $table->string("direct_shipping_destination")->nullable();
            $table->string("uniform_number")->nullable();
            $table->string("cycle")->nullable();
            $table->string("number_of_accomodated")->nullable();
            $table->string("aisin_factory")->nullable();
            $table->string("responsible_person")->nullable();
            $table->string("minimum_delivery_unit")->nullable();
            $table->string("number_per_day")->nullable();
            $table->string("number_of_cards")->nullable();
            $table->string("kanban_number")->nullable();
            $table->string("standard_stock")->nullable();
            $table->string("sp_tp_classification")->nullable();
            $table->string("manufactorer_factory")->nullable();
            $table->string("manufactorer_factory_destination")->nullable();
            $table->string("data_partition")->nullable();
            $table->string("current_month_order_rate_factored_number")->nullable();
            $table->string("color_mode")->nullable();
            $table->string("customer_part_number")->nullable();
            $table->string("customer")->nullable();
            $table->string("design_change_code")->nullable();
            $table->string("input_category")->nullable();
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
