<?php

namespace App\Console\Commands;

use App\Jobs\SeedJsonToTableJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class SeedFromJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:seed-from-json';

     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed all data from txt files';


    protected $excludeInNaming = [".txt", "dbo_", '.csv', '.json', 'split-texts'];


    protected $storageFiles;

    protected $table;

    protected $checkedTables = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $files = collect(Storage::disk('database-json-files')->allFiles());

        $this->storageFiles = $files->filter(function($q){
            if($q != ".DS_Store"){
                return $q;
            }
        });
    
        if($this->checkSeedingData()){
            $this->processSeedingData();
        }
    }

    function processSeedingData(){
        $this->newLine();

        $fileCount = count($this->storageFiles);
        $this->line("Seeding " . $fileCount . " entries/files");
        try {

            // DB::beginTransaction();
            $this->line("Putting processes on queue");
            $bar = $this->output->createProgressBar($fileCount);
            
            foreach ($this->storageFiles as $file) {
                SeedJsonToTableJob::dispatch($file);
                $bar->advance();
            }

            // DB::commit();
            $bar->finish();
            return Command::SUCCESS;

        } catch ( Exception $exception ) {
            // DB::rollBack();
            $this->error($exception->getMessage());
            return Command::SUCCESS;
        }
        
    }

    function checkSeedingData(){
        $hasIssue = false;
        $log = [];
        $this->newLine();
        $this->line("Checking seeding data for existing table and columns");
        $this->newLine();

        foreach ($this->storageFiles as $file) {
            $this->line("Checking: " . $this->sanitizeTxtFile($file));
            if(!in_array($this->sanitizeTxtFile($file), $this->checkedTables)){
                return true;
            }
            if ( ! $this->checkTableExists($file)) {
                $hasIssue = true;
                $log[] = $this->sanitizeTxtFile($file) . " table is not exists";
                $this->error("ERROR: " . $this->sanitizeTxtFile($file));
            }else {
                $this->info("OK: " . $this->sanitizeTxtFile($file));
            }

            array_push($this->checkedTables, $this->sanitizeTxtFile($file));
        }

        if($hasIssue){
            $this->newLine();
            $this->line("Summary");
            foreach($log as $log){
                $this->error("Error: ". $log);
            }
            $this->newLine();
            $this->line("Please review and try again.");
            return false;
        }
        
        return true;
    }



    function checkTableExists($table){
        $tableName = $this->sanitizeTxtFile($table, true);
        return Schema::hasTable($tableName);
    }

    function checkTableColumnCounts($table)
    {
        return count($this->getTableColumns($table));
    }

    function getTableColumns($table){
        $tableName = $this->tableName($table);
        
        $columns = collect(
            Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableColumns($tableName)
          );
          
        $data = [];
        foreach($columns as $column => $value){
            array_push($data, $column);
        }
        
        array_shift($data); // to remove the id
        return $data;
    }

    function sanitizeTxtFile($file, $excludePrefix = false){

        if($excludePrefix){
            $prefix = config("database.connections.mysql.prefix");
            if($prefix){
                array_push($this->excludeInNaming, $prefix);
            }
        }

        for($i = 0; $i < 4000; $i++){
            array_push($this->excludeInNaming, "_". str_pad($i, 5, '0', STR_PAD_LEFT));
        }
        
        return str_replace($this->excludeInNaming, "", $file);
    }
    function tableName($file){
        $prefix = config("database.connections.mysql.prefix");        
        return $prefix . $this->sanitizeTxtFile($file, true);
    }
}
