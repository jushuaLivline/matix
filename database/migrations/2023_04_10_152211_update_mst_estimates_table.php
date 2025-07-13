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
        // if (Schema::hasColumn('estimates', 'estimate_status_id')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropConstrainedForeignId('estimate_status_id');
        //     });
        // }
        // Schema::table('estimates', function (Blueprint $table) {
        //     $table->foreignId('contact_id')->constrained('contacts');
        //     $table->dropColumn([
        //         'reply_dt', 'part_code', 'production_volume',
        //         'mass_production_dt'
        //     ]);
        //     $table->dateTime('reply_due_date_dt');
        //     $table->string('part_number', 45);
        //     $table->integer('standard_number_per_month');
        //     $table->dateTime('sop_date_dt');
        //     $table->string('base_part_number', 45)->nullable();
        //     $table->foreignId('last_reply_status_id')->constrained('estimate_status');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // if (Schema::hasColumn('estimates', 'contact_id')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropConstrainedForeignId('contact_id');
        //     });
        // }
        // if (Schema::hasColumn('estimates', 'reply_due_date_dt')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropColumn('reply_due_date_dt');
        //     });
        // }
        // if (Schema::hasColumn('estimates', 'part_number')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropColumn('part_number');
        //     });
        // }
        // if (Schema::hasColumn('estimates', 'standard_number_per_month')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropColumn('standard_number_per_month');
        //     });
        // }
        // if (Schema::hasColumn('estimates', 'sop_date_dt')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropColumn('sop_date_dt');
        //     });
        // }
        // if (Schema::hasColumn('estimates', 'base_part_number')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropColumn('base_part_number');
        //     });
        // }
        // if (Schema::hasColumn('estimates', 'last_reply_status_id')) {
        //     Schema::table('estimates', function (Blueprint $table) {
        //         $table->dropConstrainedForeignId('last_reply_status_id');
        //     });
        // }
        // Schema::table('estimates', function (Blueprint $table) {
        //     $table->dateTime('reply_dt');
        //     $table->string('part_code', 45);
        //     $table->integer('production_volume');
        //     $table->dateTime('mass_production_dt');
        //     $table->foreignId('estimate_status_id')->constrained('estimate_status');
        // });
    }
};
