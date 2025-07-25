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
        Schema::create('facility_work_results', function (Blueprint $table) {
            $table->id();
            $table->date('working_day');
            $table->string('employee_code');
            $table->string('department_code');
            $table->string('work_division');
            $table->decimal('working_hours', 8, 2);
            $table->decimal('overtime_hours', 8, 2);
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('facility_work_results');
    }
};
