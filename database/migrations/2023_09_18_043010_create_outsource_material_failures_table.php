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
        Schema::create('outsource_material_failures', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('process_code')->nullable();
            $table->string('slip_no')->nullable();
            $table->date('return_date')->nullable();
            $table->string('product_number')->nullable();
            $table->string('material_number')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('material_manufacturer_code')->nullable();
            $table->string('reason_code')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('processing_rate', 10, 2)->nullable();
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
        Schema::dropIfExists('outsource_material_failures');
    }
};
