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
        Schema::create('machine_numbers', function (Blueprint $table) {
            $table->id();
            $table->string("machine_number");
            $table->unsignedBigInteger("branch_number");
            $table->unsignedBigInteger("sign");
            $table->string("machine_number_name")->nullable();
            $table->string("project_name")->nullable();
            $table->string("line_name")->nullable();
            $table->string("machine_division")->nullable();
            $table->dateTime("drawing_date")->nullable();
            $table->dateTime("completion_date")->nullable();
            $table->string("manager", 20)->nullable();
            $table->string("remarks")->nullable();
            $table->boolean("delete_flag");
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
        Schema::dropIfExists('machine_numbers');
    }
};
