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
        Schema::create('system_setting_masters', function (Blueprint $table) {
            $table->id();
            $table->string("setting_category", 20)->nullable();
            $table->string("setting_id", 20)->nullable();
            $table->string("number_1")->nullable();
            $table->string("number_2")->nullable();
            $table->string("number_3")->nullable();
            $table->string("number_4")->nullable();
            $table->string("string_1", 500)->nullable();
            $table->string("string_2", 500)->nullable();
            $table->string("string_3", 500)->nullable();
            $table->string("string_4", 500)->nullable();
            $table->string("remarks", 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
