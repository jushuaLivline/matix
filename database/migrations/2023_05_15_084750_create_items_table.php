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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string("expense_item");
            $table->string("subsidy_items")->nullable();
            $table->string("item_name")->nullable();
            $table->string("acount")->nullable();
            $table->string("acount_name")->nullable();
            $table->string("supplementary_subjects")->nullable();
            $table->string("auxiliary_course_name")->nullable();
            $table->boolean("delete_flag")->default(0);
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
        Schema::dropIfExists('items');
    }
};
