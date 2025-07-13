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
        Schema::table('sales_plans', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'customer_code', 'amount_category', 'department_code', 'line_code',
            ]);
        });

        Schema::table('sales_actuals', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'customer_code', 'year_month', 'amount_category',
                'department_code', 'line_code',
            ]);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'expense_item', 'amount_of_money', 'date',
            ]);
        });

        Schema::table('items', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'expense_item', 'delete_flag',
            ]);
        });

        Schema::table('product_numbers', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'product_name', 'customer_code', 'production_division',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'customer_name',
            ]);
        });

        Schema::table('processes', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'process_name', 'inside_and_outside_division', 'created_at',
            ]);
        });

        Schema::table('projects', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'project_name', 'delete_flag', 'project_number',
            ]);
        });

        Schema::table('kanban_masters', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'part_number', 'kanban_classification', 'delete_flag',
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'employee_code', 'employee_name', 'authorization_code',
                'department_code', 'delete_flag',
            ]);
        });

        Schema::table('machine_numbers', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'machine_number_name', 'project_number', 'line_name', 'machine_division',
                'remarks', 'completion_date', 'machine_number', 'branch_number', 'delete_flag',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'name', 'name_abbreviation', 'department_name', 'section_name', 'group_name',
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
        Schema::table('sales_plans', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'customer_code', 'amount_category', 'department_code', 'line_code',
            ]);
        });

        Schema::table('sales_actuals', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'customer_code', 'year_month', 'amount_category',
                'department_code', 'line_code',
            ]);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'expense_item', 'amount_of_money', 'date',
            ]);
        });

        Schema::table('items', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'expense_item', 'delete_flag',
            ]);
        });

        Schema::table('product_numbers', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'product_name', 'customer_code', 'production_division',
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'customer_name',
            ]);
        });

        Schema::table('processes', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'process_name', 'inside_and_outside_division', 'created_at',
            ]);
        });

        Schema::table('projects', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'project_name', 'delete_flag', 'project_number',
            ]);
        });

        Schema::table('kanban_masters', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'part_number', 'kanban_classification', 'delete_flag',
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'employee_code', 'employee_name', 'authorization_code',
                'department_code', 'delete_flag',
            ]);
        });

        Schema::table('machine_numbers', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'machine_number_name', 'project_number', 'line_name', 'machine_division',
                'remarks', 'completion_date', 'machine_number', 'branch_number', 'delete_flag',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'name', 'name_abbreviation', 'department_name', 'section_name', 'group_name',
            ]);
        });
    }
};
