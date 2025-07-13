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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->char('code',6)->nullable();
            $table->string('name',60)->nullable();
            $table->string('name_abbreviation',40)->nullable();
            $table->string('department_name',40)->nullable();
            $table->string('section_name',40)->nullable();
            $table->string('group_name',40)->nullable();
            $table->boolean('delete_flag')->nullable()->default(0);
            $table->dateTime('created_at')->default(now());
            $table->unsignedBigInteger('creator')->nullable()->comment("The user who is created");
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updator_code')->nullable()->comment("The user who recently updates");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('divisions');
    }
};
