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
        Schema::create('temporary_uploads', function (Blueprint $table) {
            $table->id();
            $table->string("file_name");
            $table->text("form")->comment("to what form it is from");
            $table->text("user_id");
            $table->timestamps();
        });

        $table = config("database.connections.mysql.prefix") . "temporary_uploads";
        DB::statement("ALTER TABLE $table ADD file MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_uploads');
    }
};
