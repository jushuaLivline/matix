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
        // Schema::table('estimates', function (Blueprint $table) {
        //     $table->string('product_name')->nullable()->after('part_number');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // if (Schema::hasColumn('estimates', 'product_name')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropColumn('product_name');
        //     });
        // }
    }
};
