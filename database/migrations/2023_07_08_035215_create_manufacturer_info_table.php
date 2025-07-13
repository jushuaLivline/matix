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
        Schema::create('manufacturer_infos', function (Blueprint $table) {
            $table->id();
            $table->string('material_manufacturer_code', 4);
            $table->string('person_in_charge', 200);
            $table->dateTime("created_at")->useCurrent();
            $table->unsignedBigInteger("creator_code")->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger("updator_code")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacturer_info');
    }
};
