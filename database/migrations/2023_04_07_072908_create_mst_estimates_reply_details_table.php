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
        Schema::create('estimate_reply_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('reply_estimate_d')->nullable();
            $table->string('reply_message')->nullable();
            $table->boolean('decline_flag')->default(0);
            $table->boolean('delete_flag')->default(0);
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
        Schema::dropIfExists('estimate_reply_details');
    }
};
