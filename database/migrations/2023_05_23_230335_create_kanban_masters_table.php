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
        Schema::create('kanban_masters', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger("product_id");
            // $table->unsignedBigInteger("process_id");
            $table->string("management_no")->nullable();
            $table->string("kanban_classification")->nullable();
            $table->string("part_number")->nullable();
            $table->string("process_code")->nullable();
            $table->string("customer_acceptance")->nullable();
            $table->string("process_order")->nullable();
            $table->string("next_process_code")->nullable();
            $table->string("cycle_day")->nullable();
            $table->string("number_of_cycles")->nullable();
            $table->string("cycle_interval")->nullable();
            $table->string("number_of_accomodated")->nullable();
            $table->string("box_type")->nullable();
            $table->string("acceptance")->nullable();
            $table->string("shipping_location")->nullable();
            $table->string("printed_jersey_number")->nullable();
            $table->string("remark_1")->nullable();
            $table->string("remark_2")->nullable();
            $table->string("remark_qr_code")->nullable();
            $table->string("issued_sequence_number")->nullable();
            $table->string("paid_category")->comment("1 Paid, 2 No Charge")->nullable();
            $table->boolean('delete_flag',1)->default(0);
            $table->dateTime('created_at')->default(now());
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
        Schema::dropIfExists('kanban_masters');
    }
};
