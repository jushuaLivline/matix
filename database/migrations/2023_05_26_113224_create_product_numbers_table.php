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
        Schema::create('product_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('part_number');
            $table->string('product_name')->nullable();
            $table->string('name_abbreviation')->nullable();
            $table->integer('product_category')->default(0);
            $table->string('customer_code')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('department_code')->nullable();
            $table->string('line_code')->nullable();
            $table->string('secondary_line_code')->nullable();
            $table->string('standard')->nullable();
            $table->string('material_manufacturer_code')->nullable();
            $table->string('unit_code')->nullable();
            $table->string('uniform_number')->nullable();
            $table->string('part_number_editing_format')->nullable();
            $table->string('edited_part_number')->nullable();
            $table->integer('instruction_class')->nullable();
            $table->string('customer_part_number')->nullable();
            $table->string('customer_part number_edit_format')->nullable();
            $table->string('customer_edited_product_number')->nullable();
            $table->integer('production_division')->default(0);
            $table->integer('delete_flag')->default(0);
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
        Schema::dropIfExists('product_numbers');
    }
};
