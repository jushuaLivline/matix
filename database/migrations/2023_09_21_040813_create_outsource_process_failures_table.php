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
        Schema::create('outsource_process_failures', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no');
            $table->string('serial_number');
            $table->string('process_code')->nullable();
            $table->date('disposal_date')->nullable();
            $table->string('part_number')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('slip_no')->nullable();
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
        Schema::dropIfExists('outsource_process_failures');
    }
};
