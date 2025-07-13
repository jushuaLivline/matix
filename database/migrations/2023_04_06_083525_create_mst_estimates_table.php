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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->default(0);
            $table->string('customer_person', 30);
            $table->date('estimate_d');
            $table->date('answer_due_d');
            $table->string('base_product_code', 50);
            $table->string('product_code', 50);
            $table->string('product_name');
            $table->string('model_type');
            $table->integer('per_month_reference_amount');
            $table->dateTime('sop_d');
            $table->longText('message');
            $table->boolean('delete_flag')->default(0);
            $table->timestamps();

            $table->index("answer_due_d");
            $table->index("product_code");
            $table->index("product_name");
            $table->index("model_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimates');
    }
};
