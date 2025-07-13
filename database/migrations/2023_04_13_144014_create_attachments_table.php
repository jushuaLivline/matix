<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('extension');
            $table->integer("sort_no")->default(1);
            $table->integer('attachable_id');
            $table->string('attachable_type', 45);
            $table->timestamps();
        });


        $table = config("database.connections.mysql.prefix") . "attachments";
        DB::statement("ALTER TABLE $table ADD file MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
};
