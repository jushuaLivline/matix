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
        Schema::create('supply_material_arrivals', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->string('delivery_no');
            $table->enum('voucher_class', [1, 2, 3])->default(1);
            $table->date('arrival_date');
            $table->string('flight_no')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('part_number');
            $table->string('material_manufacturer_code')->nullable();
            $table->string('material_no');
            $table->string('line_code')->nullable();
            $table->string('department_code')->nullable();
            $table->string('arrival_quantity');
            $table->string('processing_rate')->nullable();
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
        Schema::dropIfExists('supply_material_arrivals');
    }
};
