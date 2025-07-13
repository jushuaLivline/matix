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
        Schema::create('supply_material_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("kanban_master_id")->nullable();
            $table->string('management_no')->nullable();
            $table->string('branch_number')->nullable();
            $table->string('material_number')->nullable();
            $table->string('order_classification')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('material_manufacturer_code')->nullable();
            $table->date('instruction_date')->nullable();
            $table->string('instruction_no')->nullable();
            $table->string('lot')->nullable();
            $table->integer('instruction_kanban_quantity')->nullable();
            $table->integer('instruction_number')->nullable();
            $table->integer('arrival_quantity')->nullable();
            $table->string('where_to_use_department_code')->nullable();
            $table->date('document_issue_date')->nullable();
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
        Schema::dropIfExists('supply_material_orders');
    }
};
