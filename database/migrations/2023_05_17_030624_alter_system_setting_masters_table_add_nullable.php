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
        Schema::table('system_setting_masters', function (Blueprint $table) {
            $table->integer("number_1")->nullable()->change();
            $table->integer("number_2")->nullable()->change();
            $table->integer("number_3")->nullable()->change();
            $table->integer("number_4")->nullable()->change();
            $table->string("string_1", 500)->nullable()->change();
            $table->string("string_2", 500)->nullable()->change();
            $table->string("string_3", 500)->nullable()->change();
            $table->string("string_4", 500)->nullable()->change();
            $table->string("remarks", 1000)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_setting_masters', function (Blueprint $table) {
            $table->integer("number_1")->nullable(false)->change();
            $table->integer("number_2")->nullable(false)->change();
            $table->integer("number_3")->nullable(false)->change();
            $table->integer("number_4")->nullable(false)->change();
            $table->string("string_1", 500)->nullable(false)->change();
            $table->string("string_2", 500)->nullable(false)->change();
            $table->string("string_3", 500)->nullable(false)->change();
            $table->string("string_4", 500)->nullable(false)->change();
            $table->string("remarks", 1000)->nullable(false)->change();
        });
    }
};
