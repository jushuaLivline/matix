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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable();
            $table->string('item_name')->nullable();
            $table->string('product_name_abbreviation')->nullable();
            $table->string('product_category')->nullable();
            $table->char('customer_code',6)->nullable();
            $table->char('supplier_code',6)->nullable();
            $table->char('department_code',6)->nullable();
            $table->string('line_code')->nullable();
            $table->char('sub_line_code',2)->nullable();
            $table->string('standard')->nullable();
            $table->char('material_manufacturer_code',4)->nullable();
            $table->string('unit_code',10)->nullable();
            $table->string('part_number_edit_format')->nullable();
            $table->string('edit_part_number',24)->nullable();
            $table->string('instruction_classification')->nullable();
            $table->string('customer_part_number')->nullable();
            $table->string('customer_part_number_edit_format')->nullable();
            $table->string('customer_edited_part_number')->nullable();
            $table->string('production_classification')->nullable();
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
        Schema::dropIfExists('products');
    }
};
