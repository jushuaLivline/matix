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
        Schema::dropIfExists('facility_work_details');
        Schema::create('facility_work_details', function (Blueprint $table) {
            $table->id();
            $table->date('working_day');
            $table->string('employee_code');
            $table->string('serial_number')->nullable();
            $table->string('classification_category');
            $table->string('project_number')->nullable();
            $table->string('line_code')->nullable();
            $table->string('machine_number')->nullable();
            $table->string('branch_number')->nullable();
            $table->string('working_code');
            $table->decimal('working_hours', 8, 2);
            $table->text('remarks')->nullable();
            $table->string('creator_code')->nullable();
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
        Schema::table('facility_work_details', function (Blueprint $table) {
            
        });
    }
};
