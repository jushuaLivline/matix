<?php

use App\Traits\CreateTableIndexes;
use App\Traits\DropTableIndexes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use CreateTableIndexes, DropTableIndexes;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outsourced_processings', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'supplier_code', 'product_code', 'document_issue_date', 'instruction_date',
                'instruction_number', 'management_no', 'order_no', 'arrival_day', 'incoming_flight_number',
            ]);
        });

        Schema::table('kanban_masters', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'management_no',
            ]);
        });

        Schema::table('outsource_material_failures', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'material_number', 'process_code', 'supplier_code',
                'slip_no', 'return_date', 'created_at',
            ]);
        });

        Schema::table('codes', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'division',
            ]);
        });

        Schema::table('outsource_process_failures', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'part_number', 'process_code', 'slip_no',
                'disposal_date', 'created_at',
            ]);
        });

        Schema::table('purchase_records', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'supplier_code', 'purchase_category', 'voucher_class', 'machine_number', 'department_code',
                'line_code', 'expense_item', 'part_number', 'slip_no', 'project_number', 'amount_of_money',
                'created_at', 'arrival_date',
            ]);
        });

        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'order_date', 'deadline', 'department_code', 'line_code', 'purchase_order_number',
                'requested_date', 'supplier_code', 'approval_method_category', 'requisition_number',
                'created_at', 'part_number', 'machine_number', 'product_name', 'expense_items',
                'standard', 'creator', 'quantity', 'amount_of_money',
            ]);
        });

        Schema::table('purchase_arrivals', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'purchase_order_no', 'slip_no', 'arrival_quantity', 'purchase_receipt_date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outsourced_processings', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'supplier_code', 'product_code', 'document_issue_date', 'instruction_date',
                'instruction_number', 'management_no', 'order_no', 'arrival_day', 'incoming_flight_number',
            ]);
        });

        Schema::table('kanban_masters', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'management_no',
            ]);
        });

        Schema::table('outsource_material_failures', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'material_number', 'process_code', 'supplier_code',
                'slip_no', 'return_date', 'created_at',
            ]);
        });

        Schema::table('codes', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'division',
            ]);
        });

        Schema::table('outsource_process_failures', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'part_number', 'process_code', 'slip_no',
                'disposal_date', 'created_at',
            ]);
        });

        Schema::table('purchase_records', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'supplier_code', 'purchase_category', 'voucher_class', 'machine_number', 'department_code',
                'line_code', 'expense_item', 'part_number', 'slip_no', 'project_number', 'amount_of_money',
                'created_at', 'arrival_date',
            ]);
        });

        Schema::table('purchase_requisitions', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'order_date', 'deadline', 'department_code', 'line_code', 'purchase_order_number',
                'requested_date', 'supplier_code', 'approval_method_category', 'requisition_number',
                'created_at', 'part_number', 'machine_number', 'product_name', 'expense_items',
                'standard', 'creator', 'quantity', 'amount_of_money',
            ]);
        });

        Schema::table('purchase_arrivals', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'purchase_order_no', 'slip_no', 'arrival_quantity', 'purchase_receipt_date',
            ]);
        });
    }
};
