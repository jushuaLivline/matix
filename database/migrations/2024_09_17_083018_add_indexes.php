<?php

use App\Traits\CreateTableIndexes;
use App\Traits\DropTableIndexes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use CreateTableIndexes, DropTableIndexes;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'estimate_d', 'customer_id',
            ]);
        });

        Schema::table('estimate_reply_details', function (Blueprint $table) {
            $this->createTableIndexes($table, [
                'estimate_id', 'decline_flag',
            ]);
        });

        Schema::table('unofficial_notices', function (Blueprint $blueprint) {
            $table = $blueprint->getTable();

            if (!isIndexExistsInTable($table, 'mst_unofficial_notices_ymddcpn_idx', true)) {
                $blueprint->index([
                    'year_and_month',
                    'delivery_destination_code',
                    'product_number',
                ], 'mst_unofficial_notices_ymddcpn_idx');
            }

            if (!isIndexExistsInTable($table, 'mst_unofficial_notices_cmorfn_idx', true)) {
                $blueprint->index('current_month_order_rate_factored_number', 'mst_unofficial_notices_cmorfn_idx');
            }

            $this->createTableIndexes($blueprint, [
                'current_month', 'next_month', 'two_months_later', 'year_and_month',
                'instruction_class', 'acceptance',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'estimate_d', 'customer_id',
            ]);
        });

        Schema::table('estimate_reply_details', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'estimate_id', 'decline_flag',
            ]);
        });

        Schema::table('unofficial_notices', function (Blueprint $table) {
            $this->dropTableIndexes($table, [
                'mst_unofficial_notices_ymddcpn_idx',
                'mst_unofficial_notices_cmorfn_idx',
            ], true);

            $this->dropTableIndexes($table, [
                'current_month', 'next_month', 'two_months_later', 'year_and_month',
                'instruction_class', 'acceptance',
            ]);
        });
    }
};
