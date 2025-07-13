<?php

namespace App\Jobs;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SeedJsonToTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $excludeInNaming = [".txt", "dbo_", '.csv', '.json'];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $file)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    
        $jsonFile = Storage::disk("database-json-files")->get($this->file);
        $decodedJson = json_decode($jsonFile);
        $chunkItems = collect($decodedJson);

        foreach($chunkItems->chunk(150) as $items) {
            $data = [];
            foreach($items as $row){
                $index = 0;
                $rowData = [];
                foreach($this->tableColumns() as $column => $type) {
                    $column = str_replace("`", "", $column);
                    if($type == "datetime") {
                        $insertData = $this->toDateFormat($row[$index]);
                    } elseif ($type == "bigint") {
                        $insertData = $row[$index] == "NULL" ? null : (int) $row[$index];
                    } else {
                        $insertData = $row[$index] == "NULL" ? null : $row[$index];
                    }
                    $rowData[$column] = $insertData;
                    $index++;
                }
                $data[] = $rowData;
            }
            
            DB::table($this->excludePrefix())->insert($data);
        }

        Storage::disk("database-json-files")->delete($this->file);

    }

    /**
    * Calculate the number of seconds to wait before retrying the job.
    *
    * @return array
    */
    public function backoff()
    {
        return [5, 10, 15];
    }

    function toDateFormat($value){
        try {
            if($value != "NULL" && $value){
                return Carbon::parse($value)->format("Y-m-d H:i:s");
            }
    
            return now()->format("Y-m-d H:i:s");;
        } catch (Exception $ex){
            throw new Exception("Error in Date Formating: " . $ex->getMessage());
        }
        
    }

    
    function excludePrefix(){
        $prefix = config("database.connections.mysql.prefix");
        return str_replace($prefix, "", $this->tableName());
    }


    function sanitizeTxtFile($excludePrefix = false){
        // if(Cache::has($this->excludePrefix() . "-excludeInNaming")){
        //     return str_replace(
        //         Cache::get($this->excludePrefix() . "-excludeInNaming"), 
        //         "", 
        //         $this->file);

        // }

        if($excludePrefix){
            $prefix = config("database.connections.mysql.prefix");
            if($prefix){
                array_push($this->excludeInNaming, $prefix);
            }
        }

        $numbering = array_map(fn($value) => "_". str_pad($value, 5, '0', STR_PAD_LEFT), range(1,4000));

        $excludeInNaming = array_merge($this->excludeInNaming, $numbering);

        // Cache::remember($this->excludePrefix() . "-excludeInNaming", 120, function() use ($excludeInNaming) {
        //     return $excludeInNaming;
        // });
        
        return str_replace($excludeInNaming, "", $this->file);
    }




    function tableName(){
        $prefix = config("database.connections.mysql.prefix");        
        return $prefix . $this->sanitizeTxtFile(true);
    }

    function tableColumns(){
        if(Cache::has($this->excludePrefix())){
            return Cache::get($this->excludePrefix());
        }
        
        $tableName = $this->tableName();
        
        $columns = collect(
            Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableColumns($tableName)
          );
          
        $data = [];
        foreach($columns as $column => $value){
            $data[$column] = $value->getType()->getName();
        }
        
        array_shift($data); // to remove the id

        return Cache::remember($this->excludePrefix(), 30, function() use ($data) {
            return $data;
        });
        
    }
    
}
