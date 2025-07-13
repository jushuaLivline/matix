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
        Schema::dropIfExists('facility_work_results');
        Schema::create('facility_work_results', function (Blueprint $table) {
            $table->id();
            $table->date('working_day');
            $table->string('employee_code')->nullable();
            $table->string('department_code')->nullable();
            $table->string('work_division');
            $table->decimal('working_hours', 8, 2);
            $table->decimal('overtime_hours', 8, 2);
            $table->text('remarks')->nullable();
            $table->string('creator_code');
            $table->string('updater_code')->nullable();
            $table->timestamps();

        });
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
