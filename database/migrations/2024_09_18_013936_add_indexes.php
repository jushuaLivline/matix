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
        Schema::table('daily_production_control', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'part_number', 'line_id', 'year', 'month',
            ]);
        });

        Schema::table('unofficial_notices', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'product_number', 'delivery_destination_code',
            ]);
        });

        Schema::table('product_numbers', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'department_code', 'line_code', 'supplier_code', 'material_manufacturer_code',
                'instruction_class', 'product_category',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'delete_flag',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'customer_flag', 'supplier_tag', 'delete_flag',
            ]);
        });

        Schema::table('processes', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'process_code', 'delete_flag',
            ]);
        });

        Schema::table('firm_orders', function (Blueprint $blueprint) {
            $table = $blueprint->getTable();

            if (!isIndexExistsInTable($table, 'mst_firm_orders_due_date_kanban_number_index', true)) {
                $blueprint->index(['due_date', 'kanban_number']);
            }

            if (!isIndexExistsInTable($table, 'mst_firm_orders_due_date_instruction_number_index', true)) {
                $blueprint->index(['due_date', 'instruction_number']);
            }

            $this->createTableIndexes($blueprint, [
                'part_number', 'delivery_destination_code', 'plant', 'acceptance',
                'delivery_no', 'classification', 'kanban_number', 'instruction_number',
            ]);
        });

        Schema::table('shipment_records', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'kanban_no', 'instruction_no',
            ]);
        });

        Schema::table('supply_material_orders', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'material_number', 'instruction_date', 'instruction_number',
                'supplier_code', 'instruction_no', 'management_no',
            ]);
        });

        Schema::table('supply_material_arrivals', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'material_no', 'arrival_day', 'arrival_quantity', 'processing_rate', 'product_number',
                'flight_no', 'delivery_no', 'supplier_code', 'department_code', 'line_code', 'voucher_class',
                'material_manufacturer_code',
            ]);
        });

        Schema::table('sales_plans', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'part_number', 'year_month',
            ]);
        });

        Schema::table('supply_material_groups', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'part_number', 'supply_material_group',
            ]);
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'unit_price', 'part_number',
            ]);
        });

        Schema::table('process_unit_prices', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'processing_unit_price', 'part_number',
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
        Schema::table('daily_production_control', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'part_number', 'line_id', 'year', 'month',
            ]);
        });

        Schema::table('unofficial_notices', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'product_number', 'delivery_destination_code',
            ]);
        });

        Schema::table('product_numbers', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'department_code', 'line_code', 'supplier_code', 'material_manufacturer_code',
                'instruction_class', 'product_category',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'delete_flag',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'customer_flag', 'supplier_tag', 'delete_flag',
            ]);
        });

        Schema::table('processes', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'process_code', 'delete_flag',
            ]);
        });

        Schema::table('firm_orders', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'mst_firm_orders_due_date_kanban_number_index',
                'mst_firm_orders_due_date_instruction_number_index',
            ], true);

            $this->dropTableIndexes($table, [
                'part_number', 'delivery_destination_code', 'plant', 'acceptance',
                'delivery_no', 'classification', 'kanban_number', 'instruction_number',
            ]);
        });

        Schema::table('shipment_records', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'kanban_no', 'instruction_no',
            ]);
        });

        Schema::table('supply_material_orders', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'material_number', 'instruction_date', 'instruction_number',
                'supplier_code', 'instruction_no', 'management_no',
            ]);
        });

        Schema::table('supply_material_arrivals', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'material_no', 'arrival_day', 'arrival_quantity', 'processing_rate', 'product_number',
                'flight_no', 'delivery_no', 'supplier_code', 'department_code', 'line_code', 'voucher_class',
                'material_manufacturer_code',
            ]);
        });

        Schema::table('sales_plans', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'part_number', 'year_month',
            ]);
        });

        Schema::table('supply_material_groups', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'part_number', 'supply_material_group',
            ]);
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'unit_price', 'part_number',
            ]);
        });

        Schema::table('process_unit_prices', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'processing_unit_price', 'part_number',
            ]);
        });
    }
};
