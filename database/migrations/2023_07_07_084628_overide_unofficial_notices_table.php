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
            $table->unsignedBigInteger("product_number");
            $table->string("acceptance");
            $table->string("delivery_destination_code");
            $table->string("year_and_month");

            for($i = 1; $i < 32; $i++){
                $table->string("day_". $i);
            }

            $table->string("current_month");
            $table->string("next_month");
            $table->string("two_months_later");
            $table->string("instruction_class");
            $table->string("direct_shipping_destination");
            $table->string("uniform_number");
            $table->string("cycle");
            $table->string("number_of_accomodated");
            $table->string("aisin_factory");
            $table->string("responsible_person");
            $table->string("minimum_delivery_unit");
            $table->string("number_per_day");
            $table->string("number_of_cards");
            $table->string("kanban_number");
            $table->string("standard_stock");
            $table->string("sp_tp_classification");
            $table->string("manufactorer_factory");
            $table->string("manufactorer_factory_destination");
            $table->string("data_partition");
            $table->string("current_month_order_rate_factored_number");
            $table->string("color_mode");
            $table->string("customer_part_number");
            $table->string("customer");
            $table->string("design_change_code");
            $table->string("input_category");
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
