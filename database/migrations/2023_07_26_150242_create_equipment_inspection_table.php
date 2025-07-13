<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try {
            if (!Schema::hasTable('basic_inspection_item_set')) {
                Schema::create('basic_inspection_item_set', function (Blueprint $table) {
                    $table->id();
                    // $table->increments('id');
                    $table->string('inspection_item_set')->nullable();
                    $table->longText('json_data')->nullable();
                    $table->bigInteger('created_by')->unsigned()->nullable();
                    $table->bigInteger('updated_by')->unsigned()->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
            if (!Schema::hasTable('facility_lines')) {
                Schema::create('facility_lines', function (Blueprint $table) {
                    $table->id();
                    // $table->increments('id');
                    $table->bigInteger('line_id')->unsigned()->nullable();
                    $table->string('line_code')->nullable();
                    $table->string('line_name')->nullable();
                    $table->longText('json_data')->nullable();
                    $table->bigInteger('created_by')->unsigned()->nullable();
                    $table->bigInteger('updated_by')->unsigned()->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
            if (!Schema::hasTable('facility_machines')) {
                Schema::create('facility_machines', function (Blueprint $table) {
                    $table->id();
                    // $table->increments('id');
                    $table->string('machine_number')->nullable();
                    $table->string('machine_name')->nullable();
                    $table->integer('number_of_maintenance')->nullable();
                    $table->longText('json_data')->nullable();
                    $table->bigInteger('created_by')->unsigned()->nullable();
                    $table->bigInteger('updated_by')->unsigned()->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                    // $table->foreign('machine_number_id')->references('id')->on('machine_numbers')->onUpdate('cascade');
                });
            }

            if (!Schema::hasTable('equipment_inspection')) {
                Schema::create('equipment_inspection', function (Blueprint $table) {
                    $table->id();
                    // $table->increments('id');
                    $table->bigInteger('mst_department_id')->unsigned()->nullable();
                    $table->bigInteger('inspection_line_id')->unsigned()->nullable();
                    $table->text('line_image')->nullable();
                    $table->integer('year')->unsigned()->nullable();
                    $table->integer('month')->unsigned()->nullable();
                    $table->bigInteger('mst_basic_id')->unsigned()->nullable();
                    $table->bigInteger('process_id')->unsigned()->nullable();
                    $table->longText('json_data')->nullable();
                    $table->bigInteger('created_by')->unsigned()->nullable();
                    $table->bigInteger('updated_by')->unsigned()->nullable();
                    $table->bigInteger('confirmed_by')->unsigned()->nullable();
                    $table->bigInteger('approved_by')->unsigned()->nullable();
                    $table->bigInteger('completed_by')->unsigned()->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                    // $table->foreign('mst_department_id')->references('id')->on('departments')->onUpdate('cascade');
                    $table->foreign('inspection_line_id')->references('id')->on('facility_lines')->onUpdate('cascade');
                    $table->foreign('mst_basic_id')->references('id')->on('basic_inspection_item_set')
                        ->onUpdate('cascade');
                    // $table->foreign('created_by')->references('id')->on('employees')->onUpdate('cascade');
                    // $table->foreign('confirmed_by')->references('id')->on('employees')->onUpdate('cascade');
                    // $table->foreign('approved_by')->references('id')->on('employees')->onUpdate('cascade');
                    // $table->foreign('completed_by')->references('id')->on('employees')->onUpdate('cascade');
                });
            }

            if (!Schema::hasTable('daily_production_control')) {
                Schema::create('daily_production_control', function (Blueprint $table) {
                    $table->id();
                    // $table->increments('id');
                    $table->bigInteger('mst_department_id')->unsigned()->nullable();
                    $table->bigInteger('mst_line_id')->unsigned()->nullable();
                    $table->bigInteger('equipment_inspection_id')->unsigned()->nullable();
                    $table->integer('year')->unsigned()->nullable();
                    $table->integer('month')->unsigned()->nullable();
                    $table->integer('ct_input')->unsigned()->nullable();
                    $table->longText('json_data')->nullable();
                    $table->bigInteger('created_by')->unsigned()->nullable();
                    $table->bigInteger('updated_by')->unsigned()->nullable();
                    $table->softDeletes();
                    $table->timestamps();
                    // $table->foreign('mst_department_id')->references('id')->on('departments')->onUpdate('cascade');
                    $table->foreign('mst_line_id')->references('id')->on('facility_lines')->onUpdate('cascade');
                    // $table->foreign('created_by')->references('id')->on('employees')->onUpdate('cascade');
                    $table->foreign('equipment_inspection_id')->references('id')->on('equipment_inspection')
                        ->onUpdate('cascade');
                });
            }
            DB::commit();
            print_r('create facility_machines'. PHP_EOL);
            print_r('create daily_production_control'. PHP_EOL);
            print_r('create equipment_inspection'. PHP_EOL);
            print_r('create basic_inspection_item_set'. PHP_EOL);
            print_r('create facility_lines'. PHP_EOL);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facility_machines');
        Schema::dropIfExists('daily_production_control');
        Schema::dropIfExists('equipment_inspection');
        Schema::dropIfExists('basic_inspection_item_set');
        Schema::dropIfExists('facility_lines');
    }
};
