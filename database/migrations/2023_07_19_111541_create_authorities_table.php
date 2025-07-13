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
        Schema::create('authorities', function (Blueprint $table) {
            $table->id();
            $table->string('authorization_code', 6);
            $table->string('authority_name', 20);
            $table->tinyInteger('delete_flag')->default(0);
            $table->dateTime('creation_date');
            $table->string('creator_code', 10);
            $table->dateTime('updated_date')->nullable();
            $table->string('updater_code', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authorities');
    }
};
