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
        Schema::dropIfExists('firm_orders'); // Drop the old table

        Schema::create('firm_orders', function (Blueprint $table) {
            $table->id();
            $table->string('confirmed_order_no')->nullable();
            $table->string('delivery_destination_code')->nullable();
            $table->date('due_date')->nullable();
            $table->string('delivery_no')->nullable();
            $table->string('part_number')->nullable();
            $table->string('classification')->default(1);
            $table->string('plant')->nullable();
            $table->string('acceptance')->nullable();
            $table->string('uniform_number')->nullable();
            $table->string('number_of_accommodated')->nullable();
            $table->string('kanban_number')->nullable();
            $table->string('instruction_number')->nullable();
            $table->string('ai_delivery_number')->nullable();
            $table->string('ai_jersey_number')->nullable();
            $table->string('instructions_printed_flag')->nullable();
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
        Schema::dropIfExists('firm_orders');
    }
};
