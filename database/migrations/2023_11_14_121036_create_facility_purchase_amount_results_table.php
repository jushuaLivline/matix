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
        Schema::create('facility_purchase_amount_results', function (Blueprint $table) {
            $table->id();
            $table->string('year_and_month');
            $table->string('machine_number');
            $table->string('branch_number');
            $table->string('expense_item');
            $table->string('subsidy_items');
            $table->string('data_partition');
            $table->decimal('purchase_amount', 10, 2);
            $table->timestamp('creation_date')->useCurrent();
            $table->string('creator_code');
            $table->timestamp('updated_at')->nullable();
            $table->string('updater_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facility_purchase_amount_results');
    }
};
