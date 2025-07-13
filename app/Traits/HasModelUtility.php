<?php
namespace App\Traits;

trait HasModelUtility{
    static function tableName(){
        return (new self)->getTable(); 
    }

}