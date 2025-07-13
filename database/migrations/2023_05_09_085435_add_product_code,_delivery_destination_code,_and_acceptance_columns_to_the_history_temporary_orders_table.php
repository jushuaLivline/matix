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
        Schema::table('history_temporary_orders', function (Blueprint $table) {
            $table->string('product_code')->nullable()->after('customer_code');
            $table->string('delivery_destination_code')->nullable()->after('product_code');
            $table->string('acceptance')->default('all')->after('delivery_destination_code');
            $table->string('department_code')->nullable()->after('acceptance');
            $table->string('line_code')->nullable()->after('department_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_temporary_orders', function (Blueprint $table) {
            $table->dropColumn(['product_code', 'delivery_destination_code', 'acceptance']);
        });
    }
};
