<?php

namespace App\Console\Commands;

use App\Models\DailyProductionControl;
use App\Models\Line;
use Illuminate\Console\Command;

class ClearTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:table {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tableName = $this->argument('table');

        if (!empty($tableName)) {
            \DB::table($tableName)->delete();
            $this->info("All records from the '$tableName' table have been deleted.");
        } else {
            $this->error('Table name is required.');
        }
    }
}
