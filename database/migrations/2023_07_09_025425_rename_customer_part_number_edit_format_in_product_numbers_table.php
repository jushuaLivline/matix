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
        Schema::table('product_numbers', function (Blueprint $table) {
            $table->dropColumn('customer_part number_edit_format');
            $table->string('customer_part_number_edit_format')->nullable()->after('customer_part_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_numbers', function (Blueprint $table) {
            //
        });
    }
};
