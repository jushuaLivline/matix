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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_code');
            $table->string('process_name')->nullable();
            $table->string('abbreviation_process_name')->nullable();
            $table->string('inside_and_outside_division')->nullable();
            $table->unsignedBigInteger('customer_code')->nullable();
            $table->integer('backorder_days')->nullable();
            $table->integer('material_receiving_classification')->default(2);
            $table->boolean("delete_flag")->default(0);
            $table->dateTime("created_at")->useCurrent()->nullable();
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
        Schema::dropIfExists('processes');
    }
};
