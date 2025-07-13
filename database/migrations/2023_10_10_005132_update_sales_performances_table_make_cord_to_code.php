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
        Schema::table('sales_performances', function (Blueprint $table) {
            $table->renameColumn('line_cord', 'line_code');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_performances', function (Blueprint $table) {
            $table->renameColumn('line_cord', 'line_code');
            //
        });
    }
};
