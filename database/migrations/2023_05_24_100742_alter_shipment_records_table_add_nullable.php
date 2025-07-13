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

    public function __construct()
    {
        
    }

    public function up()
    {
        Schema::table('shipment_records', function (Blueprint $table) {
            $table->dropColumn('serial_number');
            $table->dropColumn('classification');
        });
        
        Schema::table('shipment_records', function (Blueprint $table) {
            $table->string('shipment_no')->nullable()->change();
            $table->tinyInteger('serial_number')->nullable();
            $table->string('slip_no')->nullable()->change();
            $table->string('voucher_class')->nullable()->change();
            $table->string('customer_code')->nullable()->change();
            $table->dateTime('due_date')->nullable()->change();
            $table->string('delivery_no')->nullable()->change();
            $table->string('acceptance')->nullable()->change();
            $table->string('drop_ship_code')->nullable()->change();
            $table->string('part_no')->nullable()->change();
            $table->string('line_code')->nullable()->change();
            $table->string('department_code')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->integer('unit_price')->nullable()->change();
            $table->string('remarks')->nullable()->nullable()->change();
            $table->string('plant')->nullable()->change();
            $table->dateTime('closing_date')->nullable()->change();
            $table->string('ai_slip_type')->nullable()->change();
            $table->enum('classification', ['kanban', 'instruction'])->nullable();
            $table->string('uniform_no')->nullable()->change();
            $table->string('accomodation_no')->nullable()->change();
            $table->string('kanban_no')->nullable()->change();
            $table->string('instruction_no')->nullable()->change();
            $table->string('ai_delivery_no')->nullable()->change();
            $table->string('ai_jersey_no')->nullable()->change();
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
            $table->string('shipment_no')->nullable(false)->change();
            $table->tinyInteger('serial_number')->nullable(false)->change();
            $table->string('slip_no')->nullable(false)->change(false);
            $table->string('voucher_class')->nullable(false)->change();
            $table->string('customer_code')->nullable(false)->change();
            $table->dateTime('due_date')->nullable(false)->change();
            $table->string('delivery_no')->nullable(false)->change();
            $table->string('acceptance')->nullable(false)->change();
            $table->string('drop_ship_code')->nullable(false)->change();
            $table->string('part_no')->nullable(false)->change();
            $table->string('line_code')->nullable(false)->change();
            $table->string('department_code')->nullable(false)->change();
            $table->integer('quantity')->nullable(false)->change();
            $table->integer('unit_price')->nullable(false)->change();
            $table->string('remarks')->nullable(false)->change();
            $table->string('plant')->nullable(false)->change();
            $table->dateTime('closing_date')->nullable(false)->change();
            $table->string('ai_slip_type')->nullable(false)->change();
            $table->enum('classification', ['kanban', 'instruction'])->nullable(false)->change();
            $table->string('uniform_no')->nullable(false)->change();
            $table->string('accomodation_no')->nullable(false)->change();
            $table->string('kanban_no')->nullable(false)->change();
            $table->string('instruction_no')->nullable(false)->change();
            $table->string('ai_delivery_no')->nullable(false)->change();
            $table->string('ai_jersey_no')->nullable(false)->change();
        });
    }
};
