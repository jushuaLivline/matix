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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('year_and_month')->nullable();
            $table->string('supplier_code')->nullable();
            $table->decimal('tax_exempt_amount', 10, 2)->nullable();
            $table->decimal('taxable_amount', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('offset_amount', 10, 2)->nullable();
            $table->decimal('total_to_pay', 10, 2)->nullable();
            $table->decimal('bill_amount', 10, 2)->nullable();
            $table->decimal('transfer_amount', 10, 2)->nullable();
            $table->decimal('transfer_fee', 10, 2)->nullable();
            $table->dateTime("created_at")->useCurrent();
            $table->unsignedBigInteger("creator_code")->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger("updator_code")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
