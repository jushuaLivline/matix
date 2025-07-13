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
        Schema::create('purchase_approval_histories', function (Blueprint $table) {
            $table->id(); // Auto Increment, Primary Key
            $table->string('purchase_requisition_id')->index();
            //Causing error on migration
            //its been set a nullable() on purchase requisition
            //$table->bigInteger('purchase_requisition_id')->unsigned()->index();
            $table->string('department_code')->nullable();
            $table->string('line_code')->nullable();
            $table->string('machine_number')->nullable();
            $table->string('part_number')->nullable();
            $table->string('product_name')->nullable();
            $table->string('standard')->nullable();
            $table->string('reason')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit_code')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('amount_of_money')->nullable();
            $table->string('expense_items')->nullable();
            $table->date('deadline')->nullable();
            $table->string('quotation_existence_flag')->nullable();
            $table->string('approval_method_category')->nullable();
            $table->string('remarks')->nullable();
            $table->string('reason_for_denial')->nullable();
            $table->string('creator')->nullable();
            $table->dateTime('created_at')->useCurrent();

            // Foreign Key Constraint
            $table->foreign('purchase_requisition_id')
                ->references('requisition_number')->on('purchase_requisitions')
                ->onDelete('cascade');

            $table->foreign('creator')
                ->references('employee_code')->on('employees')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_approval_histories');
    }
};
