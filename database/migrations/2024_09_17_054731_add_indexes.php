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
        Schema::table('shipment_records', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'product_no', 'line_code', 'department_code', 'due_date', 'delivery_no',
                'slip_no', 'delivery_destination_code', 'voucher_class',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'code',
            ]);
        });

        Schema::table('product_numbers', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'part_number', 'delete_flag',
            ]);
        });

        Schema::table('lines', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'delete_flag', 'line_code', 'department_code',
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
        Schema::table('shipment_records', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'product_no', 'line_code', 'department_code', 'due_date', 'delivery_no',
                'slip_no', 'delivery_destination_code', 'voucher_class',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'code',
            ]);
        });

        Schema::table('product_numbers', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'part_number', 'delete_flag',
            ]);
        });

        Schema::table('lines', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'delete_flag', 'line_code', 'department_code',
            ]);
        });
    }
};
