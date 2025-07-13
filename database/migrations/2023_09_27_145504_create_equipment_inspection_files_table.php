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
        Schema::create('equipment_inspection_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('extension');
            $table->string('size');
            $table->integer('user_id');
            $table->string('form');
            $table->integer('file_id');
            $table->string('file_type', 100);
            $table->timestamps();
        });

        $table = config("database.connections.mysql.prefix") . "equipment_inspection_files";
        \DB::statement("ALTER TABLE $table ADD file MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_inspection_files');
    }
};
