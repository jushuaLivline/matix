<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabaseContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:check-database-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the database connection
        $connection = DB::connection();

        // Get the table names
        $tableNames = $connection->getDoctrineSchemaManager()->listTableNames();

        $output = [];
        // Iterate over the table names
        foreach ($tableNames as $tableName) {
            // Count the records in each table
            $tableName = str_replace(config("database.connections.mysql.prefix"), "" ,$tableName);
            $count = $connection->table($tableName)->count();

            // Output the table name and count
            // $this->line( "Table: $tableName, Count: $count" );
            array_push($output, [$tableName, number_format($count)]);
        }

        $this->table(
            ['Table Name', 'Row Count'],
            $output
        );

        return Command::SUCCESS;
    }
}
