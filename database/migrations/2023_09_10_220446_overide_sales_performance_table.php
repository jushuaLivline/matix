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
        Schema::dropIfExists('sales_performances');

        Schema::create('sales_performances', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sales_date');
            $table->string('customer_code');
            $table->string('slip_no');
            $table->integer('voucher_class')->default(1);
            $table->string('department_code')->nullable();
            $table->string('line_cord')->nullable();
            $table->string('remarks')->nullable();
            $table->string('closing_date')->nullable();
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
        //
    }
};
