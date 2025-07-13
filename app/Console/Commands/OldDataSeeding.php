<?php

namespace App\Console\Commands;

use App\Imports\TableSeedingImport;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OldDataSeeding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-old-data {file?} {truncate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed all data from excel files';


    protected $excludeInNaming = [".txt", "dbo_", '.csv', '.xlsx'];


    protected $storageFiles;

    protected $table;

    
    protected $checkedTable = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $files = collect(Storage::disk('seeding-files')->allFiles());

        if($this->argument("file")){
            $argrumentFile = $this->argument("file");
            $exists = Storage::disk('seeding-files')->exists($argrumentFile);
            if($exists){
                $files = collect([$this->argument("file")]);       
            }else{
                $this->error('File provided is not exists');
                return Command::SUCCESS;
            }
        }
        
        if($this->argument("truncate")) {

            try{
                $tableData = $this->argument("truncate")::all();

                foreach ($tableData as $tdata) {
                    $tdata->delete();
                }
            } catch (Exception $ex){
                $this->error('Error in truncating ' . $this->argument("truncate"). " Error:" . $ex->getMessage());
                return Command::SUCCESS;
            }
        }

        for($i = 0; $i < 4000; $i++){
            array_push($this->excludeInNaming, "_". str_pad($i, 3, '0', STR_PAD_LEFT));
        }

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

            DB::beginTransaction();
            $bar = $this->output->createProgressBar($fileCount);
            
            foreach ($this->storageFiles as $file) {

                $tableName = $this->tableName($file);
                $file = Storage::disk('seeding-files')->path($file);
                Excel::import(
                    new TableSeedingImport($tableName), 
                    $file
                    // Storage::disk('seeding-files')->path($file)
                );

                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            return Command::SUCCESS;

        } catch ( Exception $exception ) {
            DB::rollBack();
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
            if(in_array($this->sanitizeTxtFile($file), $this->checkedTable)){
                continue;
            }

            $this->line("Checking: " . $this->sanitizeTxtFile($file));
            if ( ! $this->checkTableExists($file)) {
                $hasIssue = true;
                $log[] = $this->sanitizeTxtFile($file) . " table is not exists";
                $this->error("ERROR: " . $this->sanitizeTxtFile($file));
            }else {
                $this->info("OK: " . $this->sanitizeTxtFile($file));
            }

            array_push($this->checkedTable, $this->sanitizeTxtFile($file));
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

        
        return str_replace($this->excludeInNaming, "", $file);
    }
    function tableName($file){
        $prefix = config("database.connections.mysql.prefix");        
        return $prefix . $this->sanitizeTxtFile($file, true);
    }
}
