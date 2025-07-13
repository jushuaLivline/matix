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
        if (! Schema::hasColumn('daily_production_control', 'line_id'))
        {
            Schema::table('daily_production_control', function (Blueprint $table)
            {
                $table->bigInteger('line_id')->unsigned()->nullable()->after('mst_line_id');
            });
        }
        if (! Schema::hasColumn('facility_lines', 'line_id'))
        {
            Schema::table('facility_lines', function (Blueprint $table)
            {
                $table->bigInteger('line_id')->unsigned()->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 
    }
};
