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
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->string("division");
            $table->string("code", 10);
            $table->string("name", 40);
            $table->string("abbreviation", 20)->nullable();
            $table->string("spare_1", 40)->nullable();
            $table->string("spare_2", 40)->nullable();
            $table->string("spare_3", 40)->nullable();
            $table->string("spare_4", 40)->nullable();
            $table->boolean("delete_flag")->default(0);
            $table->dateTime('created_at')->default(now());
            $table->unsignedBigInteger("creator")->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger("updator")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codes');
    }
};
