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
        Schema::table('machine_numbers', function (Blueprint $table) {
            $table->dropColumn('project_name');
            $table->string('project_number', 10)->after('machine_number_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_numbers', function (Blueprint $table) {
            $table->dropColumn('project_number');
            $table->string('project_name')->after('machine_number_name')->nullable();
        });
    }
};
