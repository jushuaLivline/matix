<?php

namespace App\Imports;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TableSeedingImport implements ToCollection, WithChunkReading, ShouldQueue
{
    
    protected $tableName;

    protected $excludeInNaming = [".txt", "dbo_", '.csv', '`.xlsx`'];

    public function chunkSize(): int
    {
        return 1000;
    }

    function __construct($table){
        $this->tableName = $table;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach($collection as $value){
            $data = [];
            $counter = 0;

            if(!$value[0]){
                break;
            }

            try {
                foreach($this->tableColumns() as $column => $type) {
                    $column = str_replace("`", "", $column);
                    
                    if($type == "datetime"){
                        $insertData = $this->toDateFormat($value[$counter]);
                    } else if ($type == "bigint") {
                        $insertData = $value[$counter] == "NULL" ? null : (int) $value[$counter];
                    } else if ($column == "delete_flag"){
                        if(!$value[$counter]){
                            $insertData = 0;
                        }

                        $insertData = $value[$counter];
                    } else {
                        $insertData = $value[$counter] == "NULL" ? null : $value[$counter];
                    }
                    $data[$column] = $insertData;
                    $counter++;
                }

                // Do the insert
                DB::table($this->excludePrefix())->insert($data);
            } catch (Exception $e) {
                throw new Exception($this->tableName . ":" . $e->getMessage());
            }
        }
    }
    

    function toDateFormat($value){
        try {
            if($value != "NULL" && $value){
                $parse = Carbon::parse($value)->timestamp;
                
                return Date::excelToDateTimeObject(
                    Date::PHPToExcel($parse)
                );
            }
    
            return null;
        } catch (Exception $ex){
            throw new Exception("Error in Date Formating: " . $ex->getMessage());
        }
        
    }

    function excludePrefix(){
        $prefix = config("database.connections.mysql.prefix");
        return str_replace($prefix, "", $this->tableName);
    }


    function tableColumns(){
        $tableName = $this->tableName;
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
        return $data;
    }

}
