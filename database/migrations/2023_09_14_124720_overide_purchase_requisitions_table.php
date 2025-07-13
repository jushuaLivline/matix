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
        Schema::dropIfExists('purchase_requisitions'); // Drop the old table
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_number')->nullable();
            $table->dateTime('requested_date')->nullable();
            $table->string('supplier_code')->nullable();
            $table->string('department_code')->nullable();
            $table->string('line_code')->nullable();
            $table->string('machine_number')->nullable();
            $table->string('part_number')->nullable();
            $table->string('product_name')->nullable();
            $table->string('standard')->nullable();
            $table->string('reason')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit_code')->nullable();
            $table->double('unit_price')->nullable();
            $table->integer('amount_of_money')->nullable();
            $table->string('expense_items')->nullable();
            $table->string('subsidy_items')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->char('tax_classification', 2)->nullable();
            $table->string('project_number')->nullable();
            $table->string('where_used_code')->nullable();
            $table->string('quotation_existence_flag')->nullable();
            $table->string('approval_method_category')->nullable();
            $table->string('approval_route_number')->nullable();
            $table->string('data_type')->nullable();
            $table->string('state_classification')->nullable();
            $table->string('next_approver')->nullable();
            $table->string('remarks')->nullable();
            $table->string('reason_for_denial')->nullable();
            $table->string('purchase_order_number')->nullable();
            $table->string('purchase_order_details_number')->nullable();
            $table->dateTime('order_date')->nullable();
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
        //
    }
};
