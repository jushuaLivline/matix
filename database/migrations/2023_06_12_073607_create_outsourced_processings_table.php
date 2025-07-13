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
        Schema::create('outsourced_processings', function (Blueprint $table) {
            $table->id();
            $table->integer('order_no')->nullable();
            $table->string('management_no')->nullable();
            $table->integer('branch_number')->nullable();
            $table->string('product_code')->nullable();
            $table->string('supplier_process_code')->nullable();
            $table->string('order_classification')->default(1);
            $table->date('instruction_date')->nullable();
            $table->integer('instruction_number')->nullable();
            $table->string('lot')->nullable();
            $table->integer('instruction_kanban_quantity')->nullable();
            $table->integer('arrival_number')->nullable();
            $table->date('arrival_day')->nullable();
            $table->string('incoming_flight_number')->nullable();
            $table->integer('arrival_quantity')->nullable();
            $table->date('document_issue_date')->nullable();
            $table->string('supplier_code')->nullable();
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
        Schema::dropIfExists('outsourced_processings');
    }
};
