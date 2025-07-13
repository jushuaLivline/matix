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
        Schema::create('estimate_reply_quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("estimate_reply_detail_id");
            $table->double("amount_per_month");
            $table->unsignedBigInteger("created_user");
            $table->unsignedBigInteger("updated_user");
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
        Schema::dropIfExists('estimate_reply_quotations');
    }
};
